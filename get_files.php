<?php
session_start();
if(!isset($_SESSION['username'])){
    header('Location: index.php');
    exit();
}

// Connect to the database
$db = new mysqli('localhost', 'root', 'shreya16', 'db_connect');
if ($db->connect_errno) {
    die('Failed to connect to the database: ' . $db->connect_error);
}

// Get the search term from the GET request
$search = $_GET['search'];

// Get the list of files matching the search term from the database
$stmt = $db->prepare("SELECT file_name FROM files WHERE user_id = ? AND file_name LIKE ?");
$searchValue = '%' . $search . '%';
$stmt->bind_param("is", $_SESSION['id'], $searchValue);

$stmt->execute();
$result = $stmt->get_result();

// Store the list of file names in an array
$fileList = array();
while ($row = $result->fetch_assoc()) {
    array_push($fileList, $row['file_name']);
}

// Send the list of file names as a JSON response
echo json_encode($fileList);
?>
