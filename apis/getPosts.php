<?php

$response = array();

if(isset($_GET['apiKey']) && isset($_GET['projectId'])){
	include_once "../database/DB.const.php";
	include_once "../database/Table.const.php";
	include_once "../database/Column.const.php";
	include_once "../database/Database.cls.php";
	include_once "../database/DbTable.cls.php";
	include_once "../database/DbTableQuery.cls.php";
	include_once "../database/DbTableOperator.cls.php";

	$apiKey = $_GET['apiKey'];
	$projectId = $_GET['projectId'];

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

		$properties['columns'] = Column::ID.",".Column::TITLE.",".Column::DESCRIPTION.",".Column::METADATA;
		$properties['condition'] = "WHERE user_id = $userId AND project_id = $projectId";
		$properties['orderBy'] = "";
		$properties['limit'] = "";
		$database = new Database(DB::INFO, DB::USER, DB::PASS);
		$dbTable = new DbTable($database, Table::POSTS_TB); 
		$dbTableQuery = new DbTableQuery($properties);
		$dbTableOperator = new DbTableOperator();
		$row = $dbTableOperator->read($dbTable, $dbTableQuery, new DbPrepareResult());

		if($row == null){
			$response['status'] = "failed";
			$response['message'] = "No posts";
		}else{
			$posts = [];
			foreach($row as $post){
				$result = [];

				$result['post_id'] = $post[Column::ID];
				$result['title'] = $post[Column::TITLE];
				$result['description'] = $post[Column::DESCRIPTION];
				$result['metadata'] = $post[Column::METADATA];

				$posts[] = $result;
			}

			$response['status'] = "success";
			$response['data'] = $posts;
		}
	}
}else{
	$response['status'] = "failed";
	$response['message'] = "Invalid input";
}

echo json_encode($response);
?>