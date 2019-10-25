<?php
namespace ant\sandbox\gateway\ipay88;

class Sandbox extends \ant\sandbox\components\Sandbox {
	public $merchantCode;
	public $merchantKey;
	
	/*public $Signature;
	public $Amount;
	public $RefNo;
	public $Currency;
	public $PaymentId;*/
	
	public function process($request) {
		parent::process($request);
		
		$this->load($request->post(), '');
	}
	
	public function response() {
		
	}
	
	public function getRedirectUrl() {
		return $this->param('ResponseURL');
	}

	public static function createSignature($merchantKey, $merchantCode, $paymentId, $refNo, $amount, $currency, $status)
    {
        $amount = str_replace([',', '.'], '', $amount);

        $paramsInArray = [$merchantKey, $merchantCode, $paymentId, $refNo, $amount, $currency, $status];

        return self::createSignatureFromString(implode('', $paramsInArray));
    }

	public static function createSignatureFromString($fullStringToHash)
    {
        return base64_encode(self::hex2bin(sha1($fullStringToHash)));
    }

    protected static function hex2bin($hexSource)
    {
        $bin = '';
        for ($i = 0; $i < strlen($hexSource); $i = $i + 2) {
            $bin .= chr(hexdec(substr($hexSource, $i, 2)));
        }
        return $bin;
    }
}