<?php
class ModelMenuMenus extends Model {
	public function getMenu($menu_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "manufacturer m LEFT JOIN " . DB_PREFIX . "manufacturer_to_store m2s ON (m.manufacturer_id = m2s.manufacturer_id) WHERE m.manufacturer_id = '" . (int)$manufacturer_id . "' AND m2s.store_id = '" . (int)$this->config->get('config_store_id') . "'");
	
		return $query->row;	
	}
	public function getMenuClass($menu_id){
		$query=$this->db->query("SELECT menu_class FROM ". DB_PREFIX . "menus where menu_id='". (int)$menu_id ."'");
		//echo ("SELECT menu_class FROM ". DB_PREFIX . "menus where menu_id='". (int)$menu_id ."'");
		return $query->row["menu_class"];
	}
	
}
?>