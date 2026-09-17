<?php
/**
 * GK224.COM - Session Logout Handler
 */
require_once __DIR__ . '/includes/config.php';

// Unset all session variables
$_SESSION = [];

// Destroy session cookie if exists
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Clear custom auth cookie
setcookie('gk_logged_in', '', time() - 3600, '/');

// Destroy session
if (session_status() === PHP_SESSION_ACTIVE) {
    session_destroy();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Logging out...</title>
    <script>
        // Clear client-side local authentication
        localStorage.removeItem('gk_auth_logged_in');
        window.location.replace('login.php?logged_out=1');
    </script>
</head>
<body>
    <p>Logging out, please wait... <a href="login.php?logged_out=1">Click here if not redirected</a>.</p>
</body>
</html>
