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
$projectId = null;

$savedMsg = "";

if($_SERVER['REQUEST_METHOD'] == 'POST'){

	$projectId = $_POST['projectId'];

	if(isset($_POST['update'])){

		$equality = Column::TITLE."=?, ".Column::DESCRIPTION."=?";
		$values[] = $_POST['title'];
		$values[] = $_POST['description'];

		$condition = "WHERE id=$projectId AND user_id=$userId";

		$properties['equality'] = $equality;
		$properties['values'] = $values;
		$properties['condition'] = $condition;

		$database = new Database(DB::INFO, DB::USER, DB::PASS);
		$dbTable = new DbTable($database, Table::PROJECTS_TB); 
		$dbTableQuery = new DbTableQuery($properties);
		$dbTableOperator = new DbTableOperator();
		$dbTableOperator->update($dbTable, $dbTableQuery);

		$savedMsg = "Project Updated successfully!";
	}else if(isset($_POST['delete'])){

		$properties['condition'] =  "WHERE id = $projectId";
		$database = new Database(DB::INFO, DB::USER, DB::PASS);
		$dbTable = new DbTable($database, Table::PROJECTS_TB); 
		$dbTableQuery = new DbTableQuery($properties);
		$dbTableOperator = new DbTableOperator();
		$dbTableOperator->delete($dbTable, $dbTableQuery);

		$properties['condition'] =  "WHERE project_id = $projectId";
		$database = new Database(DB::INFO, DB::USER, DB::PASS);
		$dbTable = new DbTable($database, Table::POSTS_TB); 
		$dbTableQuery = new DbTableQuery($properties);
		$dbTableOperator = new DbTableOperator();
		$dbTableOperator->delete($dbTable, $dbTableQuery);

		$properties['condition'] =  "WHERE project_id = $projectId";
		$database = new Database(DB::INFO, DB::USER, DB::PASS);
		$dbTable = new DbTable($database, Table::DATA_TB); 
		$dbTableQuery = new DbTableQuery($properties);
		$dbTableOperator = new DbTableOperator();
		$dbTableOperator->delete($dbTable, $dbTableQuery);

		header('Location:projects.php');
	}
}else{
	$projectId = $_GET['project_id'];
}


//get the project details
$properties['columns'] = Column::TITLE.",".Column::DESCRIPTION;
$properties['condition'] = "WHERE id = $projectId AND user_id=$userId";
$properties['orderBy'] = "";
$properties['limit'] = "";
$database = new Database(DB::INFO, DB::USER, DB::PASS);
$dbTable = new DbTable($database, Table::PROJECTS_TB); 
$dbTableQuery = new DbTableQuery($properties);
$dbTableOperator = new DbTableOperator();
$row = $dbTableOperator->read($dbTable, $dbTableQuery, new DbPrepareResult());

$projectTitle = $projectDescription = "";

if($row == null){
	echo "Project not found";
	exit;
}else{
	$projectTitle = $row[0][Column::TITLE];
	$projectDescription = $row[0][Column::DESCRIPTION];
}

?>

<!DOCTYPE HTML>
<html>
<head>
	<title>Project Settings</title>
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

	<div class="rex-height-50px rex-border-bottom-gray">
		<div class="custom-responsive-container rex-center-relative-div-vertical ">
			<a href="projects.php" class="rex-fs-extra-small">All Projects</a>
			>
			<a href="posts.php?project_id=<?php echo $projectId; ?>" class="rex-fs-extra-small"><?php echo $projectTitle; ?></a>
		</div>
	</div>

	<div class="rex-border-bottom-gray rex-height-50px rex-overflow-auto rex-nowrap">
		<div class="custom-responsive-container">
			<a href="posts.php?project_id=<?php echo $projectId; ?>">
				<div class="rex-display-inline-block rex-height-50px rex-selectable-item-background rex-hover rex-color-black rex-mr-16px">
					<span class="rex-line-height-50px rex-fs-extra-small">Posts</span>
				</div>
			</a>
			<a href="projectSettings.php?project_id=<?php echo $projectId; ?>">
				<div class="rex-display-inline-block rex-height-50px rex-selectable-item-background rex-hover rex-color-black rex-mr-16px">
					<span class="rex-line-height-50px rex-fs-extra-small">Project settings</span>
				</div>
			</a>
		</div>
	</div>
	<div>
		<div class="rex-space-16px"></div>
		<div class="rex-center-text">
			<span class="rex-color-green rex-weight-bold rex-fs-normal"><?php echo $savedMsg;?></span>
		</div>
		<div class="rex-space-16px"></div>
		<!-- profile update form -->
		<form method="post" action="projectSettings.php" class="rex-responsive-center-div-2 rex-center-div-horizontal">
			<input type="hidden" name="update"/>
			<input type="hidden" name="projectId" value="<?php echo $projectId; ?>"/>
			<label for="title" class="rex-fs-extra-small rex-weight-bold">Project Title</label>
			<input required type="text" name="title" class="rex-responsive-paragragh rex-input-primary rex-width-100pp rex-pad8px rex-curDiv-4px rex-mt-8px rex-fs-small" value="<?php echo $projectTitle; ?>" />

			<div class="rex-space-16px"></div>
			<label for="description" class="rex-fs-extra-small rex-weight-bold">Description (Optional)</label>
			<textarea type="text" name="description" class="rex-responsive-paragragh rex-input-primary rex-width-100pp rex-height-200px rex-pad8px rex-curDiv-4px rex-mt-8px rex-fs-normal rex-noresize"><?php echo $projectDescription; ?></textarea>

			<div class="rex-space-32px"></div>
			<button class="rex-responsive-btn rex-btn-primary rex-pad16px rex-color-white rex-curDiv-8px rex-fs-normal rex-width-100pp"/>Update</button>
			<div class="rex-space-32px"></div>
		</form>

		<div class="rex-space-32px rex-border-bottom-gray"></div>

		<!-- delete project form -->
		<form method="post" action="projectSettings.php" class="rex-responsive-center-div-2 rex-center-div-horizontal">
			<input type="hidden" name="delete"/>
			<input type="hidden" name="projectId" value="<?php echo $projectId; ?>"/>
			<div class="rex-space-32px"></div>
			<label class="rex-fs-extra-small rex-weight-bold rex-color-red">Delete Project</label>
			<div class="rex-space-8px"></div>
			<button class="rex-responsive-btn rex-btn-secondary rex-pad16px rex-color-red rex-border-red rex-curDiv-8px rex-fs-normal rex-width-100pp">Delete Project</button>
			<div class="rex-space-32px"></div>
		</form>
		<div class="rex-space-32px"></div>
		<div class="rex-space-32px"></div>
		<div class="rex-space-32px"></div>
	</div>
</body>
</html>