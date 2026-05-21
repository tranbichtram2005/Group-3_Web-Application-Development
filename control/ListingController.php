<?php
require_once 'model/ListingModel.php';

class ListingController {
    private $listingModel;

    public function __construct() {
        $this->listingModel = new ListingModel();
    }
    
    public function create() {
        // ------------------------------------------------------------------
        // NHÁNH 1: XỬ LÝ KHI NGƯỜI DÙNG BẤM NÚT "ĐĂNG BÁN NGAY" (SUBMIT FORM)
        // ------------------------------------------------------------------
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            
            // 1. Lấy thông tin người đăng (Giả lập user ID = 2 nếu chưa có chức năng login)
            $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 2; 
            
            // 2. Nhận và dọn dẹp dữ liệu từ form
            $title = trim($_POST['title'] ?? '');
            $categoryId = $_POST['category_id'] ?? null;
            $conditionId = $_POST['condition_id'] ?? null;
            $price = $_POST['price'] ?? 0;
            $wardId = $_POST['ward_id'] ?? null; 
            $description = trim($_POST['description'] ?? '');
            $isNegotiable = isset($_POST['is_negotiable']) ? 1 : 0; // Xử lý checkbox
            
            // Dữ liệu mặc định cho tin mới
            $stockQuantity = 1; 
            $statusId = 1; // 1 = 'pending' (chờ admin duyệt)

            // Validate dữ liệu trống
            if(empty($title) || empty($categoryId) || empty($conditionId) || empty($price) || empty($wardId) || empty($description)) {
                echo "<script>alert('Vui lòng điền đầy đủ thông tin bắt buộc!'); history.back();</script>";
                return;
            }

            // 3. Lưu thông tin tin đăng vào bảng product_listings
            $listingId = $this->listingModel->createListing(
                $userId, $categoryId, $conditionId, $statusId, $wardId, 
                $title, $description, $price, $isNegotiable, $stockQuantity
            );

            // 4. Upload ảnh nếu Insert DB thành công
            if ($listingId) {
                if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
                    $this->handleImageUploads($listingId, $_FILES['images']);
                }
                echo "<script>alert('Đăng tin thành công! Tin của bạn đang chờ duyệt.'); window.location.href='index.php';</script>";
            } else {
                echo "<script>alert('Có lỗi xảy ra khi lưu tin đăng vào hệ thống.'); history.back();</script>";
            }
        
        // ------------------------------------------------------------------
        // NHÁNH 2: XỬ LÝ KHI NGƯỜI DÙNG VỪA VÀO TRANG ĐĂNG TIN (GET)
        // ------------------------------------------------------------------
        } else {
            // Lấy dữ liệu Category và Condition từ DB để truyền ra View
            $categories = $this->listingModel->getAllCategories();
            $conditions = $this->listingModel->getAllConditions();
            include 'view/post-product.php';
        }
    }

    // =======================================================
    // HÀM PHỤ TRỢ: XỬ LÝ UPLOAD HÌNH ẢNH
    // =======================================================
    private function handleImageUploads($listingId, $files) {
        $uploadDir = 'uploads/listings/'; 
        
        // Tạo thư mục nếu chưa tồn tại
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $totalImages = count($files['name']);
        $sortOrder = 1;

        for ($i = 0; $i < $totalImages; $i++) {
            if ($files['error'][$i] === UPLOAD_ERR_OK) {
                $fileExtension = strtolower(pathinfo($files['name'][$i], PATHINFO_EXTENSION));
                
                // Validate định dạng ảnh
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
                if (!in_array($fileExtension, $allowedExtensions)) continue;

                // Đổi tên file để tránh trùng lặp
                $newFileName = time() . '_' . rand(1000, 9999) . '.' . $fileExtension;
                $targetFilePath = $uploadDir . $newFileName;

                // Di chuyển file từ thư mục tạm vào thư mục dự án
                if (move_uploaded_file($files['tmp_name'][$i], $targetFilePath)) {
                    $imageUrl = '/' . $targetFilePath;
                    $isPrimary = ($i === 0) ? 1 : 0; // Ảnh đầu tiên là ảnh đại diện
                    
                    $this->listingModel->addListingImage($listingId, $imageUrl, $sortOrder, $isPrimary);
                    $sortOrder++;
                }
            }
        }
    }
}
?>