<?php

include 'controllers/ResultController.php';


$request = (object)$_POST;

$result = new ResultController();

#setting Inputs
$result->setInputs($request);


#storing csv and getting file path
$tmpName = $_FILES['file']['tmp_name'];
$csvAsArray = array_map('str_getcsv', file($tmpName));

$filteredArray = [];

foreach ($csvAsArray as $key => $value) {
	$filteredArray[$key] = array_values(array_filter($value));
}

$result->storeCSV($csvAsArray);

#filtering values
$res = (object) $result->filterByStockName();

$calc = (object) $result->calcValues();

?>
<div class="col-md-6 offset-md-3 mt-5">
	
	<a class="mt-3 d-flex" href="/">Go back</a>
	<h1>Result Of Analysed Data	 of <?php echo "'".$res->stockName."'" ?></h1>
	
	<hr>
	
	<?php
	
	 if(!empty($res->emptyResult))
	 {
		echo '<h4 class="alert alert-warning">No Data Found. Showing Result of All Date Range.</h4>';
	 }
	
	 ?>
	 
	
	<div class="form-group row">
	    <label for="mean_value" class="col-sm-4 col-form-label">Stock Mean Price</label>
	    <div class="col-sm-8">
	      <input type="number" class="form-control" id="mean_value" value="<?php echo  $calc->mean ?>" placeholder="Mean Value" disabled>
	    </div>
  	</div>
	
	<div class="form-group row">
	    <label for="" class="col-sm-4 col-form-label">Standard Deviation</label>
	    <div class="col-sm-8">
	      <input type="number" class="form-control" id="mean_value" value="<?php echo  $calc->stdDev ?>" placeholder="-" disabled>
	    </div>
  	</div>
	
	<div class="form-group row">
	    <label for="" class="col-sm-4 col-form-label">Best Date to buy Shares</label>
	    <div class="col-sm-8">
	      <input type="text" class="form-control" id="mean_value" value="<?php echo  $calc->minimumDate ?>" placeholder="" disabled>
	    </div>
  	</div>
	
	<div class="form-group row">
	    <label for="" class="col-sm-4 col-form-label">Best Date to sell Shares</label>
	    <div class="col-sm-8">
	      <input type="text" class="form-control" id="mean_value" value="<?php echo  $calc->maximumDate ?>" placeholder="" disabled>
	    </div>
  	</div>
	
	<div class="form-group row">
	    <label for="" class="col-sm-4 col-form-label">Maximum Profit</label>
	    <div class="col-sm-8">
	      <input type="text" class="form-control" id="mean_value" value="<?php echo  $calc->maxDiff ?>" placeholder="" disabled>
	    </div>
  	</div>
		
	
	<div class="form-group">
		<label> More Information </label>
		<table class="table table-striped">
			  
			 <?php
			 
			 echo '<thead>';
			 echo '<tr>';
			 echo '<th>'.$filteredArray[0][0].'</th>';
			 echo '<th>'.$filteredArray[0][1].'</th>';
			 echo '<th>'.$filteredArray[0][2].'</th>';
			 echo '<th>'.$filteredArray[0][3].'</th>';
			 echo  '</tr>';
			 echo '</thead>';
			 echo  '<tbody>';
			 
			 foreach ($res->result as $key => $value) {
				
				if($key != 0)
				{
					echo '<tr>';
					echo  '<th scope="row">'.$key.'</th>';
					echo '<td>'.$value[1].'</td>';
					echo '<td>'.$value[2].'</td>';
					echo '<td>'.$value[3].'</td>';
					echo '</tr>';
				}
			 }
			 ?>
			 
		  </tbody>
		</table>
	</div>
	
	
</div>
