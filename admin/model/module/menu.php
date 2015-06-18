<?php 
class Modelmodulemenu extends Model {
	
	public function install() {
		$Tablemenus_query="CREATE TABLE IF NOT EXISTS `".DB_PREFIX."menus` (
							`menu_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
							`menu_name` VARCHAR(255) DEFAULT NULL,
							`menu_description` TEXT,
							`menu_class` VARCHAR(150) DEFAULT 'menu',
							`status` TINYINT(1) DEFAULT '1',
							PRIMARY KEY (`menu_id`)
							) ENGINE=INNODB DEFAULT CHARSET=utf8;";
		$this->db->query($Tablemenus_query);
		$Tablemenuitems_query="CREATE TABLE IF NOT EXISTS `".DB_PREFIX."menuitems` (
								`menu_item_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
								`parent_id` int(11) DEFAULT '0',
								`sort_order` int(3) DEFAULT '0',
								`status` tinyint(1) DEFAULT '0',
								`menu_id` int(11) unsigned NOT NULL,
								`menu_item_class` varchar(100) DEFAULT NULL,
								`menu_item_link` varchar(255) DEFAULT NULL,
								PRIMARY KEY (`menu_item_id`)
								) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		$this->db->query($Tablemenuitems_query);
		$TablemenuitemDesc_query="CREATE TABLE IF NOT EXISTS `".DB_PREFIX."menuitem_descriptions` (
									`menu_item_id` int(11) unsigned NOT NULL,
									`language_id` int(11) unsigned NOT NULL,
									`menu_item_name` varchar(255) DEFAULT NULL,
									`menu_item_description` text,
									PRIMARY KEY (`menu_item_id`,`language_id`)
									) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		$this->db->query($TablemenuitemDesc_query);
	}

	public function uninstall(){
		$querydrop="DROP TABLE IF EXISTS `".DB_PREFIX."menus`, `".DB_PREFIX."menuitems`, `".DB_PREFIX."menuitem_descriptions`;";
		$this->db->query($querydrop);
	}

}
?>