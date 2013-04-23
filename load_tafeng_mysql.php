<?php
//This script loads the data into a local mysql database
	include "database_info.php";
	
	try{
		$dbh = new PDO("mysql:host=$host;dbname=$database;charset=utf8",$username,$password);
		$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}catch(PDOException $e){
		print $e->getMessage();
		die();
	}
	
	$sql="INSERT INTO new_table (trans_datetime,customer_id,age,location,prod_subclass,product_id,quantity,asset,sales_price) 
		  VALUES (:trans_datetime,:cust_id,:age,:location,:prod_subclass,:product_id,:quantity,:asset,:sales_price)";
	$sth=$dbh->prepare($sql);
	
	ini_set('memory_limit', '1024M');
	$files = Array('TaFengDataSet/D01');//,'TaFengDataSet/D02','TaFengDataSet/D11','TaFengDataSet/D12');
	
	foreach($files as &$file){
		echo "Processing $file...\n";
		$fh = @fopen($file,'r');
		
		if($fh){
			while(($buffer=fgets($fh))!==false){
				$buffer = explode(';',$buffer);
				foreach($buffer as &$b){
					$b=trim($b);
				}
				
				try{
					$sth->bindValue(':trans_datetime',$buffer[0],PDO::PARAM_STR);
					$sth->bindValue(':cust_id',$buffer[1],PDO::PARAM_INT);
					$sth->bindValue(':age',$buffer[2],PDO::PARAM_STR);
					$sth->bindValue(':location',$buffer[3],PDO::PARAM_STR);
					$sth->bindValue(':prod_subclass',$buffer[4],PDO::PARAM_INT);
					$sth->bindValue(':product_id',$buffer[5],PDO::PARAM_INT);
					$sth->bindValue(':quantity',$buffer[6],PDO::PARAM_INT);
					$sth->bindValue(':asset',$buffer[7],PDO::PARAM_INT);
					$sth->bindValue(':sales_price',$buffer[8],PDO::PARAM_INT);
					$sth->execute();
					print_r($dbh->errorInfo());
				}catch(PDOException $e){
					print $e->getMessage()."\n";
					die();
				}
			}
			if(!feof($fh)){
				echo "Error: unexpected fgets fail.\n";
			}
			fclose($fh);
		}
	}
	echo "Done!";
?>