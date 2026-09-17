<?php
/**
 * GK224.COM - Village Student Survey API Endpoint (api/survey.php)
 */

header('Content-Type: application/json; charset=UTF-8');
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';

$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $studentName = isset($_POST['name']) ? trim($_POST['name']) : '';
    $age = isset($_POST['age']) ? intval($_POST['age']) : 0;
    $className = isset($_POST['class']) ? trim($_POST['class']) : '';
    $villageName = isset($_POST['village']) ? trim($_POST['village']) : '';
    $parentName = isset($_POST['parent']) ? trim($_POST['parent']) : '';
    $contact = isset($_POST['contact']) ? trim($_POST['contact']) : '';
    $gender = isset($_POST['gender']) ? trim($_POST['gender']) : 'Other';

    if (empty($studentName) || empty($villageName)) {
        echo json_encode(['success' => false, 'message' => 'Student name and village are required']);
        exit;
    }

    $reward = 10.00;

    if ($db && isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];
        $stmt = $db->prepare("INSERT INTO survey_entries (user_id, student_name, age, class_name, village_name, parent_name, contact, gender, reward_paid) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$userId, $studentName, $age, $className, $villageName, $parentName, $contact, $gender, $reward]);

        // Credit ₹10 to user balance
        $upStmt = $db->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
        $upStmt->execute([$reward, $userId]);

        // Record transaction
        $txnId = 'SURVEY' . strtoupper(uniqid());
        $txStmt = $db->prepare("INSERT INTO transactions (user_id, txn_id, type, title, amount, category) VALUES (?, ?, 'earning', ?, ?, 'other')");
        $txStmt->execute([$userId, $txnId, 'Survey: ' . $studentName, $reward]);

        echo json_encode(['success' => true, 'message' => 'Survey saved and ₹10 credited', 'reward' => $reward]);
        exit;
    }

    echo json_encode(['success' => true, 'message' => 'Survey saved (Demo)', 'reward' => $reward]);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);
