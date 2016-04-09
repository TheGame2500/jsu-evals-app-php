<?php
	ini_set('display_errors', 1);
	error_reporting(E_ALL);
	require_once("../config/db.php");
	require_once("../classes/Evaluation.php");
	$evaluation = new Evaluation();

	switch($_GET["command"]){
		case "generateEval":
			$evaluation->randomGenerator();
			break;
		default:
			echo "Not a thang bruh!";
	}
?>
