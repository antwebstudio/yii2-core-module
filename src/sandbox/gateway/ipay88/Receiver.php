<?php
namespace ant\sandbox\gateway\ipay88;
use yii\helpers\ArrayHelper;
use ant\sandbox\gateway\ipay88\Sandbox;

class Receiver extends \ant\sandbox\components\BaseReceiver {
	public $isCustomResponse = false;
	
	public $MerchantCode;
	public $PaymentId;
	public $RefNo;
	public $Amount;
	public $Currency;
	public $ProdDesc;
	public $UserName;
	public $UserEmail;
	public $UserContact;
	public $Remark;
	public $Lang;
	public $Signature;
	public $ResponseURL;
	public $BackendURL;
	
	protected $_isSuccessful;
	
	public function rules() {
		return [
			[['MerchantCode', 'Signature', 'UserName'], 'required'],
			[['Amount', 'RefNo', 'Currency'], 'required'],
			[['Signature'], 'validateSignature'],
			[['MerchantCode'], 'compare', 'compareValue' => $this->gateway->merchantCode],
			[['PaymentId', 'RefNo', 'Amount', 'Currency', 'ProdDesc', 'UserName' , 'UserEmail', 'UserContact', 'Remark', 'Lang', 'ResponseURL', 'BackendURL'], 'safe'],
		];
	}
	
	public function isSuccessful() {
		if (!isset($this->_isSuccessful)) {
			$this->_isSuccessful = $this->validate();
		}
		return $this->_isSuccessful;
	}
	
	public function getCancelUrl() {
		return $this->ResponseURL;
	}
	
	public function getBackendUrl() {
		return $this->BackendURL;
	}
	
	public function getRedirectUrl() {
		return $this->ResponseURL;
	}
	
	public function getErrorReturnParams($customParams = []) {
		$status = 0; // Error
		
		return ArrayHelper::merge([
			'Currency' => $this->Currency,
			'PaymentId' => $this->PaymentId,
			'RefNo' => $this->RefNo,
			'Status' => $status,
			'Amount' => $this->Amount,
			'Signature' => $this->generateSignature(),
			'TransId' => 'TEST_'.uniqid(),
			'ErrDesc' => $this->isSuccessful() ? '' : 'Unknown Error',
			'Remark' => '',
			'ReQueryStatus' => 'Payment Fail',
		], $customParams);
	}
	
	public function getCancelReturnParams() {
		$data = $this->getReturnParams();
		$data['Status'] = 0;
		$data['ErrDesc'] = 'Customer Cancel Transaction';
		
		return $data;
	}
	
	public function getReturnParams() {
		$amount = $this->Amount;
		$refNo = $this->RefNo;
		$currency = $this->Currency;
		$paymentId = $this->PaymentId;
		$status = $this->isSuccessful() ? 1 : 0;

		$params = [
			'Currency' => $currency,
			'PaymentId' => $paymentId,
			'RefNo' => $refNo,
			'Status' => $status,
			'Amount' => $amount,
			'Signature' => $this->generateSignature(),
			'TransId' => 'TEST_'.uniqid(),
			'ErrDesc' => $this->isSuccessful() ? '' : 'Unknown Error',
			'Remark' => '',
		];
		
		return $params;
	}
	
	public function validateSignature($attribute, $params) {
		if ($this->{$attribute} != $this->getSignature()) {
			$this->addError($attribute, 'Signature not match. ');
		}
	}
	
	// Signature to be post back
	public function generateSignature() {
		$refNo = $this->RefNo;
		$total = $this->Amount;
		$currency = $this->Currency;
		$status = $this->isSuccessful() ? 1 : 0;
		
		$string = $this->gateway->merchantKey . $this->gateway->merchantCode . $refNo . str_replace(['.', ','], '', $total) . $currency . $status;
		
		return Sandbox::createSignatureFromString($string);
	}
	
	// Signature expected to be received
	protected function getSignature() {
		$refNo = $this->RefNo;
		$total = $this->Amount;
		$currency = $this->Currency;
		
		$string = $this->gateway->merchantKey . $this->MerchantCode . $refNo . str_replace(['.', ','], '', $total) . $currency;

		return Sandbox::createSignatureFromString($string);
	}
}