<?php  
class ControllerCheckoutCheckout extends Controller { 
	
	
	public function valMinQuantity(){
		// Validate minimum quantity requirments.			
		$products = $this->cart->getProducts();

		foreach ($products as $product) {
			$product_total = 0;

			foreach ($products as $product_2) {
				if ($product_2['product_id'] == $product['product_id']) {
					$product_total += $product_2['quantity'];
				}
			}		

			if ($product['minimum'] > $product_total) {
				$this->redirect($this->url->link('checkout/cart'));
			}				
		}	
		
	}
	/**
	*  добавляет в шаблон все переменные из language/checkout 
	*  если имя переменной состоит из entry_{name}, то добавляем и инициализируем переменную {name} либо значением из сессии либо пустой строкой
	* ну, спорное решение и хер бы с ним 
	*/
	public function setVars(){
		$this->data = $this->language->get('all');	
		foreach($this->data as $key=>$value){
			if (strpos($key, 'entry_')!==false) {
				$v = substr($key,6, strlen($key));				
				$this->data[$v] = (isset($this->session->data['guest'][$v]))?$this->session->data['guest'][$v]:'';				
			}			
		}
	}
	
	

	/**
	* Методы доставки
	*/ 
	public function getShippingMethods(){
			$quote_data = array();
			$this->load->model('setting/extension');
			$results = $this->model_setting_extension->getExtensions('shipping');
		
			foreach ($results as $result) {
				if ($this->config->get($result['code'] . '_status')) {		
					
					$this->load->model('shipping/' . $result['code']);

					$quote = $this->{'model_shipping_' . $result['code']}->getQuote('');  // здесь был $shipping_address в качестве праметра, но был убран. 
					// заодно были изменены модели flat и pickup

					if ($quote) {
						$quote_data[$result['code']] = array( 
							'title'      => $quote['title'],
							'quote'      => $quote['quote'], 
							'sort_order' => $quote['sort_order'],
							'error'      => $quote['error']
						);
					}
				}
			}

			$sort_order = array();

			foreach ($quote_data as $key => $value) {
				$sort_order[$key] = $value['sort_order'];
			}

			array_multisort($sort_order, SORT_ASC, $quote_data);

			$this->session->data['guest']['shipping_methods'] = $quote_data;
		




		
	}
	/**
	* Методы оплаты
	*/
	public function getPaymentMethods(){
		$total_data = array();					
			$total = 0;
			$taxes = $this->cart->getTaxes();

			$this->load->model('setting/extension');

			$sort_order = array(); 

			$results = $this->model_setting_extension->getExtensions('total');

			foreach ($results as $key => $value) {
				$sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
			}

			array_multisort($sort_order, SORT_ASC, $results);

			foreach ($results as $result) {
				if ($this->config->get($result['code'] . '_status')) {
					$this->load->model('total/' . $result['code']);

					$this->{'model_total_' . $result['code']}->getTotal($total_data, $total, $taxes);
				}
			}

			// Payment Methods
			$method_data = array();

			$this->load->model('setting/extension');

			$results = $this->model_setting_extension->getExtensions('payment');

			$cart_has_recurring = $this->cart->hasRecurringProducts();

			foreach ($results as $result) {
				if ($this->config->get($result['code'] . '_status')) {
					$this->load->model('payment/' . $result['code']);

					$method = $this->{'model_payment_' . $result['code']}->getMethod('', $total); // $payment_address - убрал временно

					if ($method) {
						if($cart_has_recurring > 0){
							if (method_exists($this->{'model_payment_' . $result['code']},'recurringPayments')) {
								if($this->{'model_payment_' . $result['code']}->recurringPayments() == true){
									$method_data[$result['code']] = $method;
								}
							}
						} else {
							$method_data[$result['code']] = $method;
						}
					}
				}
			}

			$sort_order = array(); 

			foreach ($method_data as $key => $value) {
				$sort_order[$key] = $value['sort_order'];
			}

			array_multisort($sort_order, SORT_ASC, $method_data);			

			$this->session->data['guest']['payment_methods'] = $method_data;	

		//}			

		
	}
	
	public function setData(){
		$this->language->load('checkout/checkout');		
		$this->getShippingMethods();
		$this->getPaymentMethods();
		$this->setVars();
		$this->document->setTitle($this->language->get('heading_title')); 	
		$this->document->addScript('catalog/view/javascript/jquery/colorbox/jquery.colorbox-min.js');
		$this->document->addStyle('catalog/view/javascript/jquery/colorbox/colorbox.css');
	}
	
	public function index() {
		//var_dump($this->session->data['guest']);
		// Validate cart has products and has stock.
		if (!$this->cart->hasProducts()){
			$this->redirect($this->url->link('checkout/cart'));
		}

		$this->valMinQuantity();
		
		$this->setData();

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/checkout/checkout.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/checkout/checkout.tpl';
		} else {
			$this->template = 'default/template/checkout/checkout.tpl';
		}

		$this->children = array(
			'common/column_left',
			'common/column_right',
			'common/content_top',
			'common/content_bottom',
			'common/footer',
			'common/header'	
		);

	

		$this->response->setOutput($this->render());
	}

	public function country() {
		$json = array();

		$this->load->model('localisation/country');

		$country_info = $this->model_localisation_country->getCountry($this->request->get['country_id']);

		if ($country_info) {
			$this->load->model('localisation/zone');

			$json = array(
				'country_id'        => $country_info['country_id'],
				'name'              => $country_info['name'],
				'iso_code_2'        => $country_info['iso_code_2'],
				'iso_code_3'        => $country_info['iso_code_3'],
				'address_format'    => $country_info['address_format'],
				'postcode_required' => $country_info['postcode_required'],
				'zone'              => $this->model_localisation_zone->getZonesByCountryId($this->request->get['country_id']),
				'status'            => $country_info['status']		
			);
		}

		$this->response->setOutput(json_encode($json));
	}
	/**
	* Проверка длины поля $field
    *	в json генерируется сообщение
	*/
	public function chkLen($field, $min, $max, &$json){
		$result = false;
		if ((utf8_strlen($this->request->post[$field]) < $min) || (utf8_strlen($this->request->post[$field]) > $max)) {
				$error_tpl = $this->language->get('error_tpl');
				$field_name = $this->language->get('entry_'.$field);
				$json['error'][$field] = sprintf($error_tpl, $field_name,  $min, $max);
			} else {
				$this->session->data['guest'][$field] = $this->request->post[$field];
				$result = true;	
			}
		return $result;	
	}
	public function confirm(){
		$json = array();
		$this->session->data['account'] = 'guest';
		$this->response->setOutput(json_encode($json));
		$this->language->load('checkout/checkout');		
		// Validate cart has products and has stock.
		if (!$this->cart->hasProducts()) {
			$json['redirect'] = $this->url->link('checkout/cart');		
		}
		$this->valMinQuantity();
		
		
		if (!$json) {
			 $this->chkLen('firstname', 1, 32, $json);
			 $this->chkLen('lastname', 1, 32, $json);
			 $this->chkLen('telephone', 3, 32, $json);
			 if ($this->chkLen('address_1', 3, 128, $json)) $this->session->data['guest']['address_2'] = $this->request->post['address_2'];;
			 
			if ((utf8_strlen($this->request->post['email']) > 96) || !preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $this->request->post['email'])) {
				$json['error']['email'] = $this->language->get('error_email');
			} else {
				$this->session->data['guest']['email'] = $this->request->post['email'];				
			}

		

			
			if (!isset($this->request->post['shipping_method'])) {
				$json['error']['warning'] = $this->language->get('error_shipping');
			} else {
				$shipping = explode('.', $this->request->post['shipping_method']);

				if (!isset($shipping[0]) || !isset($shipping[1]) || !isset($this->session->data['guest']['shipping_methods'][$shipping[0]]['quote'][$shipping[1]])) {			
					$json['error']['warning'] = $this->language->get('error_shipping');
				} else {									
					$this->session->data['guest']['shipping_methods_code'] = $this->request->post['shipping_method'];
				}
			}
			
			if (!isset($this->request->post['payment_method'])) {
				$json['error']['warning'] = $this->language->get('error_payment');
			} elseif (!isset($this->session->data['guest']['payment_methods'][$this->request->post['payment_method']])) {
				$json['error']['warning'] = $this->language->get('error_payment');
			} else {
				$this->session->data['guest']['payment_methods_code'] = $this->request->post['payment_method'];
			}	
			
			
		}

		$this->session->data['account'] = 'guest';


		$this->response->setOutput(json_encode($json));	
	}
}
?>