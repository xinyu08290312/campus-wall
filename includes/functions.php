<?php
if (!defined('FUNCTIONS_LOADED')) {
    define('FUNCTIONS_LOADED', true);
    
    require_once __DIR__ . '/../config/config.php';
    require_once __DIR__ . '/../config/database.php';

    // XSS防护
    if (!function_exists('e')) {
        function e($string) {
            return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
        }
    }

    // 格式化时间
    if (!function_exists('format_time')) {
        function format_time($datetime) {
            if (!$datetime) return '';
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
    }

    // ===== 用户相关函数 =====

    // 检查用户是否登录
    if (!function_exists('is_user_logged_in')) {
        function is_user_logged_in() {
            return isset($_SESSION['user_id']) && $_SESSION['user_id'] > 0;
        }
    }

    // 获取当前用户信息
    if (!function_exists('get_logged_in_user')) {
        function get_logged_in_user() {
            if (!is_user_logged_in()) {
                return null;
            }
            try {
                return db()->fetchOne('SELECT * FROM users WHERE id = ? AND status = 1', [$_SESSION['user_id']]);
            } catch (Exception $e) {
                return null;
            }
        }
    }

    // 要求用户登录
    if (!function_exists('require_user_login')) {
        function require_user_login() {
            if (!is_user_logged_in()) {
                header('Location: login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
                exit;
            }
        }
    }

    // 用户注册
    if (!function_exists('register_user')) {
        function register_user($username, $email, $password) {
            try {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $user_id = db()->insert('users', [
                    'username' => $username,
                    'email' => $email,
                    'password' => $hashed_password,
                    'avatar' => '/assets/images/default-avatar.png'
                ]);
                return ['success' => true, 'user_id' => $user_id];
            } catch (Exception $e) {
                return ['success' => false, 'message' => $e->getMessage()];
            }
        }
    }

    // 用户登录验证
    if (!function_exists('login_user')) {
        function login_user($username, $password) {
            try {
                $user = db()->fetchOne('SELECT * FROM users WHERE username = ? OR email = ?', [$username, $username]);
                if ($user) {
                    if (password_verify($password, $user['password'])) {
                        if ($user['status'] == 1) {
                            $_SESSION['user_id'] = $user['id'];
                            $_SESSION['username'] = $user['username'];
                            return ['success' => true, 'user' => $user];
                        } else {
                            return ['success' => false, 'message' => '账号已被禁用'];
                        }
                    } else {
                        return ['success' => false, 'message' => '密码错误'];
                    }
                } else {
                    return ['success' => false, 'message' => '用户不存在'];
                }
            } catch (Exception $e) {
                return ['success' => false, 'message' => '登录失败，请稍后重试'];
            }
        }
    }

    // 用户登出
    if (!function_exists('logout_user')) {
        function logout_user() {
            unset($_SESSION['user_id']);
            unset($_SESSION['username']);
        }
    }

    // 获取用户信息
    if (!function_exists('get_user')) {
        function get_user($id) {
            try {
                return db()->fetchOne('SELECT id, username, avatar, bio, created_at FROM users WHERE id = ? AND status = 1', [$id]);
            } catch (Exception $e) {
                return null;
            }
        }
    }

    // ===== 管理员相关函数 =====

    if (!function_exists('is_admin_logged_in')) {
        function is_admin_logged_in() {
            return isset($_SESSION['admin_id']) && $_SESSION['admin_id'] > 0;
        }
    }

    if (!function_exists('require_admin_login')) {
        function require_admin_login() {
            if (!is_admin_logged_in()) {
                header('Location: login.php');
                exit;
            }
        }
    }

    // ===== 话题相关函数 =====

    if (!function_exists('get_topics')) {
        function get_topics() {
            try {
                return db()->fetchAll('SELECT * FROM topics WHERE status = 1 ORDER BY sort ASC, id ASC');
            } catch (Exception $e) {
                return [];
            }
        }
    }

    if (!function_exists('get_topic')) {
        function get_topic($id) {
            try {
                return db()->fetchOne('SELECT * FROM topics WHERE id = ?', [$id]);
            } catch (Exception $e) {
                return null;
            }
        }
    }

    // ===== 动态相关函数 =====

    if (!function_exists('get_posts')) {
        function get_posts($topic_id = null, $page = 1, $limit = 20) {
            try {
                $offset = ($page - 1) * $limit;
                $sql = 'SELECT p.*, u.username, u.avatar, t.name as topic_name, t.color as topic_color 
                        FROM posts p 
                        LEFT JOIN users u ON p.user_id = u.id 
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
    }

    if (!function_exists('get_post')) {
        function get_post($id) {
            try {
                return db()->fetchOne('SELECT p.*, u.username, u.avatar, t.name as topic_name, t.color as topic_color 
                                      FROM posts p 
                                      LEFT JOIN users u ON p.user_id = u.id 
                                      LEFT JOIN topics t ON p.topic_id = t.id 
                                      WHERE p.id = ?', [$id]);
            } catch (Exception $e) {
                return null;
            }
        }
    }

    if (!function_exists('get_user_posts')) {
        function get_user_posts($user_id, $page = 1, $limit = 20) {
            try {
                $offset = ($page - 1) * $limit;
                return db()->fetchAll('SELECT p.*, t.name as topic_name, t.color as topic_color 
                                      FROM posts p 
                                      LEFT JOIN topics t ON p.topic_id = t.id 
                                      WHERE p.user_id = ? AND p.status = 1 
                                      ORDER BY p.created_at DESC 
                                      LIMIT ? OFFSET ?', [$user_id, $limit, $offset]);
            } catch (Exception $e) {
                return [];
            }
        }
    }

    if (!function_exists('create_post')) {
        function create_post($user_id, $content, $topic_id = null, $images = null) {
            try {
                $post_id = db()->insert('posts', [
                    'user_id' => $user_id,
                    'topic_id' => $topic_id,
                    'content' => $content,
                    'images' => $images
                ]);
                return ['success' => true, 'post_id' => $post_id];
            } catch (Exception $e) {
                return ['success' => false, 'message' => $e->getMessage()];
            }
        }
    }

    if (!function_exists('delete_post')) {
        function delete_post($post_id, $user_id) {
            try {
                $post = db()->fetchOne('SELECT * FROM posts WHERE id = ? AND user_id = ?', [$post_id, $user_id]);
                if (!$post) {
                    return ['success' => false, 'message' => '无权删除'];
                }
                db()->delete('posts', 'id = ?', [$post_id]);
                db()->delete('comments', 'post_id = ?', [$post_id]);
                db()->delete('likes', 'post_id = ?', [$post_id]);
                return ['success' => true];
            } catch (Exception $e) {
                return ['success' => false, 'message' => $e->getMessage()];
            }
        }
    }

    // ===== 评论相关函数 =====

    if (!function_exists('get_comments')) {
        function get_comments($post_id) {
            try {
                return db()->fetchAll('SELECT c.*, u.username, u.avatar 
                                      FROM comments c 
                                      LEFT JOIN users u ON c.user_id = u.id 
                                      WHERE c.post_id = ? AND c.status = 1 
                                      ORDER BY c.created_at ASC', [$post_id]);
            } catch (Exception $e) {
                return [];
            }
        }
    }

    if (!function_exists('create_comment')) {
        function create_comment($post_id, $user_id, $content) {
            try {
                db()->insert('comments', [
                    'post_id' => $post_id,
                    'user_id' => $user_id,
                    'content' => $content
                ]);
                
                // 更新评论数
                $post = db()->fetchOne('SELECT user_id FROM posts WHERE id = ?', [$post_id]);
                if ($post) {
                    db()->query('UPDATE posts SET comments_count = comments_count + 1 WHERE id = ?', [$post_id]);
                    
                    // 发送通知
                    if ($post['user_id'] != $user_id) {
                        create_notification($post['user_id'], 'comment', '有人评论了你的动态', $user_id, $post_id);
                    }
                }
                
                return ['success' => true];
            } catch (Exception $e) {
                return ['success' => false, 'message' => $e->getMessage()];
            }
        }
    }

    // ===== 点赞相关函数 =====

    if (!function_exists('is_liked')) {
        function is_liked($user_id, $post_id = null, $comment_id = null) {
            try {
                if ($post_id) {
                    $like = db()->fetchOne('SELECT * FROM likes WHERE user_id = ? AND post_id = ?', [$user_id, $post_id]);
                    return $like ? true : false;
                }
                if ($comment_id) {
                    $like = db()->fetchOne('SELECT * FROM likes WHERE user_id = ? AND comment_id = ?', [$user_id, $comment_id]);
                    return $like ? true : false;
                }
                return false;
            } catch (Exception $e) {
                return false;
            }
        }
    }

    if (!function_exists('toggle_like')) {
        function toggle_like($user_id, $post_id = null, $comment_id = null) {
            try {
                if ($post_id) {
                    if (is_liked($user_id, $post_id)) {
                        db()->delete('likes', 'user_id = ? AND post_id = ?', [$user_id, $post_id]);
                        db()->query('UPDATE posts SET likes = likes - 1 WHERE id = ? AND likes > 0', [$post_id]);
                        return ['success' => true, 'action' => 'unlike'];
                    } else {
                        db()->insert('likes', ['user_id' => $user_id, 'post_id' => $post_id]);
                        db()->query('UPDATE posts SET likes = likes + 1 WHERE id = ?', [$post_id]);
                        
                        $post = db()->fetchOne('SELECT user_id FROM posts WHERE id = ?', [$post_id]);
                        if ($post && $post['user_id'] != $user_id) {
                            create_notification($post['user_id'], 'like', '有人点赞了你的动态', $user_id, $post_id);
                        }
                        
                        return ['success' => true, 'action' => 'like'];
                    }
                }
                return ['success' => false];
            } catch (Exception $e) {
                return ['success' => false, 'message' => $e->getMessage()];
            }
        }
    }

    // ===== 轮播图相关函数 =====

    if (!function_exists('get_banners')) {
        function get_banners() {
            try {
                return db()->fetchAll('SELECT * FROM banners WHERE status = 1 ORDER BY sort ASC');
            } catch (Exception $e) {
                return [];
            }
        }
    }

    // ===== 通知相关函数 =====

    if (!function_exists('get_notifications')) {
        function get_notifications($user_id, $limit = 50) {
            try {
                return db()->fetchAll('SELECT n.*, u.username as from_username, u.avatar as from_avatar 
                                      FROM notifications n 
                                      LEFT JOIN users u ON n.from_user_id = u.id 
                                      WHERE n.user_id = ? 
                                      ORDER BY n.created_at DESC 
                                      LIMIT ?', [$user_id, $limit]);
            } catch (Exception $e) {
                return [];
            }
        }
    }

    if (!function_exists('get_unread_notification_count')) {
        function get_unread_notification_count($user_id) {
            try {
                $result = db()->fetchOne('SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = 0', [$user_id]);
                return $result['count'] ?? 0;
            } catch (Exception $e) {
                return 0;
            }
        }
    }

    if (!function_exists('create_notification')) {
        function create_notification($user_id, $type, $content, $from_user_id = null, $post_id = null) {
            try {
                db()->insert('notifications', [
                    'user_id' => $user_id,
                    'type' => $type,
                    'content' => $content,
                    'from_user_id' => $from_user_id,
                    'post_id' => $post_id
                ]);
                return true;
            } catch (Exception $e) {
                return false;
            }
        }
    }

    if (!function_exists('mark_notifications_read')) {
        function mark_notifications_read($user_id) {
            try {
                db()->query('UPDATE notifications SET is_read = 1 WHERE user_id = ?', [$user_id]);
                return true;
            } catch (Exception $e) {
                return false;
            }
        }
    }

    // ===== 图片上传函数 =====

    if (!function_exists('upload_image')) {
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
            
            $image_info = getimagesize($file['tmp_name']);
            if (!$image_info) {
                return ['success' => false, 'message' => '文件不是有效的图片'];
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
    }

    // ===== 统计函数 =====

    if (!function_exists('get_user_stats')) {
        function get_user_stats($user_id) {
            try {
                $posts_count = db()->fetchOne('SELECT COUNT(*) as count FROM posts WHERE user_id = ? AND status = 1', [$user_id])['count'];
                $comments_count = db()->fetchOne('SELECT COUNT(*) as count FROM comments WHERE user_id = ? AND status = 1', [$user_id])['count'];
                $likes_count = db()->fetchOne('SELECT COUNT(*) as count FROM likes WHERE user_id = ?', [$user_id])['count'];
                
                return [
                    'posts' => $posts_count,
                    'comments' => $comments_count,
                    'likes' => $likes_count
                ];
            } catch (Exception $e) {
                return ['posts' => 0, 'comments' => 0, 'likes' => 0];
            }
        }
    }
}
