<?php
	ini_set('display_errors', 1);
	error_reporting(E_ALL);
	require_once("../config/db.php");
	require_once("../classes/Evaluation.php");
	$evaluation = new Evaluation();

	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	    switch($_POST["command"]){
			case "submitMarks":
				$evaluation->submitMarks($_POST["marks"]);
				break;
			default:
				echo "Not a thang bruh";
		}
	} else if($_SERVER['REQUEST_METHOD'] === 'GET'){
		switch($_GET["command"]){
			case "generateEval":
				$evaluation->randomGenerator();
				break;
			default:
				echo "Not a thang bruh!";
		}
	} else {
		echo "Not a thang bruh!";
	}
?>
