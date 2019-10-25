<?php
namespace ant\sandbox\gateway\ipay88;

class RequeryReceiver extends \ant\sandbox\components\BaseReceiver {
	public $isCustomResponse = true;
	public $reachedDayLimit = false;
	public $error = false;
	
	public function response() {
		if ($this->reachedDayLimit) return 'Limited by per day maximum number of requery';
		
		return $this->isSuccessful() ? '00' : 'Payment fail';
	}
	
	public function isSuccessful() {
		return !$this->error;
	}
}