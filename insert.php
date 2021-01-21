<?php
/* 
	User inputted values from the form are checked against blacklist and uploaded to database only if:
	1. captcha is complete
	2. neither word contains substring of a blacklisted word
	3. word length does not exceed maximum length (to keep table clean)
	4. words do not contain special chars that would overflow the cell boundaries

	Znamensky | 2021
*/




// Google Captcha -----------------------------------------------------------------------------------
$captcha;
if(isset($_POST['g-recaptcha-response'])){
	$captcha=$_POST['g-recaptcha-response'];
}
if(!$captcha){ // checks if user submitted the captcha
	echo 'Please complete the captcha.';
	exit;
}
$secretKey = ""; // redacted
$url = 'https://www.google.com/recaptcha/api/siteverify?secret=' . urlencode($secretKey) .  '&response=' . urlencode($captcha);
$response = file_get_contents($url);
$responseKeys = json_decode($response,true);
if($responseKeys["success"]) {
} else {
	exit; 
}
// Google Captcha -----------------------------------------------------------------------------------




// simple popup function for debugging
function function_alert($message) { 
	echo "<script>alert('$message');</script>"; 
} 



// Character Limit and Non-UTF8 Filter --------------------------------------------------------------
// The purpose is to avoid chars which disrupt cell boundaries or the layout of the table

$Name = $_POST['name']; // fetches result from form submission POST
$Food = $_POST['food'];
$Name = utf8_decode($Name); // utf8 processing
$Food = utf8_decode($Food);
$length_Name = strlen($utf8_Name); // checks submission lengths
$length_Food = strlen($utf8_Food);

if ($length_Name>30 or $length_Food>30 ){
	function_alert("Message is too long, or contains unaccepted non-UTF8 characters. Character limit is 30.");
	exit;
}


// Blacklist check and database upload --------------------------------------------------------------
// Pulls blacklist table, compares against submission (case-insensitive), submits
$servername = "";
$username= "";
$password= "";
$dbname = "";

try {
	$conn = new PDO("mysql:host=$servername ;dbname=$dbname ", $username, $password);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
	$query = "SELECT * FROM badwords"; // pulls blacklist table
	$words = $conn->query($query);
	$words->setFetchMode(PDO::FETCH_ASSOC);
	$isClean=0; // variable to track whether submission is clean (0 = clean)
	foreach($data as $row){
		foreach ($row as $name=>$val){
			if (stripos($Name, $val)!==False){
				$isClean=1;
			}
			if (stripos($Food, $val)!==False){
				$isClean=1;
			}
		} 
	} 

	if($isClean==0){
		$sql = "INSERT INTO food (name, food) VALUES ('$Name', '$Food')";
		$conn->exec($sql);
	} 

} catch(PDOException $e) {
		echo 'Error.';
}

$conn = null; //closes connection
header("refresh:1; url=index.php"); //refreshes page, executing insert.php

?> 