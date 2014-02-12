<?php
################################################################################################
#  DIY Module Builder for Opencart 1.5.1.x From HostJars http://opencart.hostjars.com    	   #
################################################################################################
class ModelModuleBling extends Model {
	public function createModuleTables() {
		$query = $this->db->query('CREATE TABLE IF NOT EXISTS '.DB_PREFIX.'bling_assoc_produtos (opencart_id INT(11), bling_id VARCHAR(10))');
		$query = $this->db->query('CREATE TABLE IF NOT EXISTS '.DB_PREFIX.'bling_assoc_pedidos (opencart_id INT(11), bling_id VARCHAR(10))');
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