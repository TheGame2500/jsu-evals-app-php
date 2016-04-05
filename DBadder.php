<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "login";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$sql = "INSERT INTO evals (nume, prenume, progress, question1, question2, question3, n1_1, n2_1, n3_1, n1_2, n2_2, n3_2, nf_1, nf_2, nota_finala) VALUES ('îăîășțș', 'Rareș', '0', 'șlșț', 'dșț', 'îășț', '0', '0', '0', '0', '0', '0', '0', '0', '0')";

if ($conn->query($sql) === TRUE) {
    echo "New record created successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>