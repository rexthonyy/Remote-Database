<?php	
	abstract class Column {
		//all
		const ID = "id";
		
		//users_tb
		const PASSWORD = "password";
		const API_KEY = "api_key";

		//session_tb
		const USER_ID = "user_id";
		const SESSION_ID = "session_id";
		const EXPIRE = "expire";

		//projects_tb
		//const USER_ID = "user_id";
		const TITLE = "title";
		const DESCRIPTION = "description";
		
		//posts_tb
		const PROJECT_ID = "project_id";
		//const TITLE = "title";
		//const DESCRIPTION = "description";
		const METADATA = "metadata";

		//data_tb
		const POST_ID = "post_id";
		const ORDER_IN_CATEGORY = "order_in_category";
		const KEY = "data_key";
		const VALUE = "data_value";
	}
?>