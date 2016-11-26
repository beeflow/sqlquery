# Simple SQL query manager

A simple SQL query manager with option to secure queries by setting parameter type. 

It uses classes which represents siple var types as string, integer etc... and own classes 
like secureString, email etc...

To better secure queries, you can create your own var types classes, for example password or phone

## Install

### composer

    $ composer require "beeflow/sqlquerymanager:dev-master"

### GIT
    
    $ git clone https://github/beeflow/

## Examples

### SQL query example:

	SELECT example1 FROM exampleTable WHERE example = {value->secureString}

### Example with new method of calling SQL files

    `<?php
    
    	use Beeflow\SQLQueryManager\SQLQuery
    
    	try {
    	    $query = new SQLQuery();
            $query->sqlExample([
                    'value'                  => 'TEST_VALUE',
                    'value2'                 => 11,
                    'vatno'                  => '1111111111',
                    'valueArrayWithoutAtype' => array('one', 'two', 'tree')
            ]);
             
            echo $query->getQuery();
    	} catch (Exception $ex) {
            echo $ex->getMessage();
    	}`
    
    
### Example with a correct data:

	`<?php

	use Beeflow\SQLQueryManager\SQLQuery

	try {
	    $query = new SQLQuery("sqlExample");
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
	}`

### Example with incorrect data:

    `<?php

    use Beeflow\SQLQueryManager\SQLQuery

	try {
	    $newQuery = new SQLQuery("sqlExample");
	    $newQuery->value = 'TEST_VALUE';
	    $newQuery->value2 = 11;

	    // incorrect polish vat no
	    $newQuery->vatno = '1212111211';

	    $query->valueArrayWithoutAtype = array('one', 'two', 'tree');
	    $query->valueWithoutParamType = "value Without Param Type";
	    echo $newQuery->getQuery();
	} catch (Exception $ex) {
	    echo $ex->getMessage();
	}`

### Example with conditioned value:

    `<?php

    use Beeflow\SQLQueryManager\SQLQuery

	try {
	    $query = new SQLQuery("sqlExample");
	    $query->value = 'TEST_VALUE';

	    // if you set a string value it will be set as 0 (zero) because (integer)'ddd' = 0 (zero)
	    $query->value2 = 11;
	    $query->vatno = '1111111111';

	    $query->valueArrayWithoutAtype = array('one', 'two', 'tree');
	    $query->valueWithoutParamType = "value Without Param Type";

	    // condition !empty()
	    $query->notEmptyValue = 1;

	    echo $query->getQuery();
	} catch (Exception $ex) {
	    echo $ex->getMessage();
	}`
