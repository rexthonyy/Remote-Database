<?php
include_once "../database/DB.const.php";
include_once "../database/Table.const.php";
include_once "../database/Column.const.php";
include_once "../database/Database.cls.php";
include_once "../database/DbTable.cls.php";
include_once "../database/DbTableQuery.cls.php";
include_once "../database/DbTableOperator.cls.php";

$properties['columns'] = Column::ID.",".Column::EXPIRE;
$properties['condition'] = "";
$properties['orderBy'] = "";
$properties['limit'] = "";
$database = new Database(DB::INFO, DB::USER, DB::PASS);
$dbTable = new DbTable($database, Table::SESSION_TB); 
$dbTableQuery = new DbTableQuery($properties);
$dbTableOperator = new DbTableOperator();
$row = $dbTableOperator->read($dbTable, $dbTableQuery, new DbPrepareResult());

$status = "";

if($row != null){

	$isDeletedSession = false;

	foreach($row as $session){
		$id = $session[Column::ID];
		$expire = $session[Column::EXPIRE];

		if(time() > $expire){

			$isDeletedSession = true;

			$properties['condition'] =  "WHERE id = $id";
			$database = new Database(DB::INFO, DB::USER, DB::PASS);
			$dbTable = new DbTable($database, Table::SESSION_TB); 
			$dbTableQuery = new DbTableQuery($properties);
			$dbTableOperator = new DbTableOperator();
			$dbTableOperator->delete($dbTable, $dbTableQuery);
		}
	}

	if($isDeletedSession){
		$status = "expired sessions deleted";
	}else{
		$status = "active sessions have not expired";
	}
}else{
	$status = "no active sessions";
}

echo $status;
?>