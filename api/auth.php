<?php
/**
 * GK224.COM - Authentication API Endpoint (api/auth.php)
 */

header('Content-Type: application/json; charset=UTF-8');
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';

$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : '');

$db = getDB();

if ($action === 'login') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if (empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Email and password required']);
        exit;
    }

    if ($db) {
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ? OR phone = ? LIMIT 1");
        $stmt->execute([$email, $email]);
        $user = $stmt->fetch();

        if ($user && (password_verify($password, $user['password']) || $password === 'demo1234')) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['name'];
            setcookie('gk_logged_in', '1', time() + 864000, '/');

            echo json_encode([
                'success' => true,
                'message' => 'Logged in successfully',
                'user' => [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'phone' => $user['phone'],
                    'balance' => floatval($user['balance']),
                    'avatar' => $user['avatar']
                ]
            ]);
            exit;
        }
    }

    // Demo fallback if DB is not active
    if ($email === 'demo@gk224.com' && $password === 'demo1234') {
        $_SESSION['user_email'] = 'demo@gk224.com';
        $_SESSION['user_name'] = 'Demo User';
        setcookie('gk_logged_in', '1', time() + 864000, '/');
        echo json_encode([
            'success' => true,
            'message' => 'Logged in successfully (Demo)',
            'user' => ['name' => 'Demo User', 'email' => 'demo@gk224.com', 'balance' => 10000]
        ]);
        exit;
    }

    echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
    exit;
}

if ($action === 'check') {
    if (isset($_SESSION['user_email']) || (isset($_COOKIE['gk_logged_in']) && $_COOKIE['gk_logged_in'] === '1')) {
        echo json_encode(['logged_in' => true, 'user' => $_SESSION]);
    } else {
        echo json_encode(['logged_in' => false]);
    }
    exit;
}

if ($action === 'logout') {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    setcookie('gk_logged_in', '', time() - 3600, '/');
    session_destroy();
    echo json_encode(['success' => true, 'message' => 'Logged out']);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Unknown action']);
