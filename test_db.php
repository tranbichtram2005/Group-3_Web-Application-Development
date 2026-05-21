<?php
// Bật hiển thị mọi lỗi của PHP để dễ debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'model/Database.php';

echo "<h1>Kiểm tra kết nối Database</h1>";

// 1. Thử khởi tạo Database
$db = new Database();
$conn = $db->getConnection();

if ($conn) {
    echo "<h2 style='color: green;'>✅ KẾT NỐI DATABASE THÀNH CÔNG!</h2>";
    
    // 2. Thử truy vấn dữ liệu từ bảng categories
    try {
        $sql = "SELECT * FROM categories";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($categories) > 0) {
            echo "<p style='color: blue;'>✅ Đã lấy được " . count($categories) . " danh mục từ CSDL.</p>";
            echo "<pre>";
            print_r($categories);
            echo "</pre>";
        } else {
            echo "<p style='color: orange;'>⚠️ Kết nối thành công nhưng bảng `categories` đang trống (không có dữ liệu).</p>";
        }
    } catch(PDOException $e) {
        echo "<p style='color: red;'>❌ Lỗi truy vấn SQL: " . $e->getMessage() . "</p>";
    }

} else {
    echo "<h2 style='color: red;'>❌ KẾT NỐI THẤT BẠI!</h2>";
    echo "<p>Vui lòng kiểm tra lại thông tin cấu hình.</p>";
}
?>