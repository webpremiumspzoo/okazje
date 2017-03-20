<?php

// virtue mart

ini_set('error_reporting',E_NONE);
ini_set('display_errors',0);

define('_JEXEC', 1);
defined('DS') or define('DS', DIRECTORY_SEPARATOR);

if (file_exists(__DIR__ . '/defines.php')) include_once __DIR__ . '/defines.php';
if (!defined('_JDEFINES')) {
	define('JPATH_BASE', __DIR__);
	require_once JPATH_BASE . '/includes/defines.php';
}

require_once JPATH_BASE . '/includes/framework.php';
	
class eokazje {
	
		private $hash1 = '{{XXX}}';
		private $hash2 = '{{YYY}}';
		
		private $db;
		private $query;
		private $app;
		private $vmProdTableSuff;
		private $vmConfig;
		private $vmLang;
		
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
			
			$this->app = JFactory::getApplication('site');
			$this->config = JFactory::getConfig();
			$this->db = JFactory::getDbo();
	
			if (!class_exists( 'VmConfig' )) require(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php');
	
			$this->vmConfig = VmConfig::loadConfig();
			$this->vmLang = VmConfig::get('active_languages','default');
			$this->vmLang = array_shift($this->vmLang);
			$this->vmProdTableSuff = '_'.strtolower(str_replace('-','_',$this->vmLang));
			
			if($this->vmProdTableSuff == '_') unset($this->vmProdTableSuff);
			$query = $this->db->getQuery(true);
			$query->select($this->db->quoteName(array('virtuemart_product_id', 'product_in_stock', 'product_ordered')));
			$query->from($this->db->quoteName('#__virtuemart_products'.$this->vmProdTableSuff));
			$query->where($this->db->quoteName('virtuemart_product_id') . '='. $this->db->quote( $_POST['product_id'] ));
			$query->order('virtuemart_product_id ASC');
			$this->db->setQuery($query);
			$p = $this->db->loadObjectList();
			var_dump($p);
			
			if(empty($p)) die(json_encode(array('result' => 'error', 'message' => 'product does not exist')));
			
			$stock = $p->product_in_stock - $p->product_ordered;
			
			if($stock <= $_POST['stockmax'] and $stock >= $_POST['stockmin']) {
				die( json_encode( array('result' => 'ok', 'message' => 'run rule') ) );
			}
			
			die( json_encode( array('result' => 'ok', 'message' => 'do not run rule') ) );
		}
} 


$eokazje = new eokazje();
$eokazje->checkStock();

?>
