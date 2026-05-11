<?php
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => '无效请求']);
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    echo json_encode(['success' => false, 'message' => '参数错误']);
    exit;
}

try {
    db()->query("UPDATE posts SET likes = likes + 1 WHERE id = ?", [$id]);
    $post = db()->fetchOne("SELECT likes FROM posts WHERE id = ?", [$id]);
    echo json_encode(['success' => true, 'likes' => $post['likes']]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => '操作失败']);
}
