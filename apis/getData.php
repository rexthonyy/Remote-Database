<?php

$response = array();

if(isset($_GET['apiKey']) && isset($_GET['postId'])){
	include_once "../database/DB.const.php";
	include_once "../database/Table.const.php";
	include_once "../database/Column.const.php";
	include_once "../database/Database.cls.php";
	include_once "../database/DbTable.cls.php";
	include_once "../database/DbTableQuery.cls.php";
	include_once "../database/DbTableOperator.cls.php";

	$apiKey = $_GET['apiKey'];
	$postId = $_GET['postId'];

	

//convert apikey to user id
	$properties['columns'] = Column::ID;
	$properties['condition'] = "WHERE api_key=$apiKey";
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

		$properties['columns'] = Column::ID.",".Column::ORDER_IN_CATEGORY.",".Column::KEY.",".Column::VALUE;
		$properties['condition'] = "WHERE user_id = $userId AND post_id = $postId";
		$properties['orderBy'] = "";
		$properties['limit'] = "";
		$database = new Database(DB::INFO, DB::USER, DB::PASS);
		$dbTable = new DbTable($database, Table::DATA_TB); 
		$dbTableQuery = new DbTableQuery($properties);
		$dbTableOperator = new DbTableOperator();
		$row = $dbTableOperator->read($dbTable, $dbTableQuery, new DbPrepareResult());

		if($row == null){
			$response['status'] = "failed";
			$response['message'] = "No data";
		}else{
			$dataList = [];
			foreach($row as $data){
				$result = [];

				$result['data_id'] = $data[Column::ID];
				$result['order_in_category'] = $data[Column::ORDER_IN_CATEGORY];
				$result['key'] = $data[Column::KEY];
				$result['value'] = $data[Column::VALUE];

				$dataList[] = $result;
			}

			$response['status'] = "success";
			$response['data'] = $dataList;
		}
	}
}else{
	$response['status'] = "failed";
	$response['message'] = "Invalid input";
}
echo json_encode($response);
?>