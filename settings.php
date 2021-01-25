<?php
//check whether the session is set
include_once "utility.php";
checkSession();

include_once "database/DB.const.php";
include_once "database/Table.const.php";
include_once "database/Column.const.php";
include_once "database/Database.cls.php";
include_once "database/DbTable.cls.php";
include_once "database/DbTableQuery.cls.php";
include_once "database/DbTableOperator.cls.php";

//get the user from the session table
$userId = getUserIdFromSessionId();

$properties['columns'] = Column::ID.",".Column::PASSWORD.",".Column::API_KEY;
$properties['condition'] = "WHERE id = ".$userId;
$properties['orderBy'] = "";
$properties['limit'] = "LIMIT 1";
$database = new Database(DB::INFO, DB::USER, DB::PASS);
$dbTable = new DbTable($database, Table::USERS_TB); 
$dbTableQuery = new DbTableQuery($properties);
$dbTableOperator = new DbTableOperator();
$row = $dbTableOperator->read($dbTable, $dbTableQuery, new DbPrepareResult());

$apiKey = "";

if($row == null){
	echo "User not found";
	exit;
}else{
	$apiKey = $row[0][Column::API_KEY];
}

$savedMsg = "";

if($_SERVER['REQUEST_METHOD'] == 'POST'){

	if(isset($_POST['apiKey'])){

		$equality = Column::API_KEY."=?";
		$values[] = $apiKey = md5(uniqid(rand(), true));

		$condition = "WHERE id=$userId";

		$properties['equality'] = $equality;
		$properties['values'] = $values;
		$properties['condition'] = $condition;

		$database = new Database(DB::INFO, DB::USER, DB::PASS);
		$dbTable = new DbTable($database, Table::USERS_TB); 
		$dbTableQuery = new DbTableQuery($properties);
		$dbTableOperator = new DbTableOperator();
		$dbTableOperator->update($dbTable, $dbTableQuery);

		$savedMsg = "API Key Updated successfully!";
	}else if(isset($_POST['deleteAccount'])){

		$properties['condition'] =  "WHERE id = $userId";
		$database = new Database(DB::INFO, DB::USER, DB::PASS);
		$dbTable = new DbTable($database, Table::USERS_TB); 
		$dbTableQuery = new DbTableQuery($properties);
		$dbTableOperator = new DbTableOperator();
		$dbTableOperator->delete($dbTable, $dbTableQuery);

		$properties['condition'] =  "WHERE user_id = $userId";
		$database = new Database(DB::INFO, DB::USER, DB::PASS);
		$dbTable = new DbTable($database, Table::SESSION_TB); 
		$dbTableQuery = new DbTableQuery($properties);
		$dbTableOperator = new DbTableOperator();
		$dbTableOperator->delete($dbTable, $dbTableQuery);

		$properties['condition'] =  "WHERE user_id = $userId";
		$database = new Database(DB::INFO, DB::USER, DB::PASS);
		$dbTable = new DbTable($database, Table::PROJECTS_TB); 
		$dbTableQuery = new DbTableQuery($properties);
		$dbTableOperator = new DbTableOperator();
		$dbTableOperator->delete($dbTable, $dbTableQuery);

		$properties['condition'] =  "WHERE user_id = $userId";
		$database = new Database(DB::INFO, DB::USER, DB::PASS);
		$dbTable = new DbTable($database, Table::POSTS_TB); 
		$dbTableQuery = new DbTableQuery($properties);
		$dbTableOperator = new DbTableOperator();
		$dbTableOperator->delete($dbTable, $dbTableQuery);

		$properties['condition'] =  "WHERE user_id = $userId";
		$database = new Database(DB::INFO, DB::USER, DB::PASS);
		$dbTable = new DbTable($database, Table::DATA_TB); 
		$dbTableQuery = new DbTableQuery($properties);
		$dbTableOperator = new DbTableOperator();
		$dbTableOperator->delete($dbTable, $dbTableQuery);

		setcookie('session_id', '', time() - 60);
		unset($_SESSION['session_id']);
		header('Location:index.php');
	}
}
?>

<!DOCTYPE HTML>
<html>
<head>
	<title>Settings</title>
	<link href="css/rex.css" rel="stylesheet" />
	<link href="css/utils.css" rel="stylesheet" />
</head>
<body>
	<div class="rex-border-bottom-gray rex-height-50px">
		<div class="custom-responsive-container rex-line-height-50px rex-fs-normal">
			<a href="projects.php"><span class="rex-center-relative-div-vertical rex-hover">App name</span></a>
		</div>
	</div>
	<div class="rex-border-bottom-gray rex-height-50px">
		<div class="rex-center-relative-div-vertical rex-center-text">General Settings</div>
	</div>
	<div>
		<div class="rex-space-32px"></div>
		<div class="rex-center-text">
			<span class="rex-color-green"><?php echo $savedMsg;?></span>
		</div>

		<form method="post" action="settings.php" class="rex-responsive-center-div-2 rex-center-div-horizontal">
			<div class="rex-space-32px"></div>
			<label for="apiKey" class="rex-fs-extra-small rex-weight-bold">API Key</label>
			<input type="hidden" name="apiKey"/>
			<div class="rex-responsive-paragragh rex-input-primary rex-width-100pp rex-pad8px rex-curDiv-4px rex-mt-8px rex-fs-small"><?php echo $apiKey; ?></div>

			<div class="rex-space-32px"></div>
			<button class="rex-responsive-btn rex-btn-primary rex-pad16px rex-color-white rex-curDiv-8px rex-fs-normal rex-width-100pp"/>Regenerate</button>
			<div class="rex-space-32px"></div>
		</form>

		<div class="rex-space-32px rex-border-bottom-gray"></div>

		<form method="post" action="settings.php" class="rex-responsive-center-div-2 rex-center-div-horizontal">
			<div class="rex-space-32px"></div>
			<label for="deleteAccount" class="rex-fs-extra-small rex-weight-bold rex-color-red">Delete Account</label>
			<div class="rex-space-8px"></div>
			<input type="hidden" name="deleteAccount" value=" "/>
			<button class="rex-responsive-btn rex-btn-secondary rex-pad16px rex-color-red rex-border-red rex-curDiv-8px rex-fs-normal rex-width-100pp">Delete Account</button>
			<div class="rex-space-32px"></div>
		</form>
		<div class="rex-space-32px"></div>
		<div class="rex-space-32px"></div>
		<div class="rex-space-32px"></div>
	</div>
</body>
</html>