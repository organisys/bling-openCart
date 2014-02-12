<?php
################################################################################################
#  DIY Module Builder for Opencart 1.5.1.x From HostJars http://opencart.hostjars.com    	   #
################################################################################################
?><?php

class ControllerModuleBling extends Controller {
	protected function erro($cod){
		$erro_msgs = array(
			1 => 'Erro: apikey não configurada',
			2 => 'Erro: apikey invalida',
			3 => 'Erro: Nenhuma requisição'
		);

		die('<erro><cod>'.$cod.'</cod><msg>'.$erro_msgs[$cod].'</msg></erro>');
	}

	protected function index() {
		//header('Content-type: application/xml');
		echo '<?xml version="1.0" encoding="UTF-8"?>';

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

						echo (($a = print_r($data,true)) ? $a : 'false').PHP_EOL;

						if(isset($data['order_id'])){
							$order = $this->model_sale_order->getOrder($data['order_id']);
							$order['order_product'] = $this->model_sale_order->getOrderProducts($data['order_id']);
							$order['order_voucher'] = $this->model_sale_order->getOrderVouchers($data['order_id']);
							$order['order_totals'] = $this->model_sale_order->getOrderTotals($data['order_id']);

							print_r($order);

							foreach($data as $key=>$value)
								$order[$key] = $value;

							$this->model_sale_order->editOrder($data['order_id'],$order);
						}
					}
				}
			} else {
				$this->erro(3);
			}

			die();
		}
	}
}
?>