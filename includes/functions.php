<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

function format_time($datetime) {
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;
    
    if ($diff < 60) {
        return '刚刚';
    } elseif ($diff < 3600) {
        return floor($diff / 60) . '分钟前';
    } elseif ($diff < 86400) {
        return floor($diff / 3600) . '小时前';
    } elseif ($diff < 604800) {
        return floor($diff / 86400) . '天前';
    } else {
        return date('Y-m-d', $timestamp);
    }
}

function is_admin_logged_in() {
    return isset($_SESSION['admin_id']) && $_SESSION['admin_id'] > 0;
}

function require_admin_login() {
    if (!is_admin_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

function get_topics() {
    try {
        return db()->fetchAll('SELECT * FROM topics ORDER BY sort ASC, id ASC');
    } catch (Exception $e) {
        return [];
    }
}

function get_posts($topic_id = null, $page = 1, $limit = 10) {
    try {
        $offset = ($page - 1) * $limit;
        $sql = 'SELECT p.*, t.name as topic_name, t.color as topic_color 
                FROM posts p 
                LEFT JOIN topics t ON p.topic_id = t.id 
                WHERE p.status = 1 ';
        $params = [];
        
        if ($topic_id) {
            $sql .= ' AND p.topic_id = ? ';
            $params[] = $topic_id;
        }
        
        $sql .= ' ORDER BY p.is_top DESC, p.created_at DESC LIMIT ? OFFSET ?';
        $params[] = $limit;
        $params[] = $offset;
        
        return db()->fetchAll($sql, $params);
    } catch (Exception $e) {
        return [];
    }
}

function get_post($id) {
    try {
        $sql = 'SELECT p.*, t.name as topic_name, t.color as topic_color 
                FROM posts p 
                LEFT JOIN topics t ON p.topic_id = t.id 
                WHERE p.id = ?';
        return db()->fetchOne($sql, [$id]);
    } catch (Exception $e) {
        return null;
    }
}

function get_comments($post_id) {
    try {
        return db()->fetchAll('SELECT * FROM comments WHERE post_id = ? AND status = 1 ORDER BY created_at ASC', [$post_id]);
    } catch (Exception $e) {
        return [];
    }
}

function get_banners() {
    try {
        return db()->fetchAll('SELECT * FROM banners WHERE status = 1 ORDER BY sort ASC');
    } catch (Exception $e) {
        return [];
    }
}

function upload_image($file) {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => '文件上传失败'];
    }
    
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, ALLOWED_TYPES)) {
        return ['success' => false, 'message' => '不支持的文件格式'];
    }
    
    if ($file['size'] > MAX_FILE_SIZE) {
        return ['success' => false, 'message' => '文件太大'];
    }
    
    $filename = uniqid() . '.' . $extension;
    $filepath = UPLOAD_DIR . $filename;
    
    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0755, true);
    }
    
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => true, 'url' => UPLOAD_URL . $filename];
    }
    
    return ['success' => false, 'message' => '文件保存失败'];
}
