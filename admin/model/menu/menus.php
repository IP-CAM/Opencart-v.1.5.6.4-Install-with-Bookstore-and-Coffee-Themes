<?php
class ModelMenuMenus extends Model {
	
	public function addMenu($data) {
		$data['menu_class']=$data['menu_class']?$data['menu_class']:'menu';

		$this->db->query("INSERT INTO " . DB_PREFIX . "menus".
						" SET menu_name='".$this->db->escape($data['menu_name']).
						"',menu_description='".$this->db->escape($data['menu_description']).
						"',menu_class='".$this->db->escape($data['menu_class']).
						"', status = '" . (int)$data['status'] . "'");
		
		$this->cache->delete('menus');
		return $this->db->getLastId();
	}

	public function editMenu($menu_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "menus".
						" SET `menu_name` = '" . $this->db->escape($data['menu_name']) . 
						"',`menu_description` = '" . $this->db->escape($data['menu_description']) . 
						"',`menu_class` = '" . $this->db->escape($data['menu_class']) .
						"', status = '" . (int)$data['status'] . 
						"' WHERE menu_id = '" . (int)$menu_id . "'");
		$this->cache->delete('menus');
	}

	public function deleteMenu($menu_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "menus WHERE menu_id = '" . (int)$menu_id . "'");
		$this->cache->delete('menus');
	}

	public function getMenu($menu_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM ". DB_PREFIX . "menus WHERE menu_id = '" . (int)$menu_id . "'");

		return $query->row;
	}
	
	public function getMenuName($menu_id) {
		$query = $this->db->query("SELECT menu_name FROM ". DB_PREFIX . "menus WHERE menu_id = '" . (int)$menu_id . "'");
	
		return $query->row['menu_name'];
	}

	public function getMenus() {
			$menu_data = array();

			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "menus ORDER BY menu_id");

			foreach ($query->rows as $result) {
				$menu_data[] = array(
						'menu_id' => $result['menu_id'],
						'menu_name'        => $result['menu_name'],
						'menu_description'  => $result['menu_description'],
						'menu_class'  => $result['menu_class'],
						'status'  	  => $result['status']						
				);
			}

		return $menu_data;
	}
	

	public function getTotalMenus() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "menus");
		return $query->row['total'];
	}

}
?>