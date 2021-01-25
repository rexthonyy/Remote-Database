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

$savedMsg = "";

$projectId = $postId = -1;

if($_SERVER['REQUEST_METHOD'] == 'POST'){

	$projectId = $_POST['projectId'];
	$postId = $_POST['postId'];

	if(isset($_POST['update'])){

		$title = $_POST['title'];
		$description = $_POST['description'];
		$metadata = $_POST['metadata'];

		$equality = Column::TITLE."=?, ".Column::DESCRIPTION."=?, ".Column::METADATA."=?";
		$values[] = $title;
		$values[] = $description;
		$values[] = $metadata;

		$condition = "WHERE id=$postId AND user_id=$userId";

		$properties['equality'] = $equality;
		$properties['values'] = $values;
		$properties['condition'] = $condition;

		$database = new Database(DB::INFO, DB::USER, DB::PASS);
		$dbTable = new DbTable($database, Table::POSTS_TB); 
		$dbTableQuery = new DbTableQuery($properties);
		$dbTableOperator = new DbTableOperator();
		$dbTableOperator->update($dbTable, $dbTableQuery);

		$savedMsg = "Post Updated successfully!";
	}else{
		$properties['condition'] =  "WHERE post_id = $postId";
		$database = new Database(DB::INFO, DB::USER, DB::PASS);
		$dbTable = new DbTable($database, Table::DATA_TB); 
		$dbTableQuery = new DbTableQuery($properties);
		$dbTableOperator = new DbTableOperator();
		$dbTableOperator->delete($dbTable, $dbTableQuery);

		$properties['condition'] =  "WHERE id = $postId";
		$database = new Database(DB::INFO, DB::USER, DB::PASS);
		$dbTable = new DbTable($database, Table::POSTS_TB); 
		$dbTableQuery = new DbTableQuery($properties);
		$dbTableOperator = new DbTableOperator();
		$dbTableOperator->delete($dbTable, $dbTableQuery);

		header("Location:posts.php?project_id=$projectId");
	}
}else{
	$projectId = $_GET['project_id'];
	$postId = $_GET['post_id'];
}

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

$projectTitle = $projectDescription = "";

if($row == null){
	header("Location: posts.php?project_id=".$projectId);
}else{
	$projectTitle = $row[0][Column::TITLE];
	$projectDescription = $row[0][Column::DESCRIPTION];
}

//load this post details
$properties['columns'] = Column::TITLE.",".Column::DESCRIPTION.",".Column::METADATA;
$properties['condition'] = "WHERE user_id = ".$userId." AND id=".$postId;
$properties['orderBy'] = "";
$properties['limit'] = "";
$database = new Database(DB::INFO, DB::USER, DB::PASS);
$dbTable = new DbTable($database, Table::POSTS_TB); 
$dbTableQuery = new DbTableQuery($properties);
$dbTableOperator = new DbTableOperator();
$row = $dbTableOperator->read($dbTable, $dbTableQuery, new DbPrepareResult());

$postTitle = $postDescription = $postMetaData = "";

if($row == null){
	header("Location: posts.php?project_id=".$projectId);
}else{
	$postTitle = $row[0][Column::TITLE];
	$postDescription = $row[0][Column::DESCRIPTION];
	$postMetaData = $row[0][Column::METADATA];
}
?>

<!DOCTYPE HTML>
<html>
<head>
	<title>Post Settings</title>
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
			>
			<a class="rex-fs-extra-small"><?php echo $postTitle; ?></a>
		</div>
	</div>

	<div class="rex-border-bottom-gray rex-height-50px rex-overflow-auto rex-nowrap">
		<div class="custom-responsive-container">
			<a href="data.php?project_id=<?php echo $projectId; ?>&post_id=<?php echo $postId;?>">
				<div class="rex-display-inline-block rex-height-50px rex-selectable-item-background rex-hover rex-color-black rex-mr-16px">
					<span class="rex-line-height-50px rex-fs-extra-small">Data</span>
				</div>
			</a>
			<a href="postSettings.php?project_id=<?php echo $projectId; ?>&post_id=<?php echo $postId; ?>">
				<div class="rex-display-inline-block rex-height-50px rex-selectable-item-background rex-hover rex-color-black rex-mr-16px">
					<span class="rex-line-height-50px rex-fs-extra-small">Post settings</span>
				</div>
			</a>
		</div>
	</div>

	<div class="rex-space-16px"></div>

	<div class="rex-center-text">
		<span class="rex-color-green rex-weight-bold rex-fs-normal"><?php echo $savedMsg;?></span>
	</div>

	<div class="rex-space-16px"></div>

	<form method="post" action="postSettings.php" class="rex-responsive-center-div-2 rex-center-div-horizontal">
		<input type="hidden" name="projectId" value="<?php echo $projectId; ?>"/>
		<input type="hidden" name="postId" value="<?php echo $postId; ?>"/>
		<label for="title" class="rex-fs-extra-small rex-weight-bold">Post name</label>
		<input required type="text" name="title" class="rex-responsive-paragragh rex-input-primary rex-width-100pp rex-pad8px rex-curDiv-4px rex-mt-8px rex-fs-small" value="<?php echo $postTitle; ?>" />

		<div class="rex-space-16px"></div>
		<label for="description" class="rex-fs-extra-small rex-weight-bold">Description (Optional)</label>
		<textarea type="text" name="description" class="rex-responsive-paragragh rex-input-primary rex-width-100pp rex-height-200px rex-pad8px rex-curDiv-4px rex-mt-8px rex-fs-normal rex-noresize"><?php echo $postDescription; ?></textarea>

		<div class="rex-space-16px"></div>
		<label for="metadata" class="rex-fs-extra-small rex-weight-bold">Meta-data (JSON) (Optional)</label>
		<textarea type="text" name="metadata" class="rex-responsive-paragragh rex-input-primary rex-width-100pp rex-height-200px rex-pad8px rex-curDiv-4px rex-mt-8px rex-fs-normal rex-noresize"><?php echo $postMetaData; ?></textarea>

		<div class="rex-space-32px"></div>
		<div class="rex-space-32px"></div>

		<div>
			<input type="submit" name="update" class="rex-responsive-btn rex-btn-primary rex-pad16px rex-color-white rex-curDiv-8px rex-fs-normal rex-width-100pp" value="Update post"/>
		</div>
	</form>

	<div class="rex-space-32px rex-border-bottom-gray"></div>

	<!-- delete project form -->
	<form method="post" action="postSettings.php" class="rex-responsive-center-div-2 rex-center-div-horizontal">
		<input type="hidden" name="projectId" value="<?php echo $projectId; ?>"/>
		<input type="hidden" name="postId" value="<?php echo $postId; ?>"/>
		<div class="rex-space-32px"></div>
		<label class="rex-fs-extra-small rex-weight-bold rex-color-red">Delete Post</label>
		<div class="rex-space-8px"></div>
		<input type="submit" name="delete" class="rex-responsive-btn rex-btn-secondary rex-pad16px rex-color-red rex-border-red rex-curDiv-8px rex-fs-normal rex-width-100pp" value="Delete post"/>
		<div class="rex-space-32px"></div>
	</form>
	<div class="rex-space-32px"></div>
	<div class="rex-space-32px"></div>
	<div class="rex-space-32px"></div>
</body>
</html>