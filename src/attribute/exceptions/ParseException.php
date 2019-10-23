<?php
namespace ant\attribute\exceptions;

class ParseException extends \Exception {
	protected $_params = [];
	
	public function __construct($message, $params, $previous = null) {
		$this->_params = [];
		foreach ($params as $name => $p) {
			if (is_object($p)) {
				$this->_params[$name] = get_class($p);
			}
			$this->_params[$name] = $p;
		}
		parent::__construct($message, null, $previous);
	}
	
	public function getParamsUsed() {
		return $this->_params;
	}
}