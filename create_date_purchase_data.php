<?php
//This script creates a customer vip_data.tsv file. It may be used as a template for creating other data files.

    //We increase the amount of memory this script is allowed to use from the 128MB default to 1GB
    ini_set('memory_limit', '1024M');
    
    //We increase the maximum permitted execution time of the script so that long queries can complete.
    ini_set('max_execution_time', 3000);//Max execution time is 50 minutes
    
    //Adds in the contents of the database_info.php file.
	include "database_info.php";
	
    //Trys to connect to the database so that we may query it.
	try{
		$dbh = new PDO("mysql:host=$host;dbname=$database;charset=utf8",$username,$password);
		$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//Turns on error messages
	}catch(PDOException $e){
	    //if there is an error it will be printed to the screen and the script will end
		print $e->getMessage();
		die();
	}
	
    //This variable holds the query you would like to submit to the database
	$sql="SELECT trans_datetime,COUNT(*) AS purchases FROM new_table GROUP BY trans_datetime";
	
    //We prepare the query for execution
	$sth=$dbh->prepare($sql);
	
    //Now we try to query the database and store the results			
	try{
		$sth->execute();
		$results = $sth->fetchAll(PDO::FETCH_ASSOC);
	}catch(PDOException $e){
	    //if there is an error it will be printed to the screen and the script will end
		print $e->getMessage()."\n";
		die();
	}
	
    //Now we open a file to write the results into. You may change 'vip_data.tsv' to any name of your choosing
	$fh = @fopen('date_purchase_data.tsv','w');
	
	//This line writes the names of the columns into the file. You may change 'customer' and 'purchases' to your
	//columns names
	fwrite($fh,"date\tpurchases\n");
    
    //This loop writes each one of the results to the file
	foreach($results as $r){
		fwrite($fh,str_replace("-", "", substr($r['trans_datetime'],0,10))."\t".$r['purchases']."\n");
	}
    
    //This line closes the file we were writing to
	fclose($fh);
	echo "Done";
?>