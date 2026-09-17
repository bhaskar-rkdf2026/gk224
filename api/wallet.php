<?php
/**
 * GK224.COM - Wallet & Transactions API Endpoint (api/wallet.php)
 */

header('Content-Type: application/json; charset=UTF-8');
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';

$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : '');
$db = getDB();

if ($action === 'balance') {
    if ($db && isset($_SESSION['user_id'])) {
        $stmt = $db->prepare("SELECT balance FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $row = $stmt->fetch();
        echo json_encode(['success' => true, 'balance' => floatval($row['balance'] ?? 10000)]);
        exit;
    }
    echo json_encode(['success' => true, 'balance' => 10000]);
    exit;
}

if ($action === 'transaction') {
    $type = isset($_POST['type']) ? $_POST['type'] : 'payment';
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;
    $category = isset($_POST['category']) ? $_POST['category'] : 'other';

    if (empty($title) || $amount == 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid transaction data']);
        exit;
    }

    if ($db && isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];
        $txnId = 'GK' . strtoupper(uniqid());

        $stmt = $db->prepare("INSERT INTO transactions (user_id, txn_id, type, title, amount, category) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$userId, $txnId, $type, $title, $amount, $category]);

        $upStmt = $db->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
        $upStmt->execute([$amount, $userId]);

        echo json_encode(['success' => true, 'txn_id' => $txnId]);
        exit;
    }

    echo json_encode(['success' => true, 'txn_id' => 'DEMO' . Date('YmdHis')]);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Unknown action']);
