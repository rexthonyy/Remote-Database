<?php
session_start();
$isError = false;

if($_SERVER['REQUEST_METHOD'] == 'POST'){

	if(isset($_POST['password'])){
		$password = htmlentities($_POST['password']);
		
		//check whether the password exists in the database
		include_once "database/DB.const.php";
		include_once "database/Table.const.php";
		include_once "database/Column.const.php";
		include_once "database/Database.cls.php";
		include_once "database/DbTable.cls.php";
		include_once "database/DbTableQuery.cls.php";
		include_once "database/DbTableOperator.cls.php";

		$properties['columns'] = Column::ID;
		$properties['condition'] = "WHERE password = '$password'";
		$properties['orderBy'] = "";
		$properties['limit'] = "";
		$database = new Database(DB::INFO, DB::USER, DB::PASS);
		$dbTable = new DbTable($database, Table::USERS_TB); 
		$dbTableQuery = new DbTableQuery($properties);
		$dbTableOperator = new DbTableOperator();
		$row = $dbTableOperator->read($dbTable, $dbTableQuery, new DbPrepareResult());
		
		if($row == null){
			$isError = true;
		}else{
			$user_id = $row[0][Column::ID];
			$session_id = md5(uniqid(rand(), true));
			$expire = time() + (60 * 60 * 60 * 24 * 7); //1 week session length

			$columns = "(".Column::USER_ID.",".Column::SESSION_ID.",".Column::EXPIRE.")";
			$tokens = "(?,?,?)";
			$values[] = $user_id;
			$values[] = $session_id;
			$values[] = $expire;

			$properties['columns'] = $columns;
			$properties['tokens'] = $tokens;
			$properties['values'] = $values;

			$database = new Database(DB::INFO, DB::USER, DB::PASS);
			$dbTable = new DbTable($database, Table::SESSION_TB); 
			$dbTableQuery = new DbTableQuery($properties);
			$dbTableOperator = new DbTableOperator();
			$dbTableOperator->insert($dbTable, $dbTableQuery);

			$_SESSION['session_id'] = $session_id;
			setcookie('session_id', $session_id, $expire, '/');

			header('Location:projects.php');
		}
	}
}
?>

<!DOCTYPE HTML>
<html>
<head>
	<title>Login</title>
	<link href="css/rex.css" rel="stylesheet" />
</head>
<body>
	<form method="post" action="index.php" class="rex-display-flex rex-center-flex-items rex-height-100vh">

		<h1>Welcome back Rex!</h1>

		<div class="rex-space-16px"></div>

		<input type="password" class="rex-width-50pp rex-pad8px rex-curDiv-8px rex-input-primary" name="password" placeholder="Enter your password"/>

		<div class="rex-space-16px"></div>
		<?php if($isError){
			echo "<p class='rex-center-text rex-color-red'>Your password is not correct. Try again</p>";
		}?>
		<div class="rex-space-16px"></div>

		<button class="rex-btn-primary rex-pad16px rex-color-white rex-width-50pp">Login</button>

	</form>
</body>
</html>