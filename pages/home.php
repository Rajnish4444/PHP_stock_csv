<div class="col-md-6 offset-md-3 mt-5">
	 <h1>Stock CSV Analyzer</h1>
	 <form accept-charset="UTF-8" name="form" action="result.php" method="POST" enctype='multipart/form-data'>
		 
		 <hr>
		 <div class="form-group mt-3">
		   <label class="mr-2">Upload your CSV:</label>
		   <input type="file" id="file" accept=".csv" name="file">
		 </div>
		 <hr>
		 
	 
		 <div class="form-group">
			<label for="exampleFormControlSelect1">Stock Name</label>
			<select class="form-control" id="stock_name" name="stock_name" required="required">
				<option>Select Stock Name</option>
			</select>
		 </div>
		 
		 <input type="hidden" name="csv_data[]"  id="csv_data">
			 
		   <div class="form-group">
			 <label for="exampleInputEmail1" required="required">Data Range</label><br>
			 
			 <label>Start Date: </label>
			 <div id="start_date" class="input-group date" data-date-format="dd-mm-yyyy">
				 <input class="form-control" type="text" name="start_date" readonly />
				 <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
			 </div>
			 
			 <label>End Date: </label>
				 <div id="end_date" class="input-group date" data-date-format="dd-mm-yyyy">
					 <input class="form-control" type="text" name="end_date" readonly />
					 <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
				 </div>
				 
		   </div>
	   
	 
	   <button type="submit" class="btn btn-primary" id="submit" disabled="disabled" >Submit</button>
	   
	 </form>
 </div>
