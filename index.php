<?php
/* 
	Builds and populates table with database values
	Znamensky | 2021
*/

try {
	$conn = new PDO('', "", ""); // database credentials redacted
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT); //debug mode off
	$qry = "SELECT * FROM tbl"; //query string: pulls *all* from table tbl
	print "<table>";
	$output = $conn->query($qry); // instances query
	$row = $output->fetch(PDO::FETCH_ASSOC);
	print " <tr>";
	 			
	foreach ($row as $header => $val){ // prints headers
		print " <th>$header</th>"; 
	} 
	print " </tr>";
	
	// pulls actual vals 		
	$words = $conn->query($qry);
	$words->setFetchMode(PDO::FETCH_ASSOC);
	
	// generates rest of table below headers
	foreach($words as $row){
		print " <tr>";
		foreach ($row as $text=>$val){
			print " <td>$val</td>";
		} 
		print " </tr>";
	} 
	print "</table>";

} catch(PDOException $e){
	echo 'Exception has occured.';
} 

?>