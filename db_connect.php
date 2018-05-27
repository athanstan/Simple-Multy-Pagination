<?php 
	/*Establish Connection with the cms Database*/

	$db['db_host'] = "localhost";
	$db['db_user'] = "root";
	$db['db_pass'] = "";
	$db['db_name'] = "stantz_duthcode";

	foreach ($db as $key => $value) {
		define(strtoupper($key), $value);
	}


	$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

	if (!$connection) {
		echo "<h1>Something Wrong with the connection";
	}
	//if in need of testing
	// else{
	// 	echo "<h1 style='color:white;'>Couldn't Establish connection</h1>";
	// }

 ?>