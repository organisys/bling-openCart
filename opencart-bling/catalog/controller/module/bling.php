<?php

class ControllerModuleBling extends Controller {
	protected function erro($cod){
		$erro_msgs = array(
			1 => 'Erro: apikey nуo configurada',
			2 => 'Erro: apikey invalida',
			3 => 'Erro: Nenhuma requisiчуo'
		);

		die(json_encode(array('erro' => array('cod' => $cod,'msg' => $erro_msgs[$cod]))));
	}

	protected function index() {
		header('Content-type: application/json');

		if ($this->request->get['route'] == 'service/bling'){
			$store_id = $this->config->get('config_store_id');

			$this->load->model('setting/setting');
			$this->load->model('catalog/product');
			$this->load->model('sale/order');

			$settings = $this->model_setting_setting->getSetting('bling',$store_id);
			$apikey = (isset($settings['apikey'])) ? trim($settings['apikey']) : '';

			if (!$apikey) $this->erro(1);

			if(isset($this->request->post)){
				if (isset($this->request->post['apikey'])){
					if($this->request->post['apikey'] != $apikey) $this->erro(1);
					if (isset($this->request->post['data'])){
						$data = json_decode(html_entity_decode($this->request->post['data']),true);

						if(isset($data['order_id'])){
							$order = $this->model_sale_order->getOrder($data['order_id']);
							$order['order_product'] = $this->model_sale_order->getOrderProducts($data['order_id']);
							$order['order_voucher'] = $this->model_sale_order->getOrderVouchers($data['order_id']);
							$order['order_totals'] = $this->model_sale_order->getOrderTotals($data['order_id']);

							echo json_encode($order);

							foreach($data as $key=>$value)
								$order[$key] = $value;

							@$this->model_sale_order->editOrder($data['order_id'],$order);
						}
					}
				}
			} else {
				$this->erro(3);
			}
		}

		die();
	}
}
?>