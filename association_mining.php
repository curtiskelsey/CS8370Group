<?php

	
	
	$fh = @fopen('frequent_purchase_products.tsv','w');
	fwrite($fh,"product\tpurchases\n");
	foreach($results as $r){
		fwrite($fh,$r['product_id']."\t".$r['purchases']."\n");
	}
	fclose($fh);


?>