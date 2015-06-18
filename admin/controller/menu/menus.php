<?php
class ControllerMenuMenus extends Controller {
	private $error = array(); 
	
	public function index() {   
		$this->load->language('menu/menu');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('menu/menus');
		$this->load->model('menu/menuitems');
		$this->getList();
		
	}
	
	public function insert() {
		$this->load->language('menu/menu');
	
		$this->document->setTitle($this->language->get('heading_title'));
	
		$this->load->model('menu/menus');
	
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$menu_id=$this->model_menu_menus->addMenu($this->request->post);
			
			$this->session->data['success'] = $this->language->get('text_insert_success');
			if($this->request->post['act_mode']){	
				$this->redirect($this->url->link('menu/menus', 'token=' . $this->session->data['token'], 'SSL'));
			}else{				
				$this->redirect($this->url->link('menu/menus/update', '&menu_id='.$menu_id.'&token=' . $this->session->data['token'], 'SSL'));
			}
		}
	
		$this->getForm();
	}
	
	public function update() {
		$this->load->language('menu/menu');
	
		$this->document->setTitle($this->language->get('heading_title'));
	
		$this->load->model('menu/menus');
		$menu_id=$this->request->get['menu_id'];
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			
			$this->model_menu_menus->editMenu($menu_id, $this->request->post);
				
			$this->session->data['success'] = $this->language->get('text_update_success');
			
			if($this->request->post['act_mode']){	
				$this->redirect($this->url->link('menu/menus', 'token=' . $this->session->data['token'], 'SSL'));
			}else{				
				$this->redirect($this->url->link('menu/menus/update', '&menu_id='.$menu_id.'&token=' . $this->session->data['token'], 'SSL'));
			}
		}
	
		$this->getForm();
	}
	
	public function delete() {
		$this->load->language('menu/menu');
		$this->load->model('menu/menuitems');
		$this->document->setTitle($this->language->get('heading_title'));
	
		$this->load->model('menu/menus');
		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $menu_id) {
				$this->model_menu_menus->deleteMenu($menu_id);
			}
	
			$this->session->data['success'] = $this->language->get('text_delete_success');
	
			$this->redirect($this->url->link('menu/menus', 'token=' . $this->session->data['token'], 'SSL'));
		}
	
		$this->getList();
	}
	
	public function getList(){
		$this->data['breadcrumbs'] = array();
		
		$this->data['breadcrumbs'][] = array(
				'text'      => $this->language->get('text_home'),
				'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
				'separator' => false
		);
		
		$this->data['breadcrumbs'][] = array(
				'text'      => $this->language->get('heading_title'),
				'href'      => $this->url->link('menu/menus', 'token=' . $this->session->data['token'], 'SSL'),
				'separator' => ' :: '
		);
			
		$this->data['insert'] = $this->url->link('menu/menus/insert', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['delete'] = $this->url->link('menu/menus/delete', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['menus'] = array();
		
		$results = $this->model_menu_menus->getMenus();

		foreach ($results as $result) {
			$action = array();
				
			$action[] = array(
					'text' => $this->language->get('text_edit'),
					'href' => $this->url->link('menu/menus/update', 'token=' . $this->session->data['token'] . '&menu_id=' . $result['menu_id'], 'SSL')
			);
			$count_menuitem = $this->model_menu_menuitems->getTotalMenuitems($result['menu_id']);
			$menuitem_add_link='<a href="'.$this->url->link('menu/menuitems/insert', 'menu_id='.$result['menu_id'].'&token=' . $this->session->data['token'], 'SSL').'">'.$this->language->get('text_menuitem_add').'</a>';
			$menuitem_view_link=($count_menuitem)?'<a href="'.$this->url->link('menu/menuitems', 'menu_id='.$result['menu_id'].'&token=' . $this->session->data['token'], 'SSL').'">'.$this->language->get('text_menuitem_view').'</a>':$this->language->get('text_menuitem_view');
			$this->data['menus'][] = array(
					'menu_id' => $result['menu_id'],
					'menu_name'   => $result['menu_name'],
					'menu_items' => $count_menuitem,
					'selected'    => isset($this->request->post['selected']) && in_array($result['menu_id'], $this->request->post['selected']),
					'status'    => ($result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled')),
					'action'      => $action,
					'menuitem_link_add' =>$menuitem_add_link,
					'menuitem_link_view' =>$menuitem_view_link
			);
		}
		
		$this->data['heading_title'] = $this->language->get('heading_title');
		
		$this->data['text_no_results'] = $this->language->get('text_no_results');
		
		$this->data['column_name'] = $this->language->get('column_name');
		$this->data['column_description'] = $this->language->get('column_description');
		$this->data['column_menuitems'] = $this->language->get('column_menu_items');
		$this->data['column_status'] = $this->language->get('column_status');
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
		
		$this->template = 'menu/menu_list.tpl';
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
		$this->data['text_clear'] = $this->language->get('text_clear');
		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		
		$this->data['entry_name'] = $this->language->get('entry_name');
		$this->data['entry_description'] = $this->language->get('entry_description');
		$this->data['entry_class'] = $this->language->get('entry_class');
		$this->data['entry_status'] = $this->language->get('entry_status');
	
		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_save_and_close'] = $this->language->get('button_save_and_close');
		$this->data['button_cancel'] = $this->language->get('button_cancel');
	
		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
	
		if (isset($this->error['menu_name'])) {
			$this->data['error_menu_name'] = $this->error['menu_name'];
		}
	
		$this->data['breadcrumbs'] = array();
	
		$this->data['breadcrumbs'][] = array(
				'text'      => $this->language->get('text_home'),
				'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
				'separator' => false
		);
	
		$this->data['breadcrumbs'][] = array(
				'text'      => $this->language->get('heading_title'),
				'href'      => $this->url->link('menu/menus', 'token=' . $this->session->data['token'], 'SSL'),
				'separator' => ' :: '
		);
	
		if (!isset($this->request->get['menu_id'])) {
			$this->data['action'] = $this->url->link('menu/menus/insert', 'token=' . $this->session->data['token'], 'SSL');
		} else {
			$this->data['action'] = $this->url->link('menu/menus/update', 'token=' . $this->session->data['token'] . '&menu_id=' . $this->request->get['menu_id'], 'SSL');
		}
	
		$this->data['cancel'] = $this->url->link('menu/menus', 'token=' . $this->session->data['token'], 'SSL');
	
		if (isset($this->request->get['menu_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$menu_info = $this->model_menu_menus->getMenu($this->request->get['menu_id']);
		}
		
		if (isset($this->request->post['menu_name'])) {
			$this->data['menu_name'] = $this->request->post['menu_name'];
		} elseif (!empty($menu_info)) {
			$this->data['menu_name'] = $menu_info['menu_name'];
		} else {
			$this->data['menu_name'] = '';
		}
		
		if (isset($this->request->post['menu_description'])) {
			$this->data['menu_description'] = $this->request->post['menu_description'];
		} elseif (!empty($menu_info)) {
			$this->data['menu_description'] = $menu_info['menu_description'];
		} else {
			$this->data['menu_description'] = '';
		}
		
		if (isset($this->request->post['menu_class'])) {
			$this->data['menu_class'] = $this->request->post['menu_class'];
		} elseif (!empty($menu_info)) {
			$this->data['menu_class'] = $menu_info['menu_class'];
		} else {
			$this->data['menu_class'] = 'menu';
		}
		
		$this->data['token'] = $this->session->data['token'];
	
		if (isset($this->request->post['status'])) {
			$this->data['status'] = $this->request->post['status'];
		} elseif (!empty($menu_info)) {
			$this->data['status'] = $menu_info['status'];
		} else {
			$this->data['status'] = 1;
		}
	
		$this->template = 'menu/menu_form.tpl';
		$this->children = array(
				'common/header',
				'common/footer'
		);
	
		$this->response->setOutput($this->render());
	}
	
	private function validateForm() {
		if (!$this->user->hasPermission('modify', 'menu/menus')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		if ((utf8_strlen($this->request->post['menu_name']) < 1) || (utf8_strlen($this->request->post['menu_name']) > 255)) {
			$this->error['name'][$language_id] = $this->language->get('error_menu_name');
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
		if (!$this->user->hasPermission('modify', 'menu/menus')) {
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