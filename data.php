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
$postId = $_GET['post_id'];

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
	header("Location: posts.php?project_id=".$projectId);
}else{
	$projectTitle = $row[0][Column::TITLE];
	$projectDescription = $row[0][Column::DESCRIPTION];
}

//load this post details
$properties['columns'] = Column::TITLE.",".Column::DESCRIPTION;
$properties['condition'] = "WHERE user_id = ".$userId." AND id=".$postId;
$properties['orderBy'] = "";
$properties['limit'] = "";
$database = new Database(DB::INFO, DB::USER, DB::PASS);
$dbTable = new DbTable($database, Table::POSTS_TB); 
$dbTableQuery = new DbTableQuery($properties);
$dbTableOperator = new DbTableOperator();
$row = $dbTableOperator->read($dbTable, $dbTableQuery, new DbPrepareResult());

$posts = [];

$postTitle = $postDescription = "";

if($row == null){
	header("Location: posts.php?project_id=".$projectId);
}else{
	$postTitle = $row[0][Column::TITLE];
	$postDescription = $row[0][Column::DESCRIPTION];
}

//load all the data for this post_id
$properties['columns'] = Column::ID.",".Column::ORDER_IN_CATEGORY.",".Column::KEY.",".Column::VALUE;
$properties['condition'] = "WHERE user_id = ".$userId." AND project_id = ".$projectId." AND post_id = ".$postId;
$properties['orderBy'] = "ORDER BY ".Column::ORDER_IN_CATEGORY." ASC";
$properties['limit'] = "";
$database = new Database(DB::INFO, DB::USER, DB::PASS);
$dbTable = new DbTable($database, Table::DATA_TB); 
$dbTableQuery = new DbTableQuery($properties);
$dbTableOperator = new DbTableOperator();
$row = $dbTableOperator->read($dbTable, $dbTableQuery, new DbPrepareResult());

$dataList = [];

if($row != null){
	for($i = 0; $i < count($row); $i++){
		$data = [];
		$data['data_id'] = $row[$i][Column::ID];
		$data['order_in_category'] = $row[$i][Column::ORDER_IN_CATEGORY];
		$data['key'] = $row[$i][Column::KEY];
		$data['value'] = $row[$i][Column::VALUE];

		$dataList[] = $data;
	}
}
?>

<!DOCTYPE HTML>
<html>
<head>
	<title><?php echo $postTitle; ?> | Data</title>
	<link href="css/rex.css" rel="stylesheet" />
	<link href="css/utils.css" rel="stylesheet" />
</head>
<body>
	<div class="rex-border-bottom-gray rex-height-50px rex-display-grid2">
		<div class="custom-responsive-container rex-line-height-50px rex-fs-normal">
			<a href="projects.php"><span class="rex-hover">App name</span></a>
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

	<div class="custom-responsive-container">
		<h1><?php echo $postTitle; ?></h1>
		<div class="rex-space-8px"></div>
		<p class="rex-color-darkgray"><?php echo $postDescription;?></p>
	</div>

	<div class="rex-space-16px"></div>

	<div class="custom-responsive-container rex-height-70px">
		<div class="rex-center-relative-div-vertical">
			<a href="createData.php?project_id=<?php echo $projectId; ?>&post_id=<?php echo $postId; ?>"><button class="rex-btn-secondary rex-fs-small rex-pad8px rex-fs-normal">Create data</button></a>
		</div>
	</div>

	<div class="rex-space-32px"></div>

	<?php if(count($dataList) > 0){ ?>
		<div class="rex-color-black rex-border-bottom-black">
			<div class="custom-responsive-container custom-data-grid ">
				<div>
					ORDER IN CATEGORY
				</div>
				<div>
					KEY
				</div>
				<div>
					VALUE
				</div>
			</div>
		</div>

		<div>
			<?php for($i = 0; $i < count($dataList); $i++){?>
				<a href="editData.php?project_id=<?php echo $projectId; ?>&post_id=<?php echo $postId; ?>&data_id=<?php echo $dataList[$i]['data_id'];?>">
					<div class="rex-border-bottom-lightgray rex-selectable-item-background rex-hover rex-pt-8px rex-pb-8px">
						<div class="custom-responsive-container custom-data-grid rex-color-black">
							<div>
								<?php echo $dataList[$i]['order_in_category']; ?>
							</div>
							<div class="rex-color-darkgray">
								<?php echo $dataList[$i]['key']; ?>
							</div>
							<div>
								<?php echo $dataList[$i]['value']; ?>
							</div>
						</div>
					</div>
				</a>
			<?php } ?>
		</div>
	<?php }else{ ?>

		<div id="noProjectContainer" class="custom-responsive-container">
			<p class="rex-fs-normal rex-mt-32px">Get started creating the future</p>
		</div>

	<?php }?>
</body>
</html>