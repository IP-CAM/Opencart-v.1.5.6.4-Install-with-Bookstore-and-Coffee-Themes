<?php
class ModelMenuMenuitems extends Model {
	

	public function getMenuitem($menuitem_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "menuitems WHERE menu_item_id = '" . (int)$menuitem_id . "'");
		
		return $query->row;
	} 
	
	public function getMenuitems($parent_id = 0, $menu_id) {
			$menuitem_data = array();
		
			$query = $this->db->query("SELECT c.*, cd.menu_item_name FROM " . DB_PREFIX . "menuitems c LEFT JOIN " . DB_PREFIX . "menuitem_descriptions cd ON (c.menu_item_id = cd.menu_item_id) WHERE c.parent_id = '" . (int)$parent_id . "' AND c.menu_id='".(int)$menu_id."' AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND c.status=1 ORDER BY c.sort_order, cd.menu_item_name ASC");
			foreach ($query->rows as $result) {
				
				$menuitem_data[] = array(
					'menu_item_id' => $result['menu_item_id'],
					'menu_name'		=>$result['menu_item_name'],
					'menu_class'	=>$result['menu_item_class'],
					'menu_link'	=>$result['menu_item_link'],
					'children'      => $this->getMenuitems($result['menu_item_id'],$menu_id),
					'status'  	  => $result['status'],
					'sort_order'  => $result['sort_order']
				);
			}	
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