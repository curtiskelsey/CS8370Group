<?php
//This script creates a products purchased by week dataset
	include "database_info.php";
	
	try{
		$dbh = new PDO("mysql:host=$host;dbname=$database;charset=utf8",$username,$password);
		$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}catch(PDOException $e){
		print $e->getMessage();
		die();
	}
	
	$sql="SELECT product_id,COUNT(*) AS purchases,WEEK(trans_datetime) AS week FROM new_table
		  GROUP BY product_id,WEEK(trans_datetime)";
	$sth=$dbh->prepare($sql);
	
	ini_set('memory_limit', '1024M');
				
	try{
		$sth->execute();
		$results = $sth->fetchAll(PDO::FETCH_ASSOC);
	}catch(PDOException $e){
		print $e->getMessage()."\n";
		die();
	}
	
	$dbh->beginTransaction();
	$sql="INSERT INTO weekly_purchases (product_id,purchases,week) VALUES (:prod,:pur,:week)";
	$sth=$dbh->prepare($sql);
	
	$fh = @fopen('product_week_clustering.tsv','w');
	fwrite($fh,"product\tpurchases\tweek\n");
	foreach($results as $r){
		try{
			$sth->bindValue(':prod',$r['product_id'],PDO::PARAM_INT);
			$sth->bindValue(':pur',$r['purchases'],PDO::PARAM_INT);
			$sth->bindValue(':week',$r['week'],PDO::PARAM_INT);
			$sth->execute();
		}catch(PDOException $e){
			print $e->getMessage()."\n";
			die();
		}
		fwrite($fh,$r['product_id']."\t".$r['purchases']."\t".$r['week']."\n");
	}
	$dbh->commit();
	fclose($fh);
	echo "Done!";
?>