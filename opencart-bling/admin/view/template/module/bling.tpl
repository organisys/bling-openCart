<?php echo $header; ?>
<div id="content">
<div class="breadcrumb">
  <?php foreach ($breadcrumbs as $breadcrumb) { ?>
  <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
  <?php } ?>
</div>
<?php if ($error_warning) { ?>
<div class="warning"><?php echo $error_warning; ?></div>
<?php } ?>
<div class="box">
  <div class="heading">
    <h1><img src="view/image/module.png" alt="" /> <?php echo $heading_title; ?></h1>
    <div style="display:inline;">
    <style type="text/css" scoped>
		p {
			margin:2px 0 0 130px;
			padding:0 0 0 12px;
			width:80px;
			height:35px;
			line-height:2.5;
			font-size:15px;
			font-weight:bold;
			color:#5050b0;
			border-left:1px solid #c0c0d0;
			cursor: pointer;
		}
		p:hover {
			background-color: rgba(60,60,140,0.08);
			color:#202080; 		
		}
		p[data-active="true"] {
			background-color: rgba(20,20,80,0.1);
			color:#202080;
		}
    </style>
    <form id="form_pages" method="post">
	<input style="display:none" id="action_aba" name="action_aba" value="" />
    </form>
    <p id="_produtos" data-active="true">Produtos</p>
    <p id="_pedidos" style="margin:-35px 0 0 222px;width:70px">Pedidos</p>
    <p id="_config" style="margin:-35px 0 0 305px;width:116px">Configura&ccedil;&atilde;o</p>
    <script>
		document.getElementById('_produtos').onclick = function(){exibir_aba('produtos','pedidos','config');$('#action_aba').attr('value','produtos');$('#form_pages').submit();};
		document.getElementById('_pedidos').onclick = function(){exibir_aba('pedidos','produtos','config');$('#action_aba').attr('value','pedidos');$('#form_pages').submit();};
		document.getElementById('_config').onclick = function(){exibir_aba('config','produtos','pedidos');$('#action_aba').attr('value','config');$('#form_pages').submit();};

		function exibir_aba(aba,ocultar1,ocultar2){
			document.getElementById(aba).style.display = '';
			document.getElementById(ocultar1).style.display = 'none';
			document.getElementById(ocultar2).style.display = 'none';
			document.getElementById('b_'+aba).style.display = '';
			document.getElementById('b_'+ocultar1).style.display = 'none';
			document.getElementById('b_'+ocultar2).style.display = 'none';
			document.getElementById('_'+aba).dataset.active = 'true';
			document.getElementById('_'+ocultar1).dataset.active = '';
			document.getElementById('_'+ocultar2).dataset.active = '';
		}

		$(document).ready(function(){<?php
			switch($aba){
				case 0:	echo "exibir_aba('produtos','pedidos','config');";break;
				case 1:	echo "exibir_aba('pedidos','produtos','config');";break;
				case 2:	echo "exibir_aba('config','produtos','pedidos');";break;
			}
		?>});
    </script>
    </div>

    <div id="b_produtos" class="buttons" style="margin-top:-37px;">
    	<a onclick="$('#action_produtos').attr('value','export');$('#form_produtos').submit();" class="button"><span>Exportar</span></a>
    	<a onclick="$('#action_produtos').attr('value','import');$('#form_produtos').submit();" class="button"><span>Importar</span></a>
    	<a onclick="$('#action_produtos').attr('value','update');$('#form_produtos').submit();" class="button"><span>Atualizar</span></a>
    </div>
    <div id="b_pedidos" class="buttons" style="margin-top:-37px;display:none;">
    	<a onclick="$('#action_pedidos').attr('value','export');$('#form_pedidos').submit();" class="button"><span>Exportar</span></a>
    	<a onclick="$('#action_pedidos').attr('value','update');$('#form_pedidos').submit();" class="button"><span>Atualizar</span></a>
    </div>
    <div id="b_config" class="buttons" style="margin-top:-37px;display:none;">
    	<a onclick="$('#form_config').submit();" class="button"><span>Salvar</span></a>
    </div>
    </div>

	<div id="msg" class="content" style="margin-bottom:-280px;<?php if(!(isset($msg) || isset($error_msg)))echo 'display:none;'?>">
		<style type="text/css" scoped>
			.msg{
				padding : 4px;
				margin-bottom:5px;
				border:2px solid #50ff50;
				border-radius: 1px;
			}
			.error_msg{
				padding : 4px;
				border:2px solid #ff3030;
				border-radius: 1px;
			}
		</style>
		<?php if(isset($msg)) echo '<div class="msg">'.$msg.'</div>';?>
		<?php if(isset($error_msg)) echo '<div class="error_msg"> '.$error_msg.'</div>';?>
	</div>

  	<div id="config" class="content" style="display:none">
  		<form id="form_config" method="post">
			<div style="padding:4px 3px 4px 3px;margin:6px 4px 6px 4px:border-bottom:1px dotted #202020;">
            	<span class="required">*</span>apikey
				<input type="text" size="48" name="apikey" value="<?php echo (isset($apikey)) ? $apikey : '';?>" size="40"/>
            </div>
		</form>
  	</div>

    <div id="produtos" class="content">
      <form method="post" enctype="multipart/form-data" id="form_produtos">
      	<input style="display:none" name="form" value="produtos"/>
      	<input style="display:none" id="action_produtos" name="action" value="export"/>
        <table class="list">
          <thead>
            <tr>
              <td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
              <td class="center"><?php echo $column_image; ?></td>
              <td class="left"><?php if ($sort == 'pd.name') { ?>
                <a href="<?php echo $sort_name; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_name; ?></a>
                <?php } else { ?>
                <a href="<?php echo $sort_name; ?>"><?php echo $column_name; ?></a>
                <?php } ?></td>
              <td class="left"><?php if ($sort == 'p.model') { ?>
                <a href="<?php echo $sort_model; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_model; ?></a>
                <?php } else { ?>
                <a href="<?php echo $sort_model; ?>"><?php echo $column_model; ?></a>
                <?php } ?></td>
              <td class="left"><?php if ($sort == 'p.price') { ?>
                <a href="<?php echo $sort_price; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_price; ?></a>
                <?php } else { ?>
                <a href="<?php echo $sort_price; ?>"><?php echo $column_price; ?></a>
                <?php } ?></td>
              <td class="right"><?php if ($sort == 'p.quantity') { ?>
                <a href="<?php echo $sort_quantity; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_quantity; ?></a>
                <?php } else { ?>
                <a href="<?php echo $sort_quantity; ?>"><?php echo $column_quantity; ?></a>
                <?php } ?></td>
              <td class="left"><?php if ($sort == 'p.status') { ?>
                <a href="<?php echo $sort_status; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_status; ?></a>
                <?php } else { ?>
                <a href="<?php echo $sort_status; ?>"><?php echo $column_status; ?></a>
                <?php } ?></td>
            </tr>
          </thead>
          <tbody>
            <tr class="filter">
              <td></td>
              <td></td>
              <td><input type="text" name="filter_name" value="<?php echo $filter_name; ?>" /></td>
              <td><input type="text" name="filter_model" value="<?php echo $filter_model; ?>" /></td>
              <td align="left"><input type="text" name="filter_price" value="<?php echo $filter_price; ?>" size="8"/></td>
              <td align="right"><input type="text" name="filter_quantity" value="<?php echo $filter_quantity; ?>" style="text-align: right;" /></td>
              <td><select name="filter_status">
                  <option value="*"></option>
                  <?php if ($filter_status) { ?>
                  <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                  <?php } else { ?>
                  <option value="1"><?php echo $text_enabled; ?></option>
                  <?php } ?>
                  <?php if (!is_null($filter_status) && !$filter_status) { ?>
                  <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                  <?php } else { ?>
                  <option value="0"><?php echo $text_disabled; ?></option>
                  <?php } ?>
                </select></td>
            </tr>
            <?php if ($products) { ?>
            <?php foreach ($products as $product) { ?>
            <tr>
              <td style="text-align: center;"><?php if ($product['selected']) { ?>
                <input type="checkbox" name="selected[]" value="<?php echo $product['product_id']; ?>" checked="checked" />
                <?php } else { ?>
                <input type="checkbox" name="selected[]" value="<?php echo $product['product_id']; ?>" />
                <?php } ?></td>
              <td class="center"><img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" style="padding: 1px; border: 1px solid #DDDDDD;" /></td>
              <td class="left"><?php echo $product['name']; ?></td>
              <td class="left"><?php echo $product['model']; ?></td>
              <td class="left"><?php if ($product['special']) { ?>
                <span style="text-decoration: line-through;"><?php echo $product['price']; ?></span><br/>
                <span style="color: #b00;"><?php echo $product['special']; ?></span>
                <?php } else { ?>
                <?php echo $product['price']; ?>
                <?php } ?></td>
              <td class="right"><?php if ($product['quantity'] <= 0) { ?>
                <span style="color: #FF0000;"><?php echo $product['quantity']; ?></span>
                <?php } elseif ($product['quantity'] <= 5) { ?>
                <span style="color: #FFA500;"><?php echo $product['quantity']; ?></span>
                <?php } else { ?>
                <span style="color: #008000;"><?php echo $product['quantity']; ?></span>
                <?php } ?></td>
              <td class="left"><?php echo $product['status']; ?></td>
            </tr>
            <?php } ?>
            <?php } else { ?>
            <tr>
              <td class="center" colspan="8"><?php echo $text_no_results; ?></td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
      </form>
      <div class="pagination"><?php echo $pagination; ?></div>
    </div>

    <div id="pedidos" class="content" style="display:none">
      <form method="post" enctype="multipart/form-data" id="form_pedidos">
        <input style="display:none" name="form" value="pedidos"/>
      	<input style="display:none" id="action_pedidos" name="action" value="export"/>
        <table class="list">
          <thead>
            <tr>
              <td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
              <td class="right"><?php if ($sort == 'o.order_id') { ?>
                <a href="<?php echo $sort_order; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_order_id; ?></a>
                <?php } else { ?>
                <a href="<?php echo $sort_order; ?>"><?php echo $column_order_id; ?></a>
                <?php } ?></td>
              <td class="left"><?php if ($sort == 'customer') { ?>
                <a href="<?php echo $sort_customer; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_customer; ?></a>
                <?php } else { ?>
                <a href="<?php echo $sort_customer; ?>"><?php echo $column_customer; ?></a>
                <?php } ?></td>
              <td class="left"><?php if ($sort == 'status') { ?>
                <a href="<?php echo $sort_status; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_status; ?></a>
                <?php } else { ?>
                <a href="<?php echo $sort_status; ?>"><?php echo $column_status; ?></a>
                <?php } ?></td>
              <td class="right"><?php if ($sort == 'o.total') { ?>
                <a href="<?php echo $sort_total; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_total; ?></a>
                <?php } else { ?>
                <a href="<?php echo $sort_total; ?>"><?php echo $column_total; ?></a>
                <?php } ?></td>
              <td class="left"><?php if ($sort == 'o.date_added') { ?>
                <a href="<?php echo $sort_date_added; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_date_added; ?></a>
                <?php } else { ?>
                <a href="<?php echo $sort_date_added; ?>"><?php echo $column_date_added; ?></a>
                <?php } ?></td>
              <td class="left"><?php if ($sort == 'o.date_modified') { ?>
                <a href="<?php echo $sort_date_modified; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_date_modified; ?></a>
                <?php } else { ?>
                <a href="<?php echo $sort_date_modified; ?>"><?php echo $column_date_modified; ?></a>
                <?php } ?></td>
            </tr>
          </thead>
          <tbody>
            <tr class="filter">
              <td></td>
              <td align="right"><input type="text" name="filter_order_id" value="<?php echo $filter_order_id; ?>" size="4" style="text-align: right;" /></td>
              <td><input type="text" name="filter_customer" value="<?php echo $filter_customer; ?>" /></td>
              <td><select name="filter_order_status_id">
                  <option value="*"></option>
                  <?php if ($filter_order_status_id == '0') { ?>
                  <option value="0" selected="selected"><?php echo $text_missing; ?></option>
                  <?php } else { ?>
                  <option value="0"><?php echo $text_missing; ?></option>
                  <?php } ?>
                  <?php foreach ($order_statuses as $order_status) { ?>
                  <?php if ($order_status['order_status_id'] == $filter_order_status_id) { ?>
                  <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                  <?php } ?>
                  <?php } ?>
                </select></td>
              <td align="right"><input type="text" name="filter_total" value="<?php echo $filter_total; ?>" size="4" style="text-align: right;" /></td>
              <td><input type="text" name="filter_date_added" value="<?php echo $filter_date_added; ?>" size="12" class="date" /></td>
              <td><input type="text" name="filter_date_modified" value="<?php echo $filter_date_modified; ?>" size="12" class="date" /></td>
            </tr>
            <?php if ($orders) { ?>
            <?php foreach ($orders as $order) { ?>
            <tr>
              <td style="text-align: center;"><?php if ($order['selected']) { ?>
                <input type="checkbox" name="selected[]" value="<?php echo $order['order_id']; ?>" checked="checked" />
                <?php } else { ?>
                <input type="checkbox" name="selected[]" value="<?php echo $order['order_id']; ?>" />
                <?php } ?></td>
              <td class="right"><?php echo $order['order_id']; ?></td>
              <td class="left"><?php echo $order['customer']; ?></td>
              <td class="left"><?php echo $order['status']; ?></td>
              <td class="right"><?php echo $order['total']; ?></td>
              <td class="left"><?php echo $order['date_added']; ?></td>
              <td class="left"><?php echo $order['date_modified']; ?></td>
            </tr>
            <?php } ?>
            <?php } else { ?>
            <tr>
              <td class="center" colspan="8"><?php echo $text_no_results; ?></td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
      </form>
      <div class="pagination"><?php echo $pagination; ?></div>
    </div>
</div>
<?php echo $footer; ?>
