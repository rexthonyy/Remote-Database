<?php 
include_once "../database/DB.const.php";
include_once "../database/Table.const.php";
include_once "../database/Column.const.php";
include_once "../database/Database.cls.php";
include_once "../database/DbTable.cls.php";
include_once "../database/DbTableQuery.cls.php";
include_once "../database/DbTableOperator.cls.php";

function read(){
    echo "<h1>READ</h1>";
    
    $properties['columns'] = Column::ID.",".Column::PASSWORD.",".Column::API_KEY;
    $properties['condition'] = "";
    $properties['orderBy'] = "";
    $properties['limit'] = "";
    $database = new Database(DB::INFO, DB::USER, DB::PASS);
    $dbTable = new DbTable($database, Table::USERS_TB); 
    $dbTableQuery = new DbTableQuery($properties);
    $dbTableOperator = new DbTableOperator();
    $row = $dbTableOperator->read($dbTable, $dbTableQuery, new DbPrepareResult());
    
    echo "<pre>";
    print_r($row);
}

function insert(){
     echo "<h1>INSERT</h1>";
    //insert
    $values = [];
    $values[] = 'anthony';
    $values[] = 'anthonyKey';
    
    $properties['columns'] = "(".Column::PASSWORD.", ".Column::API_KEY.")";
    $properties['values'] = $values;
    $properties['tokens'] = "(?, ?)";
    
    $database = new Database(DB::INFO, DB::USER, DB::PASS);
    $dbTable = new DbTable($database, Table::USERS_TB); 
    $dbTableQuery = new DbTableQuery($properties);
    $dbTableOperator = new DbTableOperator();
    $dbTableOperator->insert($dbTable, $dbTableQuery);
}

function update(){
     echo "<h1>UPDATE</h1>";
    $equality = Column::PASSWORD."=?, ".Column::API_KEY."=?";
	$values[] = "Samuel";
	$values[] = "Samneulkey";

	$condition = "WHERE id=1";

	$properties['equality'] = $equality;
	$properties['values'] = $values;
	$properties['condition'] = $condition;

	$database = new Database(DB::INFO, DB::USER, DB::PASS);
	$dbTable = new DbTable($database, Table::USERS_TB); 
	$dbTableQuery = new DbTableQuery($properties);
	$dbTableOperator = new DbTableOperator();
	$dbTableOperator->update($dbTable, $dbTableQuery);
}

function delete(){
     echo "<h1>DELETE</h1>";
    $properties['condition'] =  "WHERE id = 1";
	$database = new Database(DB::INFO, DB::USER, DB::PASS);
	$dbTable = new DbTable($database, Table::USERS_TB); 
	$dbTableQuery = new DbTableQuery($properties);
	$dbTableOperator = new DbTableOperator();
	$dbTableOperator->delete($dbTable, $dbTableQuery);
}

?>