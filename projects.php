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

	//load all the projects
$properties['columns'] = Column::ID.",".Column::TITLE.",".Column::DESCRIPTION;
$properties['condition'] = "WHERE user_id = '".$userId."'";
$properties['orderBy'] = "";
$properties['limit'] = "";
$database = new Database(DB::INFO, DB::USER, DB::PASS);
$dbTable = new DbTable($database, Table::PROJECTS_TB); 
$dbTableQuery = new DbTableQuery($properties);
$dbTableOperator = new DbTableOperator();
$row = $dbTableOperator->read($dbTable, $dbTableQuery, new DbPrepareResult());

$projects = [];

if($row != null){
	for($i = 0; $i < count($row); $i++){
		$proj = [];
		$proj['project_id'] = $row[$i][Column::ID];
		$proj['title'] = $row[$i][Column::TITLE];
		$proj['description'] = $row[$i][Column::DESCRIPTION];

		$projects[] = $proj;
	}
}
?>

<!DOCTYPE HTML>
<html>
<head>
	<title>My projects</title>
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
		</div>
	</div>

	<div class="rex-space-16px"></div>

	<div class="custom-responsive-container">
		<h1>All projects</h1>
	</div>
	<div class="rex-space-8px"></div>
	<div class="rex-space-16px"></div>

	<div class="custom-responsive-container rex-height-70px">
		<div class="rex-center-relative-div-vertical">
			<a href="createProject.php"><button id="createProjectBtn" class="rex-btn-secondary rex-fs-small rex-pad8px rex-fs-normal">Create project</button></a>
		</div>
	</div>

	<?php if(count($projects) > 0){ ?>
			<div id="projectListContainer">
			<?php for($i = 0; $i < count($projects); $i++){?>
			<a href="posts.php?project_id=<?php echo $projects[$i]['project_id'];?>">
				<div class="rex-border-bottom-lightgray rex-selectable-item-background rex-hover rex-pt-8px rex-pb-8px">
					<div class="custom-responsive-container">
						<p class="rex-color-black"><?php echo $projects[$i]['title']; ?></p>
						<div class="rex-space-8px"></div>
						<p class="rex-color-darkgray"><?php echo $projects[$i]['description']."&nbsp;"; ?></p>
					</div>
				</div>
			</a>
			<?php } ?>
		</div>
		<?php }else{ ?>

		<div id="noProjectContainer" class="custom-responsive-container">
			<p class="rex-fs-normal rex-mt-32px">Create a project to get started</p>
		</div>

	<?php }?>
</body>
</html>