<?php
$authUrl = 'http://oauth2-proxy:4180/oauth2/auth';

// 请求auth验证
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $authUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_COOKIE, $_SERVER['HTTP_COOKIE'] ?? '');
curl_setopt($ch, CURLOPT_HEADER, true);
$output = curl_exec($ch);
$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);

// 处理错误
if (curl_errno($ch)) {
    $error = curl_error($ch);
    header('HTTP/1.1 500 Internal Server Error');
    echo $error;
    exit;
}

curl_close($ch);

// 处理header
$header = substr($output, 0, $headerSize);
$headerLines = explode(PHP_EOL, $header);
$headerLines = preg_grep('/^(Date:|Content-Length:)/', $headerLines, PREG_GREP_INVERT);
$email = '';
foreach ($headerLines as $headerLine) {
    $headerLine = trim($headerLine);
    if (empty($headerLine)) {
        continue;
    }
    if (strpos($headerLine, 'X-Auth-Request-Email:') === 0) {
        $email = substr($headerLine, strlen('X-Auth-Request-Email:'));
        $email = trim($email);
    }
    header($headerLine);
}

$content = substr($output, $headerSize);

if (empty($email)) {
    echo $content;
    exit;
}

// 设置访问账号认证信息
$users = include_once 'users.php';
$access = [];
foreach ($users as $item) {
    if (!empty($item['emails']) && !empty($item['access']) && in_array($email, $item['emails'])) {
        $access = $item['access'];
        break;
    }
}

if (!empty($access['username']) && !empty($access['password'])) {
    $authStr = base64_encode($access['username'] . ':' . $access['password']);
    header('X-Auth-Request-Basic-Auth: Basic ' . $authStr);
}

echo $content;
