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
	
	$sql="SELECT customer_id,COUNT(*) AS purchases FROM new_table GROUP BY customer_id";
	$sth=$dbh->prepare($sql);
	
	ini_set('memory_limit', '1024M');
				
	try{
		$sth->execute();
		$results = $sth->fetchAll(PDO::FETCH_ASSOC);
	}catch(PDOException $e){
		print $e->getMessage()."\n";
		die();
	}
	
	$fh = @fopen('vip_data.tsv','w');
	fwrite($fh,"customer\tpurchases\n");
	foreach($results as $r){
		fwrite($fh,$r['customer_id']."\t".$r['purchases']."\n");
	}
	fclose($fh);

?>