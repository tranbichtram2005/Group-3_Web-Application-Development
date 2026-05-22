<?php
class AdminController {
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        // RULE BẢO MẬT: Nếu không phải Admin (role_id != 2), đá về trang chủ
        if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 2) {
            header("Location: index.php?controller=home");
            exit;
        }
    }

    public function index() {
        // Đường dẫn tới View Admin
        require_once __DIR__ . '/../view/admin/admin-home.php';
    }
}