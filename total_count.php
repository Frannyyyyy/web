<?php
header('Content-Type: application/json');

$servername = "LocalHost";
$username = "fran";
$password = "QueryCode2212#";
$dbname = "dafac";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(['error' => 'Connection failed: ' . $conn->connect_error]));
}

// Get count of family heads
$headsQuery = "SELECT COUNT(*) as total FROM head";
$headsResult = $conn->query($headsQuery);
$totalHeads = $headsResult->fetch_assoc()['total'];

// Get count of family members
$membersQuery = "SELECT COUNT(*) as total FROM fammem";
$membersResult = $conn->query($membersQuery);
$totalMembers = $membersResult->fetch_assoc()['total'];

echo json_encode([
    'total_heads' => $totalHeads,
    'total_members' => $totalMembers
]);

$conn->close();
?>
