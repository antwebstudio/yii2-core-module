<?php
namespace ant\google\components;

use Yii;
use yii\helpers\ArrayHelper;
use ant\helpers\DateTime;
use ant\helpers\File;

class CovidStatistic extends \yii\base\Component {
	const CACHE_NAME_CACHE_REGISTERED = 'covid_cache_registered';
	
	public $spreadsheetId = '1f67pjbwqlSzSTiXk36RdtGEHQ7DqFt3WQLV6rQ8FZ2I';
	public $spreadsheetRange = 'Covid19';
	
	public $columnDividers = [
		'malaysia' => 'Malaysia',
		'penang' => 'Penang',
		'states' => 'States',
	];
	
	public $cacheDuration = 0;
	
	protected $storePath = '@root/config/google-client.php';
	protected $_spreadsheet;
	
	public function storeAccessToken($token) {
		File::storeArray(Yii::getAlias($this->storePath), $token);
	}
	
	public function retrieveAccessToken() {
		return File::loadArray(Yii::getAlias($this->storePath));
	}
	
	public function getPenangStatistic() {
		return $this->cache->getOrSet($this->registerCacheName(__CLASS__ . '. ' . __METHOD__) , function() {
			$divider = isset($this->columnDividers['penang']) ? $this->columnDividers['penang'] : null;
			$section = $this->getSection($this->getHeader(), $divider, $this->getData());
			$dateColumn = $this->getSectionByIndex($this->getHeader(), $this->getData(), 0, 0);
			
			$combined = [];
			foreach ($section as $i => $row) {
				$combined[$i] = ArrayHelper::merge($section[$i], $dateColumn[$i]);
			}
			return $combined;
		}, $this->cacheDuration);
	}
	
	public function getLatestStatistic() {
		return $this->cache->getOrSet($this->registerCacheName(__CLASS__ . '. ' . __METHOD__) , function() {
			return $this->divideRowToSections($this->getHeader(), $this->getLastValidRow());
		}, $this->cacheDuration);
	}
	
	public function flushCache() {
		$names = $this->cache->get(self::CACHE_NAME_CACHE_REGISTERED);
		// Delete all cache registered
		foreach ($names as $name => $v) {
			$this->cache->delete($name);
		}
		// Delete the cache of registered cache name
		$this->cache->delete(self::CACHE_NAME_CACHE_REGISTERED);
	}
	
	protected function registerCacheName($name) {
		$names = $this->cache->get(self::CACHE_NAME_CACHE_REGISTERED);
		$names[$name] = 1;
		$this->cache->set(self::CACHE_NAME_CACHE_REGISTERED, $names);
		
		return $name;
	}
	
	protected function getHeader() {
		if (!isset($this->spreadsheet['header'])) {
			$this->_spreadsheet = $this->getSpreadSheet(true);
		}
		return $this->spreadsheet['header'];
	}
	
	protected function getLastValidRow() {
		foreach ($this->getData() as $row) {
			if ($this->checkIfValidRow($row)) {
				$lastValidRow = $row;
			}
		}
		return isset($lastValidRow) ? $lastValidRow : null;
	}
	
	protected function getData() {
		if (!isset($this->spreadsheet['data'])) {
			$this->_spreadsheet = $this->getSpreadSheet(true);
		}
		return $this->spreadsheet['data'];
	}
	
	protected function getSpreadSheet($refresh = false) {
		if (!isset($this->_spreadsheet)) {
			$values = $this->google->getSpreadSheet($this->spreadsheetId, $this->spreadsheetRange);
			
			// Header
			$header = $values[0];
			
			// Data
			$data = [];
			for ($i = 1; $i < count($values); $i++) {
				$data[] = $values[$i];
			}
			
			$this->_spreadsheet = [
				'header' => $header,
				'data' => $data,
			];
		}
		return $this->_spreadsheet;
	}
	
	protected function getSectionColumnRange($header, $divider) {
		$columnStartIndex = array_search($divider, $header);
		
		if ($columnStartIndex !== false) {
			for ($i = $columnStartIndex; $i < count($header) && trim($header[$i]) != ''; $i++) { if ($i > 500) throw new \Exception('t'); };
			$columnEndIndex = $i;
			return [$columnStartIndex, $columnEndIndex];
		}
		return false;
	}
	
	protected function checkIfValidRow($row) {
		return trim((string) $row[1]) == '1' && $this->checkDate2($row[0]);
	}
	
	protected function getSectionByIndex($header, $rows, $startColumnIndex, $endColumnIndex) {
		$section = [];
		foreach ($rows as $rowIndex => $row) {
			for ($i = $startColumnIndex; $i <= $endColumnIndex; $i++) {
				if ($this->checkIfValidRow($row)) {
					$section[$rowIndex][$header[$i]] = $row[$i];
				}
			}
		}
		return $section;
	}
	
	protected function getSection($header, $divider, $rows) {
		if (!isset($divider)) return;
		
		if (list($columnStartIndex, $columnEndIndex) = $this->getSectionColumnRange($this->getHeader(), $divider)) {
			return $this->getSectionByIndex($header, $rows, $columnStartIndex + 1, $columnEndIndex -1);
		}
	}
	
	protected function divideRowToSections($header, $row) {
		$row = array_pad($row, count($header), null);
		$processed = [];
		
		foreach ($this->columnDividers as $groupName => $divider) {
			$section = $this->getSection($this->getHeader(), $divider, [$row]);
			$processed[$groupName] = $section[0]; // Get first row of section return
		}
		return $processed;
	}
	
	protected function checkDate2($date) {
		if (!isset($date) || trim($date) == '') return false;
		
		$now = new DateTime;
		return new DateTime($date) < $now;
	}
	
	protected function getAccessToken() {
		return self::retrieveAccessToken();
	}
	
	protected function getCache() {
		return Yii::$app->cache;
	}
	
	protected function getGoogle() {
		Yii::$app->google->setAccessToken($this->retrieveAccessToken());
		return Yii::$app->google;
	}
}