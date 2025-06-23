<?php
require_once __DIR__ . '/db.php';
$count = $db->query("SELECT COUNT(*) as unread FROM notifications WHERE is_read=0");
echo $count->fetch_assoc()['unread'] ?? 0;
?>