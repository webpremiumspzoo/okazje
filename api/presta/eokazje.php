<?php

// prestashop

class eokazje {
	
		private $hash1 = '{{XXX}}';
		private $hash2 = '{{YYY}}';
		
		function __construct() {
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,$_POST['backurl']);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_USERPWD, $this->hash1.":".$this->hash2);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query( array('grant_type' => 'client_credentials') ) );
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			
			$output = curl_exec ($ch);
			$output = json_decode($output, true);
			if(empty($output['access_token'])) die(json_encode(array('result' => 'error', 'message' => 'wrong credentials provided')));
			
			$params['stockmin'] = intval($_POST['stockmin']);
			$params['stockmax'] = intval($_POST['stockmax']);
			$params['product_id'] = intval($_POST['product_id']);
			$params['attribute_id'] = intval($_POST['attribute_id']);
			$params['backurl'] = $_POST['backurl'];
			
			$checkSum = md5(json_encode($params));
			if($checkSum != $_POST['checksum']) die(json_encode(array('result' => 'error', 'message' => 'wrong check sum')));
			
		}
		
		public function checkStock() {
			
			// pobieramy konfiguracje
			require_once('config/settings.inc.php');
			
			$mysqli = @new mysqli(_DB_SERVER_, _DB_USER_, _DB_PASSWD_, _DB_NAME_);
			if ($mysqli->connect_errno) die(json_encode(array('result' => 'error', 'message' => 'Connect Error: ' . $mysqli->connect_errno)));

			if ($result = $mysqli->query("SELECT * FROM `"._DB_PREFIX_."stock_available` WHERE id_product=".intval($_POST['product_id'])." AND id_product_attribute=".intval($_POST['attribute_id']) )) {
                if($result->num_rows == 0) die(json_encode(array('result' => 'error', 'message' => 'product does not exist')));
				$row = $result->fetch_object();
				$result->close();
			} else {
				die(json_encode(array('result' => 'error', 'message' => 'product does not exist')));
			}
			
			if($row->quantity <= $_POST['stockmax'] and $row->quantity >= $_POST['stockmin']) {
				die( json_encode( array('result' => 'ok', 'message' => 'run rule') ) );
			}
			
			die( json_encode( array('result' => 'ok', 'message' => 'do not run rule') ) );
		}
} 


$eokazje = new eokazje();
$eokazje->checkStock();

?>
