<?php
	session_start();

	//check whether the session is set
	function checkSession(){
		if(!isset($_SESSION['session_id'])){
			echo "session not set";
			if(!isset($_COOKIE['session_id'])){
				header('Location:index.php');
			}else{
				echo "setting the session";
				$_SESSION['session_id'] = $_COOKIE['session_id'];
			}
		}
	}

	function getUserIdFromSessionId(){
		$properties['columns'] = Column::USER_ID;
		$properties['condition'] = "WHERE session_id = '".$_SESSION['session_id']."'";
		$properties['orderBy'] = "";
		$properties['limit'] = "";
		$database = new Database(DB::INFO, DB::USER, DB::PASS);
		$dbTable = new DbTable($database, Table::SESSION_TB); 
		$dbTableQuery = new DbTableQuery($properties);
		$dbTableOperator = new DbTableOperator();
		$row = $dbTableOperator->read($dbTable, $dbTableQuery, new DbPrepareResult());

		if($row == null){
			setcookie('session_id', '', time() - 60);
			header('Location:index.php');
		}else{
			return $row[0][Column::USER_ID];
		}
	}

	function getMaxOrderInCategoryOfDataFor($post_id){
		$properties['columns'] = "MAX(".Column::ORDER_IN_CATEGORY.") AS max";
		$properties['condition'] = "WHERE post_id = $post_id";
		$properties['orderBy'] = "";
		$properties['limit'] = "";
		$database = new Database(DB::INFO, DB::USER, DB::PASS);
		$dbTable = new DbTable($database, Table::DATA_TB); 
		$dbTableQuery = new DbTableQuery($properties);
		$dbTableOperator = new DbTableOperator();
		$row = $dbTableOperator->read($dbTable, $dbTableQuery, new DbPrepareResult());

		if($row == null){
			return 0;
		}else{
			return $row[0]['max'];
		}
	}

	function getAppName(){
		return "Central Database";//Remote Data Manager
	}
?>