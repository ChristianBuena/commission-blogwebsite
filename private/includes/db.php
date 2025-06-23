<?php
// $db = new mysqli('localhost', 'root', '', 'getunstuck');
// if ($db->connect_error) {
//     die('Connection failed: ' . $db->connect_error);
// }
?>

<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// ← update these with your Hostinger values
$dbHost = '	31.220.110.2';
$dbUser = 'u222506753_traci';
$dbPass = 'Letsgetunstuck2023';
$dbName = 'u222506753_letgetunstuck';

$db = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
$db->set_charset('utf8mb4');

// In production don’t expose raw errors:
if ($db->connect_errno) {
    // error_log($db->connect_error);
    die('Database connection failed.');
}
?>
