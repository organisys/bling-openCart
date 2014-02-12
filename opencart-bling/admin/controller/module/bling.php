<?php
class ControllerModuleBling extends Controller {
	private $error = array();

	public function install(){
		$this->load->model('module/bling');
		$this->load->model('setting/setting');

		$settings = $this->model_setting_setting->getSetting('bling',$store_id);

		$settings['apikey'] = '';

		$this->model_setting_setting->editSetting('bling',$settings,$store_id);

		$store_id = $this->config->get('config_store_id');

		$this->model_module_bling->setupLayout($store_id);
		$this->model_module_bling->createModuleTables();
	}

	public function gerar_codigo($id){
		return 'opencart_'.substr(sha1($id),0,8);
	}

	public function load_sales_orders(){
		if (isset($this->request->get['filter_order_id'])) {
			$filter_order_id = $this->request->get['filter_order_id'];
		} else {
			$filter_order_id = null;
		}

		if (isset($this->request->get['filter_customer'])) {
			$filter_customer = $this->request->get['filter_customer'];
		} else {
			$filter_customer = null;
		}

		if (isset($this->request->get['filter_order_status_id'])) {
			$filter_order_status_id = $this->request->get['filter_order_status_id'];
		} else {
			$filter_order_status_id = null;
		}

		if (isset($this->request->get['filter_total'])) {
			$filter_total = $this->request->get['filter_total'];
		} else {
			$filter_total = null;
		}

		if (isset($this->request->get['filter_date_added'])) {
			$filter_date_added = $this->request->get['filter_date_added'];
		} else {
			$filter_date_added = null;
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$filter_date_modified = $this->request->get['filter_date_modified'];
		} else {
			$filter_date_modified = null;
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'o.order_id';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'DESC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}

		if (isset($this->request->get['filter_customer'])) {
			$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_order_status_id'])) {
			$url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
		}

		if (isset($this->request->get['filter_total'])) {
			$url .= '&filter_total=' . $this->request->get['filter_total'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$this->data['breadcrumbs'] = array();

		$this->data['breadcrumbs'][] = array(
				'text'      => $this->language->get('text_home'),
				'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
				'separator' => false
		);

		$this->data['breadcrumbs'][] = array(
				'text'      => $this->language->get('heading_title'),
				'href'      => $this->url->link('module/bling', 'token=' . $this->session->data['token'] . $url, 'SSL'),
				'separator' => ' :: '
		);

		$this->data['invoice'] = $this->url->link('sale/order/invoice', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['insert'] = $this->url->link('sale/order/insert', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['delete'] = $this->url->link('sale/order/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');

		$this->data['orders'] = array();

		$data = array(
				'filter_order_id'        => $filter_order_id,
				'filter_customer'	     => $filter_customer,
				'filter_order_status_id' => $filter_order_status_id,
				'filter_total'           => $filter_total,
				'filter_date_added'      => $filter_date_added,
				'filter_date_modified'   => $filter_date_modified,
				'sort'                   => $sort,
				'order'                  => $order,
				'start'                  => ($page - 1) * $this->config->get('config_admin_limit'),
				'limit'                  => $this->config->get('config_admin_limit')
		);

		$order_total = $this->model_sale_order->getTotalOrders($data);

		$results = $this->model_sale_order->getOrders($data);

		foreach ($results as $result) {
			$action = array();

			$action[] = array(
					'text' => $this->language->get('text_view'),
					'href' => $this->url->link('sale/order/info', 'token=' . $this->session->data['token'] . '&order_id=' . $result['order_id'] . $url, 'SSL')
			);

			if (strtotime($result['date_added']) > strtotime('-' . (int)$this->config->get('config_order_edit') . ' day')) {
				$action[] = array(
						'text' => $this->language->get('text_edit'),
						'href' => $this->url->link('sale/order/update', 'token=' . $this->session->data['token'] . '&order_id=' . $result['order_id'] . $url, 'SSL')
				);
			}

			$this->data['orders'][] = array(
					'order_id'      => $result['order_id'],
					'customer'      => $result['customer'],
					'status'        => $result['status'],
					'total'         => $this->currency->format($result['total'], $result['currency_code'], $result['currency_value']),
					'date_added'    => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
					'date_modified' => date($this->language->get('date_format_short'), strtotime($result['date_modified'])),
					'selected'      => isset($this->request->post['selected']) && in_array($result['order_id'], $this->request->post['selected']),
					'action'        => $action
			);
		}

		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_no_results'] = $this->language->get('text_no_results');
		$this->data['text_missing'] = $this->language->get('text_missing');

		$this->data['column_order_id'] = htmlspecialchars('Nº do pedido');
		$this->data['column_customer'] = 'Cliente';
		$this->data['column_status'] = htmlspecialchars('Situação');
		$this->data['column_total'] = 'Total';
		$this->data['column_date_added'] = htmlspecialchars('Data de criação');
		$this->data['column_date_modified'] = htmlspecialchars('Data de modificação');

		$this->data['button_invoice'] = $this->language->get('button_invoice');
		$this->data['button_insert'] = $this->language->get('button_insert');
		$this->data['button_delete'] = $this->language->get('button_delete');
		$this->data['button_filter'] = $this->language->get('button_filter');

		$this->data['token'] = $this->session->data['token'];

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

		$url = '';

		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}

		if (isset($this->request->get['filter_customer'])) {
			$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_order_status_id'])) {
			$url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
		}

		if (isset($this->request->get['filter_total'])) {
			$url .= '&filter_total=' . $this->request->get['filter_total'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$this->data['sort_order'] = $this->url->link('module/bling', 'token=' . $this->session->data['token'] . '&sort=o.order_id' . $url, 'SSL');
		$this->data['sort_customer'] = $this->url->link('module/bling', 'token=' . $this->session->data['token'] . '&sort=customer' . $url, 'SSL');
		$this->data['sort_status'] = $this->url->link('module/bling', 'token=' . $this->session->data['token'] . '&sort=status' . $url, 'SSL');
		$this->data['sort_total'] = $this->url->link('module/bling', 'token=' . $this->session->data['token'] . '&sort=o.total' . $url, 'SSL');
		$this->data['sort_date_added'] = $this->url->link('module/bling', 'token=' . $this->session->data['token'] . '&sort=o.date_added' . $url, 'SSL');
		$this->data['sort_date_modified'] = $this->url->link('module/bling', 'token=' . $this->session->data['token'] . '&sort=o.date_modified' . $url, 'SSL');

		$url = '';

		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}

		if (isset($this->request->get['filter_customer'])) {
			$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_order_status_id'])) {
			$url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
		}

		if (isset($this->request->get['filter_total'])) {
			$url .= '&filter_total=' . $this->request->get['filter_total'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $order_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_admin_limit');
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('sale/order', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$this->data['pagination'] = $pagination->render();

		$this->data['filter_order_id'] = $filter_order_id;
		$this->data['filter_customer'] = $filter_customer;
		$this->data['filter_order_status_id'] = $filter_order_status_id;
		$this->data['filter_total'] = $filter_total;
		$this->data['filter_date_added'] = $filter_date_added;
		$this->data['filter_date_modified'] = $filter_date_modified;

		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		$this->data['sort'] = $sort;
		$this->data['order'] = $order;
	}

	public function load_products_catalog(){
		if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = null;
		}

		if (isset($this->request->get['filter_model'])) {
			$filter_model = $this->request->get['filter_model'];
		} else {
			$filter_model = null;
		}

		if (isset($this->request->get['filter_price'])) {
			$filter_price = $this->request->get['filter_price'];
		} else {
			$filter_price = null;
		}

		if (isset($this->request->get['filter_quantity'])) {
			$filter_quantity = $this->request->get['filter_quantity'];
		} else {
			$filter_quantity = null;
		}

		if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = null;
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'pd.name';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_price'])) {
			$url .= '&filter_price=' . $this->request->get['filter_price'];
		}

		if (isset($this->request->get['filter_quantity'])) {
			$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$this->data['breadcrumbs'] = array();

		$this->data['breadcrumbs'][] = array(
				'text'      => $this->language->get('text_home'),
				'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
				'separator' => false
		);

		$this->data['breadcrumbs'][] = array(
				'text'      => $this->language->get('heading_title'),
				'href'      => $this->url->link('catalog/product', 'token=' . $this->session->data['token'] . $url, 'SSL'),
				'separator' => ' :: '
		);

		$this->data['insert'] = $this->url->link('catalog/product/insert', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$this->data['copy'] = $this->url->link('catalog/product/copy', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$this->data['delete'] = $this->url->link('catalog/product/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');

		$this->data['products'] = array();

		$data = array(
				'filter_name'	  => $filter_name,
				'filter_model'	  => $filter_model,
				'filter_price'	  => $filter_price,
				'filter_quantity' => $filter_quantity,
				'filter_status'   => $filter_status,
				'sort'            => $sort,
				'order'           => $order,
				'start'           => ($page - 1) * $this->config->get('config_admin_limit'),
				'limit'           => $this->config->get('config_admin_limit')
		);

		$this->load->model('tool/image');

		$product_total = $this->model_catalog_product->getTotalProducts($data);

		$results = $this->model_catalog_product->getProducts($data);

		foreach ($results as $result) {
			$action = array();

			$action[] = array(
					'text' => $this->language->get('text_edit'),
					'href' => $this->url->link('catalog/product/update', 'token=' . $this->session->data['token'] . '&product_id=' . $result['product_id'] . $url, 'SSL')
			);

			if ($result['image'] && file_exists(DIR_IMAGE . $result['image'])) {
				$image = $this->model_tool_image->resize($result['image'], 40, 40);
			} else {
				$image = $this->model_tool_image->resize('no_image.jpg', 40, 40);
			}

			$special = false;

			$product_specials = $this->model_catalog_product->getProductSpecials($result['product_id']);

			foreach ($product_specials  as $product_special) {
				if (($product_special['date_start'] == '0000-00-00' || $product_special['date_start'] < date('Y-m-d')) && ($product_special['date_end'] == '0000-00-00' || $product_special['date_end'] > date('Y-m-d'))) {
					$special = $product_special['price'];

					break;
				}
			}

			$this->data['products'][] = array(
					'product_id' => $result['product_id'],
					'name'       => $result['name'],
					'model'      => $result['model'],
					'price'      => $result['price'],
					'special'    => $special,
					'image'      => $image,
					'quantity'   => $result['quantity'],
					'status'     => ($result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled')),
					'selected'   => isset($this->request->post['selected']) && in_array($result['product_id'], $this->request->post['selected']),
					'action'     => $action
			);
		}

		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['text_no_results'] = $this->language->get('text_no_results');
		$this->data['text_image_manager'] = $this->language->get('text_image_manager');

		$this->data['column_image'] = 'Imagem';
		$this->data['column_name'] = 'Nome';
		$this->data['column_model'] = 'Modelo';
		$this->data['column_price'] = htmlspecialchars('Preço');
		$this->data['column_quantity'] = 'Quantidade';
		$this->data['column_status'] = htmlspecialchars('Situação');

		$this->data['button_copy'] = $this->language->get('button_copy');
		$this->data['button_insert'] = $this->language->get('button_insert');
		$this->data['button_delete'] = $this->language->get('button_delete');
		$this->data['button_filter'] = $this->language->get('button_filter');

		$this->data['token'] = $this->session->data['token'];

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

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_price'])) {
			$url .= '&filter_price=' . $this->request->get['filter_price'];
		}

		if (isset($this->request->get['filter_quantity'])) {
			$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$this->data['sort_name'] = $this->url->link('module/bling', 'token=' . $this->session->data['token'] . '&sort=pd.name' . $url, 'SSL');
		$this->data['sort_model'] = $this->url->link('module/bling', 'token=' . $this->session->data['token'] . '&sort=p.model' . $url, 'SSL');
		$this->data['sort_price'] = $this->url->link('module/bling', 'token=' . $this->session->data['token'] . '&sort=p.price' . $url, 'SSL');
		$this->data['sort_quantity'] = $this->url->link('module/bling', 'token=' . $this->session->data['token'] . '&sort=p.quantity' . $url, 'SSL');
		$this->data['sort_status'] = $this->url->link('module/bling', 'token=' . $this->session->data['token'] . '&sort=p.status' . $url, 'SSL');
		$this->data['sort_order'] = $this->url->link('module/bling', 'token=' . $this->session->data['token'] . '&sort=p.sort_order' . $url, 'SSL');

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_price'])) {
			$url .= '&filter_price=' . $this->request->get['filter_price'];
		}

		if (isset($this->request->get['filter_quantity'])) {
			$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $product_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_admin_limit');
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('module/bling', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$this->data['pagination'] = $pagination->render();

		$this->data['filter_name'] = $filter_name;
		$this->data['filter_model'] = $filter_model;
		$this->data['filter_price'] = $filter_price;
		$this->data['filter_quantity'] = $filter_quantity;
		$this->data['filter_status'] = $filter_status;

		$this->data['sort'] = $sort;
		$this->data['order'] = $order;

		$this->template = 'module/bling.tpl';
		$this->children = array(
				'common/header',
				'common/footer'
		);
	}

	public function exportar_produto($produto,$key){
		$codigo = $this->gerar_codigo($produto['product_id']);

		$url = 'https://www.bling.com.br/Api/v2/produto/json/';
		$xml = '<?xml version="1.0" encoding="UTF-8"?><produto>';
		$xml .= '<codigo>'.$codigo.'</codigo>';
		$xml .= '<descricao>'.$produto['name'].'</descricao>';
		$xml .= '<vlr_unit>'.$produto['price'].'</vlr_unit>';
		$xml .= '<preco_custo>'.$produto['price'].'</preco_custo>';
		$xml .= '<peso_bruto>'.$produto['weight'].'</peso_bruto>';
		$xml .= '<peso_liq>'.$produto['weight'].'</peso_liq>';
		$xml .= '<estoque>'.$produto['quantity'].'</estoque>';
		$xml .= '</produto>';

		$postdata = array('apikey' => $key,'xml' => $xml);

		$curl_handle = curl_init();

		curl_setopt($curl_handle, CURLOPT_URL,$url);
		curl_setopt($curl_handle, CURLOPT_POST,1);
		curl_setopt($curl_handle, CURLOPT_POSTFIELDS,http_build_query($postdata));
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER,true);
		curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER,false);

		$result = curl_exec($curl_handle);
		if(curl_errno($curl_handle)){
			echo 'error:' . curl_error($curl_handle);
		}

		curl_close($curl_handle);

		//$this->model_module_bling->setids_assoc($codigo,$produto['product_id']);
	}

	public function exportar_produtos($list,$key){
		for($i=0,$l=count($list);$i<$l;++$i){
			$this->exportar_produto($this->model_catalog_product->getProduct($list[$i]),$key);
		}

		$this->data['msg'] = $l . ' Produtos exportados com sucesso.';
	}

	public function exportar_pedido($pedido,$key){
		$url = 'https://www.bling.com.br/Api/v2/pedido/json/';
		$xml = '<?xml version="1.0" encoding="UTF-8"?><pedido>';

		$data = explode(' ',$pedido['date_added']);
		$data = explode('-',$data[0]);
		$data = $data[2].'/'.$data[1].'/'.$data[0];
		$xml .= '<data>'.$data.'</data>';
		//$xml .= '<numero>'.$pedido['order_id'].'</numero>';
		$xml .= '<numero_loja>'.$pedido['order_id'].'</numero_loja>';

		$xml .= '<cliente>';
		$xml .= '<nome>'.$pedido['firstname'].' '.$pedido['lastname'].'</nome>';
		$xml .= '<tipo_pessoa>'.((strlen($pedido['payment_company_id']) == 11) ? 'F' : 'J').'</tipo_pessoa>';
		$xml .= '<cpf_cnpj>'.$pedido['payment_tax_id'].'</cpf_cnpj>';
		$xml .= '<ie>'.$pedido['payment_company_id'].'</ie><rg>'.$pedido['payment_company_id'].'</rg>';
		$xml .= '<endereco>'.$pedido['payment_address_1'].'</endereco>';
		$xml .= '<bairro>'.$pedido['payment_address_2'].'</bairro>';
		$xml .= '<cep>'.$pedido['payment_postcode'].'</cep>';
		$xml .= '<cidade>'.$pedido['payment_city'].'</cidade>';
		$xml .= '<uf>'.$pedido['payment_zone_code'].'</uf>';
		$xml .= '<fone>'.$pedido['telephone'].'</fone>';
		$xml .= '<email>'.$pedido['email'].'</email>';
		$xml .= '</cliente>';

		$xml .= '<transporte>';
		$xml .= '<transportadora></transportadora>';
		$xml .= '<tipo_frete>R</tipo_frete>';
		$xml .= '<servico_correios></servico_correios>';
		$xml .= '</transporte>';

		$produtos = $this->model_sale_order->getOrderProducts($pedido['order_id']);

		for($i=0,$l=count($produtos);$i<$l;++$i){
			$codigo = $this->gerar_codigo($produtos[$i]['product_id']);
			//$this->model_module_bling->setids_assoc($codigo,$produtos[$i]['product_id']);

			$xml .= '<item>';
			$xml .= '<codigo>'.$codigo.'</codigo>';
			$xml .= '<descricao>'.$produtos[$i]['name'].'</descricao>';
			$xml .= '<qtde>'.$produtos[$i]['quantity'].'</qtde>';
			$xml .= '<vlr_unit>'.$produtos[$i]['price'].'</vlr_unit>';
			$xml .= '</item>';
		}

		$vouchers = $this->model_sale_order->getOrderVouchers($pedido['order_id']);

		$frete = 0;
		$desconto = 0;

		$totals = $this->model_sale_order->getOrderTotals($pedido['order_id']);

		foreach($totals as $total){
			if ($total['code'] == 'shipping') $frete += $total;
			if ($total['code'] == 'coupon') $frete += $total;
			if ($total['code'] == 'voucher') $frete += $total;
		}

		$xml .= '<vlr_frete>'.+$frete.'</vlr_frete>';
		$xml .= '<vlr_desconto>'.+$desconto.'</vlr_desconto>';
		$xml .= '</pedido>';

		$postdata = array('apikey' => $key,'xml' => $xml);

		$curl_handle = curl_init();

		curl_setopt($curl_handle, CURLOPT_URL,$url);
		curl_setopt($curl_handle, CURLOPT_POST,1);
		curl_setopt($curl_handle, CURLOPT_POSTFIELDS,http_build_query($postdata));
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER,true);
		curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER,false);

		$result = curl_exec($curl_handle);
		if(curl_errno($curl_handle)){
			echo 'error:' . curl_error($curl_handle);
		}

		curl_close($curl_handle);
	}

	public function exportar_pedidos($list,$key){
		for($i=0,$l=count($list);$i<$l;++$i){
			$this->exportar_pedido($this->model_sale_order->getOrder($list[$i]),$key);
		}
	}

	public function atualizar_pedido($pedido,$key){
		ob_start();

		$url = 'https://www.bling.com.br/Api/v2/pedido/'.$pedido.'/json/?apikey='.$key;
		echo $url.PHP_EOL;

		$curl_handle = curl_init();

		curl_setopt($curl_handle, CURLOPT_URL,$url);
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER,true);
		curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER,false);

		$result = curl_exec($curl_handle);
		if(curl_errno($curl_handle)){
			echo 'error:' . curl_error($curl_handle);
		}

		curl_close($curl_handle);

		$data = json_decode($result,true);

		print_r($data);

		$ob = ob_get_contents();
		ob_end_clean();

		echo '<pre>'.$ob.'</pre>';
	}

	public function atualizar_pedidos($list,$key){
		for($i=0,$l=count($list);$i<$l;++$i){
			$this->atualizar_pedido($list[$i],$key);
		}
	}

	public function index() {
		$store_id = $this->config->get('config_store_id');

		$this->load->language('module/bling');
		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');
		$this->load->model('module/bling');
		$this->load->model('catalog/product');
		$this->load->model('sale/order');
		$this->load->model('localisation/order_status');

		$apikey = '';
		$this->data['aba'] = 0;

		$settings = $this->model_setting_setting->getSetting('bling',$store_id);

		if (isset($this->request->post['apikey'])){
			$this->data['aba'] = 2;
			$apikey = trim($this->request->post['apikey']);

			if (strlen($apikey) == 40){
				$settings['apikey'] = $apikey;
				$this->model_setting_setting->editSetting('bling',array('apikey' => $apikey),$store_id);
				$this->data['apikey'] = $apikey;
			} else {
				$this->data['error_msg'] = 'API key invalida';
				$apikey = '';
			}
		} else {
			$settings = $this->model_setting_setting->getSetting('bling',$store_id);
			$apikey = (isset($settings['apikey'])) ? trim($settings['apikey']) : '';

			if (strlen($apikey) == 40)
				$this->data['apikey'] = $apikey;
			else
				$this->data['error_msg'] = 'API key invalida ou inexistente';
		}

		if (isset($this->request->post['form'])){
			if (isset($this->request->post['selected']) && $apikey){
				if ($this->request->post['form'] == 'produtos'){
					$this->data['aba'] = 0;
					if ($this->request->post['action'] == 'export'){
						$this->exportar_produtos($this->request->post['selected'],$apikey);
					} elseif ($this->request->post['action'] == 'update'){

					} elseif ($this->request->post['action'] == 'import'){

					}
				} elseif ($this->request->post['form'] == 'pedidos'){
					$this->data['aba'] = 1;
					if ($this->request->post['action'] == 'export'){
						$this->exportar_pedidos($this->request->post['selected'],$apikey);
					} elseif ($this->request->post['action'] == 'update'){
						$this->atualizar_pedidos($this->request->post['selected'],$apikey);
					}
				}
			} else {
				if (!$apikey)
					$this->data['error_msg'] = 'API key invalida ou inexistente';
				else if(!isset($this->request->post['selected']))
					$this->data['error_msg'] = 'Nenhum '.(($this->request->post['form'] == 'pedidos') ? 'pedido' : 'produto').' selecionado.';
			}
		}

		//SET UP BREADCRUMB TRAIL. YOU WILL NOT NEED TO MODIFY THIS UNLESS YOU CHANGE YOUR MODULE NAME.
  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_module'),
			'href'      => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('module/bling', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);

		$this->data['action'] = $this->url->link('module/bling', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');

		//This code handles the situation where you have multiple instances of this module, for different layouts.
		$this->data['modules'] = array();

		if (isset($this->request->post['bling_module'])) {
			$this->data['modules'] = $this->request->post['bling_module'];
		} elseif ($this->config->get('bling_module')) {
			$this->data['modules'] = $this->config->get('bling_module');
		}

		$this->load->model('design/layout');

		$this->data['layouts'] = $this->model_design_layout->getLayouts();

		$this->load_products_catalog();
		$this->load_sales_orders();

		$this->template = 'module/bling.tpl';
		$this->children = array(
				'common/header',
				'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', 'module/bling')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}
	}


}
?>