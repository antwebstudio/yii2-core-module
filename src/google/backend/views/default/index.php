<?php
use yii\helpers\Url;

$client = Yii::$app->google;
$force = isset($_GET['force']);

if ($client->isAuthenticated) {
	Yii::$app->covid19->storeAccessToken(Yii::$app->google->getAccessToken());
	
	/* 
	

	echo '<pre>'.print_r($header, 1).'</pre>';
	echo '<pre>'.print_r($today, 1).'</pre>'; */
}
//$names = Yii::$app->cache->get(\ant\google\components\CovidStatistic::CACHE_NAME_CACHE_REGISTERED);

//echo '<pre>'.print_r($names, 1).'</pre>';
//Yii::$app->covid19->flushCache();
//$names = Yii::$app->cache->get(\ant\google\components\CovidStatistic::CACHE_NAME_CACHE_REGISTERED);
//echo '<pre>'.print_r($names, 1).'</pre>';

try {
	$penang = Yii::$app->covid19->getPenangStatistic($force);
	$latest = Yii::$app->covid19->getLatestStatistic($force);
	echo '<pre>'.print_r($latest, 1).'</pre>';
	echo '<pre>'.print_r($penang, 1).'</pre>';
} catch (\Exception $ex) {
	if ($force) throw $ex;
}
//print_r(Yii::$app->covid19->retrieveAccessToken());

?>
<?php if ($client->isAuthenticated): ?>
	<a class="btn btn-primary" href="<?= Url::to(['logout-oauth']) ?>">Logout</a>
<?php else: ?>
	<a class="btn btn-primary" href="<?= Url::to(['oauth']) ?>">Authenticate</a>
<?php endif ?>