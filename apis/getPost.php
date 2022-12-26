<?php

$response = array();

if(isset($_GET['apiKey']) && isset($_GET['slug'])){
	include_once "../database/DB.const.php";
	include_once "../database/Table.const.php";
	include_once "../database/Column.const.php";
	include_once "../database/Database.cls.php";
	include_once "../database/DbTable.cls.php";
	include_once "../database/DbTableQuery.cls.php";
	include_once "../database/DbTableOperator.cls.php";

	$apiKey = $_GET['apiKey'];
	$slug = $_GET['slug'];

//convert apikey to user id
	$properties['columns'] = Column::ID;
	$properties['condition'] = "WHERE api_key='$apiKey'";
	$properties['orderBy'] = "";
	$properties['limit'] = "";
	$database = new Database(DB::INFO, DB::USER, DB::PASS);
	$dbTable = new DbTable($database, Table::USERS_TB); 
	$dbTableQuery = new DbTableQuery($properties);
	$dbTableOperator = new DbTableOperator();
	$row = $dbTableOperator->read($dbTable, $dbTableQuery, new DbPrepareResult());

	if($row == null){
		$response['status'] = "failed";
		$response['message'] = "User not found";
	}else{
		$userId = $row[0][Column::ID];

		$properties['columns'] = Column::ID.",".Column::TITLE.",".Column::SLUG.",".Column::DESCRIPTION.",".Column::METADATA;
		$properties['condition'] = "WHERE slug = '$slug' AND user_id = $userId";
		$properties['orderBy'] = "";
		$properties['limit'] = "";
		$database = new Database(DB::INFO, DB::USER, DB::PASS);
		$dbTable = new DbTable($database, Table::POSTS_TB); 
		$dbTableQuery = new DbTableQuery($properties);
		$dbTableOperator = new DbTableOperator();
		$row = $dbTableOperator->read($dbTable, $dbTableQuery, new DbPrepareResult());

		if($row == null){
			$response['status'] = "failed";
			$response['message'] = "Not found";
		}else{			
			$result = [];
			$result['post_id'] = $row[0][Column::ID];
			$result['title'] = $row[0][Column::TITLE];
			$result['slug'] = $row[0][Column::SLUG];
			$result['description'] = $row[0][Column::DESCRIPTION];
			$result['metadata'] = $row[0][Column::METADATA];

			$response['status'] = "success";
			$response['data'] = $result;
		}
	}
}else{
	$response['status'] = "failed";
	$response['message'] = "Invalid input";
}

echo json_encode($response);
?>