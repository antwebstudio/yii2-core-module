<?php
namespace ant\sandbox\gateway\ipay88;

use GuzzleHttp\Client;

class BackendJob extends \yii\base\BaseObject implements \yii\queue\JobInterface
{
	public $url;
	public $params = [];
    
    public function execute($queue)
    {
		$this->params['backend'] = 1;
		try {
			$client = new Client;
			$response = $client->request('post', $this->url, ['form_params' => $this->params]);
			//$response = $client->send($request);
        } catch (\GuzzleHttp\Exception\ClientException $ex) {
			//file_put_contents('log/error.log', $ex);
			//file_put_contents('log/error-response.log', $ex->getResponse()->getBody());
            if ($ex->hasResponse() && 404 === $ex->getResponse()->getStatusCode()) {
                return false;
            }
            throw $ex;
		} catch (\GuzzleHttp\Exception\ServerException $ex) {
			//file_put_contents('log/error.log', $ex);
			//file_put_contents('log/error-response.log', $ex->getResponse()->getBody());
            throw $ex;
		}
    }
}
