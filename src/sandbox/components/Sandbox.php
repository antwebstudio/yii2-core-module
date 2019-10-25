<?php
namespace ant\sandbox\components;

use Yii;
use yii\helpers\ArrayHelper;

class Sandbox extends \yii\base\Model {
	public $name;
	public $receiverName;
	
	protected $allowVerb = ['post'];
	protected $_request;
	protected $_receiver = [];
	protected $_currentReceiver;
	
	/*public static function create($name, $isRequery = false) {
		$class = '\ant\sandbox\components\sandbox\\'.$name.'\SandboxComponent';
		
		$object = new $class;
		$object->name = $name;
		$object->setIsRequery($isRequery);
		
		return $object;
	}
	
	public function isRequery() {
		return $this->_isRequery;
	}*/
	
	public function process($request) {
		$this->_request = $request;
		
		if ($this->canProcess()) {
		} else {
			throw new \Exception('Only method: '.implode(', ', $this->allowVerb).' is allowed for this page. ');
		}
	}
	
	protected function setReceiver($value) {
		$this->_receiver = $value;
	}
	
	public function getReceiver() {
		if (!isset($this->_currentReceiver)) {
			$config = $this->getReceiverConfig($this->receiverName);
			$config['gateway'] = $this;
			
			$this->_currentReceiver = Yii::createObject($config);
			$this->_currentReceiver->load($this->_request->post(), '');
		}
		return $this->_currentReceiver;
	}
	
	protected function getReceiverConfig($name) {
		$config = ArrayHelper::merge([
			'default' => [
				'class' => '\ant\sandbox\gateway\\'.$this->name.'\Receiver',
			],
			'requery' => [
				'class' => '\ant\sandbox\gateway\\'.$this->name.'\RequeryReceiver',
			],
		], $this->_receiver);
		
		return $config[$name];
	}
	
	protected function canProcess() {
		return in_array(strtolower($this->_request->getMethod()), $this->allowVerb);
	}
	
	protected function param($name) {
		return $this->_request->post($name);
	}
}