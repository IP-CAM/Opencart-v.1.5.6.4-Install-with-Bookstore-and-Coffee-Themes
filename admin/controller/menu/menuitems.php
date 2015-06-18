<?php 
class ControllerMenuMenuitems extends Controller { 
	private $error = array();
 
	public function index() {
		$this->load->language('menu/menuitem');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('menu/menuitems');
		$this->load->model('menu/menus');
		 
		$this->getList();
	}

	public function insert() {
		$this->load->language('menu/menuitem');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('menu/menuitems');
		$this->load->model('menu/menus');
		$menu_id=isset($this->request->get['menu_id'])?$this->request->get['menu_id']:$this->request->post['menu_id'];
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$menuitem_id=$this->model_menu_menuitems->addMenuitem($this->request->post);

			$this->session->data['success'] = $this->language->get('text_insert_success');
			
			//$this->redirect($this->url->link('menu/menuitems', 'menu_id='.$menu_id.'&token=' . $this->session->data['token'], 'SSL')); 
			if($this->request->post['act_mode']){
				$this->redirect($this->url->link('menu/menuitems', 'menu_id='.$menu_id.'&token=' . $this->session->data['token'], 'SSL'));
			}else{
				$this->redirect($this->url->link('menu/menuitems/update', 'menu_id='.$menu_id.'&menuitem_id='.$menuitem_id.'&token=' . $this->session->data['token'], 'SSL'));
			}
		}

		$this->getForm();
	}

	public function update() {
		$this->load->language('menu/menuitem');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('menu/menuitems');
		$this->load->model('menu/menus');
		$menu_id=isset($this->request->get['menu_id'])?$this->request->get['menu_id']:$this->request->post['menu_id'];
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$menuitem_id=$this->request->get['menuitem_id'];
			$this->model_menu_menuitems->editMenuitem($menuitem_id, $this->request->post);
			
			$this->session->data['success'] = $this->language->get('text_update_success');
			
			//$this->redirect($this->url->link('menu/menuitems', 'menu_id='.$menu_id.'&token=' . $this->session->data['token'], 'SSL'));
			if($this->request->post['act_mode']){
				$this->redirect($this->url->link('menu/menuitems', 'menu_id='.$menu_id.'&token=' . $this->session->data['token'], 'SSL'));
			}else{
				$this->redirect($this->url->link('menu/menuitems/update', 'menu_id='.$menu_id.'&menuitem_id='.$menuitem_id.'&token=' . $this->session->data['token'], 'SSL'));
			}
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('menu/menuitem');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('menu/menuitems');
		
		$menu_id=$this->request->get['menu_id'];
		$this->data['menu_id']=$menu_id;
		
		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $menuitem_id) {
				$this->model_menu_menuitems->deleteMenuitem($menuitem_id);
			}

			$this->session->data['success'] = $this->language->get('text_delete_success');

			$this->redirect($this->url->link('menu/menuitems', 'menu_id='.$menu_id.'&token=' . $this->session->data['token'], 'SSL'));
		}

		$this->getList();
	}
	
	private function getList() {   		
		$menu_id=$this->request->get['menu_id'];
		$this->data['menu_id']=$menu_id;
		
		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('menu/menuitems', 'menu_id='.$menu_id.'&token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
   		
		$this->data['insert'] = $this->url->link('menu/menuitems/insert', 'menu_id='.$menu_id.'&token=' . $this->session->data['token'], 'SSL');
		$this->data['delete'] = $this->url->link('menu/menuitems/delete', 'menu_id='.$menu_id.'&token=' . $this->session->data['token'], 'SSL');
		
		$this->data['menuitems'] = array();
		
		$this->data['menu_name']=$this->model_menu_menus->getMenuName($menu_id);
		
		$menuitem_data = $this->cache->get('menuitems.' . (int)$this->config->get('config_language_id') . '.' . (int)$menu_id);
		if (!$menuitem_data) {
			$menuitem_data = $this->model_menu_menuitems->getMenuitems(0,$menu_id);
			$this->cache->set('menuitems.' . (int)$this->config->get('config_language_id') . '.' . (int)$menu_id, $menuitem_data);
		}
		
		$results=$menuitem_data;
		
		foreach ($results as $result) {
			$action = array();
			
			$action[] = array(
				'text' => $this->language->get('text_edit'),
				'href' => $this->url->link('menu/menuitems/update', 'menu_id='.$menu_id.'&token=' . $this->session->data['token'] . '&menuitem_id=' . $result['menu_item_id'], 'SSL')
			);
					
			$this->data['menuitems'][] = array(
				'menuitem_id' => $result['menu_item_id'],
				'name'        => $result['name'],
				'sort_order'  => $result['sort_order'],
				'status'    => ($result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled')),
				'selected'    => isset($this->request->post['selected']) && in_array($result['menu_item_id'], $this->request->post['selected']),
				'action'      => $action
			);
		}
		
		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_no_results'] = $this->language->get('text_no_results');

		$this->data['column_name'] = $this->language->get('column_name');
		$this->data['column_status'] = $this->language->get('column_status');
		$this->data['column_sort_order'] = $this->language->get('column_sort_order');
		$this->data['column_action'] = $this->language->get('column_action');

		$this->data['button_insert'] = $this->language->get('button_insert');
		$this->data['button_delete'] = $this->language->get('button_delete');
 
 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
		
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}
		
		$this->template = 'menu/menuitem_list.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	private function getForm() {
		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_none'] = $this->language->get('text_none');
		$this->data['text_default'] = $this->language->get('text_default');
		$this->data['text_image_manager'] = $this->language->get('text_image_manager');
		$this->data['text_browse'] = $this->language->get('text_browse');
		$this->data['text_enabled'] = $this->language->get('text_enabled');
    		$this->data['text_disabled'] = $this->language->get('text_disabled');
				
		$this->data['entry_name'] = $this->language->get('entry_name');
		$this->data['entry_description']=$this->language->get('entry_description');
		$this->data['entry_parent']=$this->language->get('entry_parent');
		$this->data['entry_class'] = $this->language->get('entry_class');
		$this->data['entry_link'] = $this->language->get('entry_link');
		$this->data['entry_sort_order'] = $this->language->get('entry_sort_order');
		$this->data['entry_status'] = $this->language->get('entry_status');
		
		$this->data['button_save_and_close'] = $this->language->get('button_save_and_close');
		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');

    	$this->data['tab_general'] = $this->language->get('tab_general');
    	$this->data['tab_data'] = $this->language->get('tab_data');
		
    	$menu_id=$this->request->get['menu_id'];
    	$this->data['menu_id']=$menu_id;
    	$this->data['menu_name']=$this->model_menu_menus->getMenuName($menu_id);
    	
 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
	
 		if (isset($this->error['name'])) {
			$this->data['error_name'] = $this->error['name'];
		} else {
			$this->data['error_name'] = array();
		}

  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'menu_id='.$menu_id.'&token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);
   		$this->data['breadcrumbs'][] = array(
   				'text'      => '['.$this->data['menu_name'].'] '.$this->language->get('text_menu'),
   				'href'      => $this->url->link('menu/menus', 'menu_id='.$menu_id.'&token=' . $this->session->data['token'], 'SSL'),
   				'separator' => ' :: '
   		);
   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('menu/menuitems', 'menu_id='.$menu_id.'&token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
		if (!isset($this->request->get['menuitem_id'])) {
			$this->data['action'] = $this->url->link('menu/menuitems/insert', 'menu_id='.$menu_id.'&token=' . $this->session->data['token'], 'SSL');
		} else {
			$this->data['action'] = $this->url->link('menu/menuitems/update', 'menu_id='.$menu_id.'&token=' . $this->session->data['token'] . '&menuitem_id=' . $this->request->get['menuitem_id'], 'SSL');
		}
		
		$this->data['cancel'] = $this->url->link('menu/menuitems', 'menu_id='.$menu_id.'&token=' . $this->session->data['token'], 'SSL');

		if (isset($this->request->get['menuitem_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
      		$menuitem_info = $this->model_menu_menuitems->getMenuitem($this->request->get['menuitem_id']);
    	}
		
		$this->data['token'] = $this->session->data['token'];
		
		
		
		$this->load->model('localisation/language');
		
		$this->data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['menuitem_description'])) {
			$this->data['menuitem_name'] = $this->request->post['menuitem_description'];
		} elseif (isset($this->request->get['menuitem_id'])) {
			$this->data['menuitem_description'] = $this->model_menu_menuitems->getMenuDescriptions($this->request->get['menuitem_id']);
		} else {
			$this->data['menuitem_description'] = array();
		}

		$menuitems = $this->model_menu_menuitems->getMenuItems(0, $menu_id);

		// Remove own id from list
		if (!empty($menuitem_info)) {
			foreach ($menuitems as $key => $menuitem) {
				if ($menuitem['menu_item_id'] == $menuitem_info['menu_item_id']) {
					unset($menuitems[$key]);
				}
			}
		}

		$this->data['menuitems'] = $menuitems;

		if (isset($this->request->post['parent_id'])) {
			$this->data['parent_id'] = $this->request->post['parent_id'];
		} elseif (!empty($menuitem_info)) {
			$this->data['parent_id'] = $menuitem_info['parent_id'];
		} else {
			$this->data['parent_id'] = 0;
		}
		
		if (isset($this->request->post['link'])) {
			$this->data['link'] = $this->request->post['link'];
		} elseif (!empty($menuitem_info)) {
			$this->data['link'] = $menuitem_info['menu_item_link'];
		} else {
			$this->data['link'] = '';
		}
		
		if (isset($this->request->post['class'])) {
			$this->data['class'] = $this->request->post['class'];
		} elseif (!empty($menuitem_info)) {
			$this->data['class'] = $menuitem_info['menu_item_class'];
		} else {
			$this->data['class'] = '';
		}		
		
		if (isset($this->request->post['sort_order'])) {
			$this->data['sort_order'] = $this->request->post['sort_order'];
		} elseif (!empty($menuitem_info)) {
			$this->data['sort_order'] = $menuitem_info['sort_order'];
		} else {
			$this->data['sort_order'] = 0;
		}	
		
		if (isset($this->request->post['status'])) {
			$this->data['status'] = $this->request->post['status'];
		} elseif (!empty($menuitem_info)) {
			$this->data['status'] = $menuitem_info['status'];
		} else {
			$this->data['status'] = 1;
		}

		$this->load->model('design/layout');
		
		$this->data['layouts'] = $this->model_design_layout->getLayouts();
						
		$this->template = 'menu/menuitem_form.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	private function validateForm() {
		if (!$this->user->hasPermission('modify', 'menu/menuitems')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['menuitem_description'] as $language_id => $value) {
			if ((utf8_strlen($value['name']) < 2) || (utf8_strlen($value['name']) > 255)) {
				$this->error['name'][$language_id] = $this->language->get('error_name');
			}
		}
		
		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}
					
		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}

	private function validateDelete() {
		if (!$this->user->hasPermission('modify', 'menu/menuitems')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
 
		if (!$this->error) {
			return true; 
		} else {
			return false;
		}
	}
}
?>