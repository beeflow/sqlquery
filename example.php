<?php

require_once 'sqlquery.php';

echo "Example with a correct data:<br>";
try {
	$query = new sqlQuery("sqlExample");
	$query->value = 'TEST_VALUE';
	
	// if you set a string value it will be set as 0 (zero) because (integer)'ddd' = 0 (zero)
	$query->value2 = 11;
	
	// polish vat no algoritm allows to use 1111111111 vat number
	// if you want to check an european vat no see:
	// http://www.phpclasses.org/package/2280-PHP-Check-if-a-European-VAT-number-is-valid.html
	$query->vatno = '1111111111';
	
	$query->valueArrayWithoutAtype = array('one', 'two', 'tree');
	$query->valueWithoutParamType = "value Without Param Type";
	
	echo $query->getQuery();
} catch (Exception $ex) {
	echo $ex->getMessage();
}

echo "<br><br>Example with incorrect data:<br>";
try {
	$newQuery = new sqlQuery("sqlExample");
	$newQuery->value = 'TEST_VALUE';
	$newQuery->value2 = 11;
	
	// incorrect polish vat no
	$newQuery->vatno = '1212111211';
	
	$query->valueArrayWithoutAtype = array('one', 'two', 'tree');
	$query->valueWithoutParamType = "value Without Param Type";
	echo $newQuery->getQuery();
} catch (Exception $ex) {
	echo $ex->getMessage();
}