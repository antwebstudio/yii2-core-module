<?php
namespace ant\sandbox\components;

class BaseReceiver extends \yii\base\Model {
	public $isCustomResponse = false;
	
	protected $_gateway;
	
	protected function setGateway($value) {
		$this->_gateway = $value;
	}
	
	protected function getGateway() {
		return $this->_gateway;
	}
}