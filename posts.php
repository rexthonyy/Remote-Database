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
$projectId = $_GET['project_id'];

//load this project details
$properties['columns'] = Column::TITLE.",".Column::DESCRIPTION;
$properties['condition'] = "WHERE user_id = ".$userId." AND id=".$projectId;
$properties['orderBy'] = "";
$properties['limit'] = "";
$database = new Database(DB::INFO, DB::USER, DB::PASS);
$dbTable = new DbTable($database, Table::PROJECTS_TB); 
$dbTableQuery = new DbTableQuery($properties);
$dbTableOperator = new DbTableOperator();
$row = $dbTableOperator->read($dbTable, $dbTableQuery, new DbPrepareResult());

$posts = [];

$projectTitle = $projectDescription = "";

if($row == null){
	header("Location: projects.php");
}else{
	$projectTitle = $row[0][Column::TITLE];
	$projectDescription = $row[0][Column::DESCRIPTION];
}

//load all the posts for this project_id
$properties['columns'] = Column::ID.",".Column::TITLE.",".Column::DESCRIPTION;
$properties['condition'] = "WHERE user_id = ".$userId." AND project_id = ".$projectId;
$properties['orderBy'] = "";
$properties['limit'] = "";
$database = new Database(DB::INFO, DB::USER, DB::PASS);
$dbTable = new DbTable($database, Table::POSTS_TB); 
$dbTableQuery = new DbTableQuery($properties);
$dbTableOperator = new DbTableOperator();
$row = $dbTableOperator->read($dbTable, $dbTableQuery, new DbPrepareResult());

$posts = [];

if($row != null){
	for($i = 0; $i < count($row); $i++){
		$post = [];
		$post['post_id'] = $row[$i][Column::ID];
		$post['title'] = $row[$i][Column::TITLE];
		$post['description'] = $row[$i][Column::DESCRIPTION];

		$posts[] = $post;
	}
}
?>

<!DOCTYPE HTML>
<html>
<head>
	<title>My posts</title>
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

	<div class="rex-space-16px"></div>

	<div class="custom-responsive-container">
		<h1><?php echo $projectTitle; ?></h1>
		<div class="rex-space-8px"></div>
		<p class="rex-color-darkgray"><?php echo $projectDescription;?></p>
	</div>

	<div class="rex-space-16px"></div>

	<div class="custom-responsive-container rex-height-70px">
		<div class="rex-center-relative-div-vertical">
			<a href="createPost.php?project_id=<?php echo $projectId; ?>"><button class="rex-btn-secondary rex-fs-small rex-pad8px rex-fs-normal">Create post</button></a>
		</div>
	</div>

	<?php if(count($posts) > 0){ ?>
		<div id="projectListContainer">
			<?php for($i = 0; $i < count($posts); $i++){?>
				<a href="data.php?project_id=<?php echo $projectId; ?>&post_id=<?php echo $posts[$i]['post_id'];?>">
					<div class="rex-border-bottom-lightgray rex-selectable-item-background rex-hover rex-pt-8px rex-pb-8px">
						<div class="custom-responsive-container">
							<p class="rex-color-black"><?php echo $posts[$i]['title']; ?></p>
							<div class="rex-space-8px"></div>
							<p class="rex-color-darkgray"><?php echo $posts[$i]['description']."&nbsp;"; ?></p>
						</div>
					</div>
				</a>
			<?php } ?>
		</div>
	<?php }else{ ?>

		<div id="noProjectContainer" class="custom-responsive-container">
			<p class="rex-fs-normal rex-mt-32px">Create a post to get started</p>
		</div>

	<?php }?>
</body>
</html>