<?php

session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['admin_logged_in'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Not authorized']);
    exit;
}
require_once __DIR__ . '/../../private/includes/db.php';

$id = (int)($_POST['submission_id'] ?? 0);
$action = $_POST['action'] ?? '';

if (!$id || !$action) {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
    exit;
}

switch ($action) {
    case 'approve':
        $db->query("UPDATE submissions SET status='approved', featured=0 WHERE id=$id");
        break;
    case 'decline':
        $db->query("DELETE FROM submissions WHERE id=$id");
        break;
    case 'feature':
        $db->query("UPDATE submissions SET status='approved', featured=1 WHERE id=$id");
        break;
    case 'mark_read':
        $db->query("UPDATE submissions SET status='read' WHERE id=$id");
        break;
    default:
        echo json_encode(['success' => false, 'error' => 'Unknown action']);
        exit;
}
$unread = $db->query("SELECT COUNT(*) as unread FROM submissions WHERE status='pending'");
$unreadCount = $unread->fetch_assoc()['unread'] ?? 0;
echo json_encode(['success' => true, 'unread' => $unreadCount]);