<?php
//This script creates a customer vip data.tsv file
	include "database_info.php";
	
	try{
		$dbh = new PDO("mysql:host=$host;dbname=$database;charset=utf8",$username,$password);
		$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}catch(PDOException $e){
		print $e->getMessage();
		die();
	}
	
	$sql="SELECT customer_id,product_id FROM new_table AS t CROSS JOIN (
		SELECT MAX(trans_datetime) AS trans_datetime, customer_id FROM new_table GROUP BY customer_id 
		) AS sq USING (trans_datetime,customer_id)";
	$sth=$dbh->prepare($sql);
	
	ini_set('memory_limit', '1024M');
				
	try{
		$sth->execute();
		$results = $sth->fetchAll(PDO::FETCH_ASSOC);
	}catch(PDOException $e){
		print $e->getMessage()."\n";
		die();
	}
	
	$fh = @fopen('last_purchases.tsv','w');
	fwrite($fh,"product\tpurchases\n");
	foreach($results as $r){
		fwrite($fh,$r['customer_id']."\t".$r['product_id']."\n");
	}
	fclose($fh);
?>