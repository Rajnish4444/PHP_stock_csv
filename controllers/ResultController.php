<?php


class ResultController {
	
	
	private $_stockName;
	private $_startDate;
	private $_endDate;
	private $_csvPath;
	private $_csvArray;
	
	public function __construct(){
	
	}
	
	
	public function setInputs($request){
		$this->_stockName = $request->stock_name;
		$this->_startDate = $request->start_date;
		$this->_endDate = $request->end_date;
	}
	
	public function storeCSV($array){
			
		$path = 'storage/csv/';
		$fileName = 'stock_file_'.strtotime("now").'.csv';
		$fullPath = $path.$fileName;
		
		$filteredArray = [];
		
		foreach ($array as $key => $value) {
			$filteredArray[$key] = array_values(array_filter($value));
		}
		
		$file = fopen($fullPath,"w");
		foreach ($filteredArray as $line)
		{
		  fputcsv($file, $line);
		}
		fclose($file);
		
		$this->_csvPath = $fullPath;
		$this->_csvArray = $filteredArray;
	}
	
	
	public function filterByStockName(){
		
		
		$i = 0;
		$manufacturer_key = null;
		$needles = array($this->_stockName);
		$results = array();
		$columns = array();
		
		$path = $this->_csvPath;
		
		if(($handle = fopen($path, 'r')) !== false) {
		    while(($data = fgetcsv($handle, 4096, ',')) !== false) {
		        if($i == 0)  {
		            // sets the key where column to search
		            $columns = $data;
		            $i++; $manufacturer_key = array_search('stock_name', $data);
		        } else {
					
		            foreach($needles as $needle) {
		                if(stripos($data[$manufacturer_key], $needle) !== false) {
		                    $results[] = $data;
		                }
		            }
		        }
		    }
		    fclose($handle);
		}

		array_unshift($results, $columns);
		
		
		$result = $this->filterDateRange($results);
		
		if(!empty($result))
		{
			$res = $result;
			$emptyResult = 	0;
		}
		else
		{
			$res = $results;
			$emptyResult = 	1;
		}
	
		$this->_csvArray =  $res;
		
		return [
			'result' => $res,
			'emptyResult' => $emptyResult,
			'stockName' =>$this->_stockName
		];
		
	}
	
	
	private function filterDateRange($array){
		
		$startDate= strtotime($this->_startDate. ' -1 day');
		$endDate = strtotime($this->_endDate);
		
		$key = $this->getArrayKey('date');
				
		$startDate = array_filter($array, function ($val) use ($startDate,$key) {
		    return  strtotime($val[$key]) > $startDate;
		});
		
		$filteredArray = array_filter($startDate, function ($val) use ($endDate,$key) {
		    return  strtotime($val[$key]) < $endDate;
		});
		// $this->debug_to_console($filteredArray);
		
		return $filteredArray;
		
	}
	
	public function getArrayKey($key){
		
		$i = 0;
		$keyVal = null;
			
		$path = $this->_csvPath;
		
		if(($handle = fopen($path, 'r')) !== false) {
		    while(($data = fgetcsv($handle, 4096, ',')) !== false) {
		        if($i == 0)  {
		            $i++; $keyVal = array_search($key, $data);
		        }
		    }
		    fclose($handle);
		}
		
		return $keyVal;
	}
	
	public function calcValues(){
		
		$price = $this->getArrayKey('price');
		$date = $this->getArrayKey('date');
		$aValues = [];
		
		usort($this->_csvArray, function($a, $b) {
			  $date = $this->getArrayKey('date');
			  return ($a[$date] < $b[$date]) ? -1 : 1;
			});
		
		foreach ($this->_csvArray as $key => $value)
		{
			$aValues[$key] = $value[$price];
		}
		array_pop($aValues);
		
		$stdDev= $this->meanStdDev($aValues);
		$lossProfit = $this->lossProfit($aValues);
		
		return [
			'maximumDate' => $lossProfit['maximumDate'],
			'minimumDate' => $lossProfit['minimumDate'],
			'maxDiff' => $lossProfit['maxDiff'],
			'mean' => $stdDev['mean'],
			'stdDev' => $stdDev['stdDev']
		];
	}
	
	public function meanStdDev($aValues){
		$fMean = array_sum($aValues) / count($aValues);
		
		$fVariance = 0.0;
		
		foreach ($aValues as $i)
		{
			$fVariance += pow($i - $fMean, 2);

		}
		$size = count($aValues) - 1;
		
		
		$stdDev =  (float) sqrt($fVariance)/sqrt($size);
		
		return [
			'mean' => $fMean,
			'stdDev' => $stdDev
		];
	}
	
	
	public function lossProfit($aValues){
		
		$calculated = $this->maxDiff($aValues,sizeof($aValues));
		$this->debug_to_console($calculated);
		$date = $this->getArrayKey('date');
		
		$minimumDate = ($this->_csvArray[$calculated['minValue']][$date]);
		$maximumDate = ($this->_csvArray[$calculated['maxValue']][$date]);
		
		return [
			'maximumDate' => $maximumDate,
			'minimumDate' => $minimumDate,
			'maxDiff' => $calculated['maxDiff']
		];
	
	}
	
	public function maxDiff($arr, $arr_size)
	{
	
	if($arr_size == 1)
	{
		$maxValue = key($arr);
		$minValue = key($arr);
		
		return [
			'maxValue' => $maxValue,
			'minValue' => $minValue,
			'maxDiff' => 0
		];
	}
	
	$firstValue = current($arr);
	$secondValue = current(array_slice($arr, 1, 1));
	$maxDiff = $secondValue - $firstValue;
	$maxValue = key($arr)+1;
	$minValue = key($arr);
	
	
	foreach ($arr as $key => $value)
	{
		if(is_numeric($value))
		{
			for ($j = $key+1; $j < $arr_size; $j++)
		    {
			    if ($arr[$j] - $arr[$key] > $maxDiff)
				{
					$maxValue = $j;
			        $minValue = $key;
			        $maxDiff = $arr[$j] - $arr[$key];
				}
			}
			
		}
	}
		
	return [
		'maxValue' => $maxValue,
		'minValue' => $minValue,
		'maxDiff' => $maxDiff
	];

	}

	public function debug_to_console($data) {
		$output = $data;
		if (is_array($output))
			$output = implode(',', $output);
	
		echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
	}

}
