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
	public $dateFormat = 'Y-m-d h:i:s a';
	
	public $columnDividers = [
		'date' => [0, 1],
		'malaysia' => 'Malaysia',
		'penang' => 'Penang',
		'states' => 'States',
	];
	
	public $cacheDuration = 3600 * 24 * 7;
	
	protected $storePath = '@root/config/google-client.php';
	protected $_spreadsheet;
	protected $_validRowStack;
	
	public function storeAccessToken($token) {
		File::storeArray(Yii::getAlias($this->storePath), $token);
	}
	
	public function retrieveAccessToken() {
		return File::loadArray(Yii::getAlias($this->storePath));
	}
	
	public function getPenangStatistic($refresh = false) {
		return $this->getStatistic(__CLASS__ . '. ' . __METHOD__, function() {
			$divider = isset($this->columnDividers['penang']) ? $this->columnDividers['penang'] : null;
			$section = $this->getSection($this->getHeader(), $divider, $this->getData());
			$dateColumn = $this->getSectionByIndex($this->getHeader(), $this->getData(), 0, 1, false);
			
			$combined = [];
			$lastestValidRowIndex = 0;
			foreach ($section as $i => $row) {
				$data = ArrayHelper::merge($section[$i], $dateColumn[$i]);
				if ($this->checkIfValidProcessedData($section[$i])) {
					$combined[$i] = $data;
					$lastestValidRowIndex = $i;
				}
			}
			if (count($combined)) {
				return [
					'timestamp' => time(),
					'date' => (new DateTime(implode(' ', $dateColumn[$lastestValidRowIndex]))),
					'statistic'=> $combined,
				];
			}
		}, $refresh);
	}
	
	public function getLatestStatistic($refresh = false) {
		return $this->getStatistic(__CLASS__ . '. ' . __METHOD__, function() {
			$offset = 0;
			do {
				$stat = $this->divideRowToSections($this->getHeader(), $this->getLastValidRow($offset++));
			} while(isset($stat) && !$this->checkIfValidProcessedData($stat));
			$stat['date'] = (new DateTime(implode(' ', $stat['date'])));
			$stat['timestamp'] = time();
			return $stat;
		}, $refresh);
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
	
	protected function getStatistic($name, $callback, $refresh = false) {
		$name = $this->registerCacheName($name);
		$cached = $this->cache->get($name);

		if ($refresh || $cached === false) {
			try {
				$data = call_user_func_array($callback, []);
				
				if (isset($data)) {
					$this->cache->set($name, $data, $this->cacheDuration);
					return $data;
				}
			} catch (\Exception $ex) {
				throw $ex;
			}
		}
		
		return $cached !== false ? $cached : null;
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
	
	protected function getLastValidRow($offset = 0) {
		if (!isset($this->_validRowStack)) {
			$this->_validRowStack = [];
			foreach ($this->getData() as $index => $row) {
				if ($this->checkIfValidRow($row)) {
					array_push($this->_validRowStack, $index);
				}
			}
		}
		
		if ($offset < count($this->_validRowStack)) { 
			$lastValidRowIndex = $this->_validRowStack[count($this->_validRowStack) - 1 - $offset];
			return $this->getRow($lastValidRowIndex);
		}
	}
	
	protected function getRow($index) {
		$data = $this->getData();
		return $data[$index];
	}
	
	protected function getData() {
		if (!isset($this->spreadsheet['data'])) {
			$this->_spreadsheet = $this->getSpreadSheet(true);
		}
		return $this->spreadsheet['data'];
	}
	
	protected function getSpreadSheet($refresh = false) {
		if (!$this->hasAccessToken()) throw new \Exception('Access token is not exist. ');
		
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
		if (is_array($divider)) return $divider;
		
		$columnStartIndex = array_search($divider, $header);
		
		if ($columnStartIndex !== false) {
			for ($i = $columnStartIndex; $i < count($header) && trim($header[$i]) != ''; $i++) { if ($i > 500) throw new \Exception('t'); };
			$columnEndIndex = $i;
			//throw new \Exception($divider.', '.$columnStartIndex.', '.$columnEndIndex);
			return [$columnStartIndex + 1, $columnEndIndex -1];
		}
		return false;
	}
	
	protected function checkIfValidProcessedData($processed) {
		return !$this->containEmpty($processed);
	}
	
	protected function containEmpty($values) {
		if (is_array($values)) {
			foreach ($values as $key => $v) {
				if ($this->containEmpty($v)) {
					return true;
				}
			}
			return false;
		} else {
			return !isset($values) || trim($values) == '';
		}
	}
	
	protected function checkIfValidRow($row) {
		return trim((string) $row[$this->getColumnIndexByName('Upload', 2)]) == '1' && $this->checkDate2($row[$this->getColumnIndexByName('Date', 0)]);
	}
	
	protected function getColumnIndexByName($name, $defaultValue) {
		$index = array_search($name, $this->getHeader());
		return $index !== false ? $index : $defaultValue;
	}
	
	protected function getSectionByIndex($header, $rows, $startColumnIndex, $endColumnIndex, $includeInvalid = false) {
		$section = [];
		foreach ($rows as $rowIndex => $row) {
			for ($i = $startColumnIndex; $i <= $endColumnIndex; $i++) {
				if ($this->checkIfValidRow($row) || $includeInvalid) {
					$section[$rowIndex][$header[$i]] = $row[$i];
				}
			}
		}
		return $section;
	}
	
	protected function getSection($header, $divider, $rows) {
		if (!isset($divider)) return;
		
		if (list($columnStartIndex, $columnEndIndex) = $this->getSectionColumnRange($this->getHeader(), $divider)) {
			return $this->getSectionByIndex($header, $rows, $columnStartIndex, $columnEndIndex);
		}
	}
	
	protected function divideRowToSections($header, $row) {
		if (!isset($row)) return;
		
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
	
	protected function hasAccessToken() {
		$token = $this->retrieveAccessToken();
		return isset($token);
	}
	
	protected function getGoogle() {
		$token = $this->retrieveAccessToken();
		if (isset($token)) Yii::$app->google->setAccessToken($token);
		return Yii::$app->google;
	}
}