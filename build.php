<?php
/**
 * GK224.COM - Static HTML Generator for Netlify
 * Generates standalone .html pages with full client-side functionality
 */

$pages = [
    'index.php' => 'index.html',
    'learn.php' => 'learn.html',
    'earn.php' => 'earn.html',
    'travel.php' => 'travel.html',
    'profile.php' => 'profile.html',
    'referral.php' => 'referral.html',
    'scratchpad.php' => 'scratchpad.html',
    'login.php' => 'login.html',
];

echo "Generating static HTML files for Netlify...\n";

foreach ($pages as $phpFile => $htmlFile) {
    if (!file_exists(__DIR__ . '/' . $phpFile)) {
        echo "Skipping $phpFile (not found)\n";
        continue;
    }

    ob_start();
    // Simulate server variables
    $_SERVER['SCRIPT_NAME'] = '/' . $phpFile;
    $_SERVER['HTTP_HOST'] = 'localhost';
    $_SESSION = [];
    
    // Include and capture
    include __DIR__ . '/' . $phpFile;
    $content = ob_get_clean();

    // In static HTML, update internal links to point to .html
    $replacements = [
        'href="index.php"' => 'href="index.html"',
        'href="learn.php"' => 'href="learn.html"',
        'href="earn.php"' => 'href="earn.html"',
        'href="travel.php"' => 'href="travel.html"',
        'href="profile.php"' => 'href="profile.html"',
        'href="referral.php"' => 'href="referral.html"',
        'href="reffral.php"' => 'href="referral.html"',
        'href="scratchpad.php"' => 'href="scratchpad.html"',
        'href="login.php"' => 'href="login.html"',
        "window.location.href = 'index.php" => "window.location.href = 'index.html",
        "window.location.replace('login.php" => "window.location.replace('login.html",
        "window.location.href = 'login.php" => "window.location.href = 'login.html",
    ];

    $htmlContent = strtr($content, $replacements);

    file_put_contents(__DIR__ . '/' . $htmlFile, $htmlContent);
    echo "✔ Generated $htmlFile from $phpFile (" . strlen($htmlContent) . " bytes)\n";
}

echo "Done! All static HTML pages are ready for Netlify.\n";
