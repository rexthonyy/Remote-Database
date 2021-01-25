<?php

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

if($_SERVER['REQUEST_METHOD'] == 'POST'){
	$projectId = $_POST['projectId'];
	
	$values = [];
	$values[] = $userId;
	$values[] = $projectId;
	$values[] = $_POST['title'];
	$values[] = $_POST['description'];
	$values[] = $_POST['metadata'];

	$properties['columns'] = "(".Column::USER_ID.", ".Column::PROJECT_ID.", ".Column::TITLE.", ".Column::DESCRIPTION.", ".Column::METADATA.")";
	$properties['values'] = $values;
	$properties['tokens'] = "(?, ?, ?, ?, ?)";
	
	$database = new Database(DB::INFO, DB::USER, DB::PASS);
	$dbTable = new DbTable($database, Table::POSTS_TB); 
	$dbTableQuery = new DbTableQuery($properties);
	$dbTableOperator = new DbTableOperator();
	$dbTableOperator->insert($dbTable, $dbTableQuery);

	header('Location:posts.php?project_id='.$projectId);
}else{
	$projectId = $_GET['project_id'];
}
?>

<!DOCTYPE HTML>
<html>
<head>
	<title>Create a new post</title>
	<link href="css/rex.css" rel="stylesheet" />
	<link href="css/utils.css" rel="stylesheet" />
</head>
<body>
	<div class="rex-border-bottom-gray rex-height-50px rex-display-grid2">
		<div class="custom-responsive-container rex-line-height-50px rex-fs-normal">
			<a href="projects.php"><span class="rex-hover"><?php echo getAppName(); ?></span></a>
		</div>
		<div class="custom-responsive-container">
			<a href="settings.php"><button class="rex-float-right rex-btn-secondary rex-pad8px rex-center-relative-div-vertical">Settings</button></a>
		</div>
	</div>
	<div class="rex-border-bottom-gray rex-height-50px">
		<div class="rex-center-relative-div-vertical rex-center-text">Create new post</div>
	</div>

	<div>
		<form method="post" action="createPost.php" class="rex-responsive-center-div-2 rex-center-div-horizontal">
			<input type="hidden" name="projectId" value="<?php echo $projectId; ?>"/>
			<div class="rex-space-32px"></div>
			<div class="rex-space-8px"></div>

			<label for="title" class="rex-fs-extra-small rex-weight-bold">Post name</label>
			<input required type="text" name="title" class="rex-responsive-paragragh rex-input-primary rex-width-100pp rex-pad8px rex-curDiv-4px rex-mt-8px rex-fs-small" placeholder="" />

			<div class="rex-space-16px"></div>
			<label for="description" class="rex-fs-extra-small rex-weight-bold">Description (Optional)</label>
			<textarea type="text" name="description" class="rex-responsive-paragragh rex-input-primary rex-width-100pp rex-height-200px rex-pad8px rex-curDiv-4px rex-mt-8px rex-fs-normal rex-noresize"></textarea>

			<div class="rex-space-16px"></div>
			<label for="metadata" class="rex-fs-extra-small rex-weight-bold">Meta-data (JSON) (Optional)</label>
			<textarea type="text" name="metadata" class="rex-responsive-paragragh rex-input-primary rex-width-100pp rex-height-200px rex-pad8px rex-curDiv-4px rex-mt-8px rex-fs-normal rex-noresize"></textarea>

			<div class="rex-space-32px"></div>
			<div class="rex-space-32px"></div>

			<div class="rex-display-grid2">
				<div class="rex-pr-8px">
					<button class="rex-responsive-btn rex-btn-primary rex-pad16px rex-color-white rex-curDiv-8px rex-fs-normal rex-width-100pp">Create</button>
				</div>
				<div class="rex-pl-8px">
					<a href="posts.php?project_id=<?php echo $projectId; ?>"><input type="button" id="cancelCreateProjectBtn" class="rex-responsive-btn rex-btn-secondary rex-pad16px rex-color-black rex-curDiv-8px rex-fs-normal rex-width-100pp" value="Cancel"/></a>
				</div>
			</div>
		</form>
		<div class="rex-space-32px"></div>
		<div class="rex-space-32px"></div>
		<div class="rex-space-32px"></div>
		<div class="rex-space-32px"></div>
	</div>
</body>
</html>