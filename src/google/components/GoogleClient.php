<?php
namespace ant\google\components;

use Yii;
use yii\helpers\Url;

class GoogleClient extends \yii\base\Component {
	const SESSION_NAME = 'token';
	
	public $clientId;
	public $clientSecret;
	public $redirectUrl = ['/google/backend/default/oauth'];
	public $javascriptOrigin;
	public $useOfflineAccessToken = true;
	
	protected $_client;
	protected $_authenticator;
	
	public function init() {
		$this->_client = \Ttskch\GoogleSheetsApi\Factory\GoogleClientFactory::createOAuthClient(
			$this->clientId,
			$this->clientSecret,
			Url::to($this->redirectUrl, true),
			$this->javascriptOrigin
		);
		
		if (isset($this->accessToken)) $this->_client->setAccessToken($this->accessToken);
		
		if ($this->useOfflineAccessToken) {
			$this->_client->setAccessType('offline');
			// Using "force" ensures that your application always receives a refresh token.
			// If you are not using offline access, you can omit this.
			$this->_client->setApprovalPrompt("force");
		}
		
		$this->_authenticator = new \Ttskch\GoogleSheetsApi\Authenticator($this->_client);
	}
	
	public function authorize() {
		$this->_authenticator->authorize();
	}
	
	public function authenticate($code) {
		$this->storeAccessToken($this->_authenticator->authenticate($code));
		$this->_authenticator->authenticate($code);
	}
	
	public function getIsAuthenticated() {
		return isset($this->accessToken);
	}
	
	public function getSpreadSheet($spreadsheetId, $range) {
		$api = \Ttskch\GoogleSheetsApi\Factory\ApiClientFactory::create($this->_client);

		$response = $api->getGoogleService()->spreadsheets_values->get($spreadsheetId, $range);
		return $response->getValues();
	}
	
	public function setAccessToken($token) {
		$this->_client->setAccessToken($token);
	}
	
	public function getAccessToken() {
		return $this->session->get(self::SESSION_NAME);
	}
	
	public function logout() {
		$this->session->remove(self::SESSION_NAME);
	}
	
	protected function storeAccessToken($value) {
		$this->session->set(self::SESSION_NAME, $value);
	}
	
	protected function getSession() {
		return Yii::$app->session;
	}
}