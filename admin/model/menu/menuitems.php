<?php
class ModelMenuMenuitems extends Model {
	public function addMenuitem($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "menuitems".
						" SET parent_id = '" . (int)$data['parent_id'] . "',".
						" menu_id = '" . (int)$data['menu_id']. "',".
						" menu_item_class = '" . $data['class'] . "',".
						" menu_item_link = '" . $data['link'] . "',".
						" sort_order = '" . (int)$data['sort_order'] . "',".
						" status = '" . (int)$data['status'] . "'");
	
		$menuitem_id = $this->db->getLastId();
		
		foreach ($data['menuitem_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "menuitem_descriptions".
							" SET menu_item_id = '" . (int)$menuitem_id . "',".
							" language_id = '" . (int)$language_id . "',".
							" menu_item_name = '" . $this->db->escape($value['name']) . "',".
							" menu_item_description = '" . $this->db->escape($value['description']) . "'");
		}
		
		$this->cache->delete('menuitems');
		$this->cache->delete('menuitems_front');
		return $menuitem_id;
	}
	
	public function editMenuitem($menuitem_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "menuitems".
						" SET parent_id = '" . (int)$data['parent_id'] . 
						"', menu_id = '" . (int)$data['menu_id'] .
						"', menu_item_class = '" . $data['class'] .
						"', menu_item_link = '" . $data['link'] .
						"', sort_order = '" . (int)$data['sort_order'] . 
						"', status = '" . (int)$data['status'] . 
						"' WHERE menu_item_id = '" . (int)$menuitem_id . "'");


		$this->db->query("DELETE FROM " . DB_PREFIX . "menuitem_descriptions WHERE menu_item_id = '" . (int)$menuitem_id . "'");

		foreach ($data['menuitem_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "menuitem_descriptions".
							" SET menu_item_id = '" . (int)$menuitem_id . "',".
							" language_id = '" . (int)$language_id . "',".
							" menu_item_name = '" . $this->db->escape($value['name']) . "',".
							" menu_item_description = '" . $this->db->escape($value['description']) . "'");
		}		
		$this->cache->delete('menuitems');
		$this->cache->delete('menuitems_front');
	}
	
	public function deleteMenuitem($menuitem_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "menuitems WHERE menu_item_id = '" . (int)$menuitem_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "menuitem_descriptions WHERE menu_item_id = '" . (int)$menuitem_id . "'");
		$query = $this->db->query("SELECT menu_item_id FROM " . DB_PREFIX . "menuitems WHERE parent_id = '" . (int)$menuitem_id . "'");

		foreach ($query->rows as $result) {
			$this->deleteMenuitem($result['menu_item_id']);
		}
		
		$this->cache->delete('menuitems');
		$this->cache->delete('menuitems_front');
	} 

	public function getMenuitem($menuitem_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "menuitems WHERE menu_item_id = '" . (int)$menuitem_id . "'");
		
		return $query->row;
	} 
	
	public function getMenuitems($parent_id = 0, $menu_id) {
		//$menuitem_data = $this->cache->get('menuitems.' . (int)$this->config->get('config_language_id') . '.' . (int)$menu_id);
		//if (!$menuitem_data) {
			$menuitem_data = array();
		
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "menuitems c LEFT JOIN " . DB_PREFIX . "menuitem_descriptions cd ON (c.menu_item_id = cd.menu_item_id) WHERE c.parent_id = '" . (int)$parent_id . "'AND c.menu_id='".(int)$menu_id."' AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY c.sort_order, cd.menu_item_name ASC");
			
			foreach ($query->rows as $result) {
				
				$menuitem_data[] = array(
					'menu_item_id' => $result['menu_item_id'],
					'name'        => $this->getPath($result['menu_item_id'], $this->config->get('config_language_id')),
					'status'  	  => $result['status'],
					'sort_order'  => $result['sort_order']
				);
			
				$menuitem_data = array_merge($menuitem_data, $this->getMenuitems($result['menu_item_id'],$menu_id));
			}	
	
			//$this->cache->set('menuitems.' . (int)$this->config->get('config_language_id') . '.' . (int)$menu_id, $menuitem_data);
		//}
		return $menuitem_data;
	}
		
public function getPath($menuitem_id, $language) {
		$query = $this->db->query("SELECT menu_item_name, parent_id FROM " . DB_PREFIX . "menuitems c LEFT JOIN " . DB_PREFIX . "menuitem_descriptions cd ON (c.menu_item_id = cd.menu_item_id) WHERE c.menu_item_id = '" . (int)$menuitem_id . "' AND cd.language_id = '" . (int)$language . "' ORDER BY c.sort_order, cd.menu_item_name ASC");
		
		if ($query->row['parent_id']) {
			return $this->getPath($query->row['parent_id'], $this->config->get('config_language_id')) . $this->language->get('text_separator') . $query->row['menu_item_name'];
		} else {
			return $query->row['menu_item_name'];
		}
	}
		
	public function getMenuDescriptions($menu_item_id) {
		$menu_description_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "menuitem_descriptions WHERE menu_item_id = '" . (int)$menu_item_id . "'");
		
		foreach ($query->rows as $result) {
			$menu_description_data[$result['language_id']] = array(
				'name'             => $result['menu_item_name'],
				'description'      => $result['menu_item_description']
			);
		}
		
		return $menu_description_data;
	}	
		
	public function getTotalMenuitems($menu_id) {
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "menuitems WHERE menu_id='".$menu_id."'");
		
		return $query->row['total'];
	}	
}
?>