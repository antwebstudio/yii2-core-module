<?php
namespace ant\sandbox\components;

use Yii;
use yii\helpers\ArrayHelper;

class SandboxManager extends \yii\base\Component {
	public $gateway = [];
	public $autoRedirect = false;
	public $debug = true;
	
	protected $_gateway;
	/*protected $_request;
	protected $_receiver;
	protected $_response;
	protected $_isRequery = false;*/
	
	public function init() {
	}
	
	public function getSandbox($name, $config = []) {
		if (!isset($this->_gateway[$name])) {
			$config = ArrayHelper::merge($this->gateway[$name], $config);
			$config['name'] = $name;
			
			$this->_gateway[$name] = Yii::createObject($config);
		}
		return $this->_gateway[$name];
	}
}