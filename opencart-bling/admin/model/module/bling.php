<?php
class ModelModuleBling extends Model {
	public function createModuleTables() {
		$query = $this->db->query('CREATE TABLE IF NOT EXISTS '.DB_PREFIX.'bling_assoc_produtos (opencart_id INT(11), bling_id VARCHAR(10))');
		$query = $this->db->query('CREATE TABLE IF NOT EXISTS '.DB_PREFIX.'bling_assoc_pedidos (opencart_id INT(11), bling_id VARCHAR(10))');
	}

	public function setupLayout($store_id = 0){
		$query = $this->db->query('INSERT INTO '.DB_PREFIX.'setting (`store_id`,`group`,`key`,`value`,`serialized`) VALUES (\'0\',\'bling\',\'bling_module\',\'a:1:{i:0;a:4:{s:9:"layout_id";s:2:"14";s:8:"position";s:11:"content_top";s:6:"status";s:1:"1";s:10:"sort_order";s:3:"999";}}\',\'1\');');
	}

	public function setids_assoc($blingid,$opencartid,$tipo = 0){
		$query = $this->db->query('DELETE FROM '.DB_PREFIX.'bling_assoc_'.(($tipo)? 'pedidos' : 'produtos').' WHERE opencart_id = '.addslashes($opencartid));
		$query = $this->db->query('INSERT INTO '.DB_PREFIX.'bling_assoc_'.(($tipo)? 'pedidos' : 'produtos').' (opencart_id,bling_id) VALUES ('.addslashes($opencartid).',"'.addslashes($blingid).'")');
	}

	public function getids_assoc($blingid = false,$opencartid = false,$tipo = 0){
		// $tipo = 0  -> produtos
		// $tipo = 1  -> pedidos

		if ($blingid === false){
			if ($opencartid === false)
				return false;

			$query = $this->db->query('SELECT * FROM '.DB_PREFIX.'bling_assoc_'.(($tipo)? 'pedidos' : 'produtos').' WHERE opencart_id = '.addslashes($opencartid));
			return $query->rows[0]['bling_id'];

		} else {
			$query = $this->db->query('SELECT * FROM '.DB_PREFIX.'bling_assoc_'.(($tipo)? 'pedidos' : 'produtos').' WHERE bling_id = "'.addslashes($blingid)).'"';
			return $query->rows[0]['opencart_id'];
		}
	}
}
?>