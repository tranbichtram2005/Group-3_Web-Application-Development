<?php
require_once 'model/ListingModel.php';

class ListingController
{
    private $listingModel;

    public function __construct()
    {
        $this->listingModel = new ListingModel();
    }

    public function create()
    {
        if (session_status() == PHP_SESSION_NONE) session_start();

        if (!isset($_SESSION['user_id'])) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'Vui lòng đăng nhập để đăng tin!']);
            } else {
                echo "<script>alert('Vui lòng đăng nhập để đăng tin!'); window.location.href='index.php?controller=auth&action=login';</script>";
            }
            exit();
        }
        
        $userId = $_SESSION['user_id']; 

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');
            
            $categoryId    = $_POST['category_id'] ?? null;
            $conditionId   = $_POST['condition_id'] ?? null;
            $statusId      = 1; 
            $wardId        = $_POST['ward_id'] ?? null;
            $title         = isset($_POST['title']) ? trim($_POST['title']) : '';
            $description   = isset($_POST['description']) ? trim($_POST['description']) : '';
            $price         = $_POST['price'] ?? 0;
            $isNegotiable  = isset($_POST['is_negotiable']) ? 1 : 0;
            $stockQuantity = $_POST['stock_quantity'] ?? 1;

            $listingId = $this->listingModel->createListing(
                $userId, $categoryId, $conditionId, $statusId, $wardId, 
                $title, $description, $price, $isNegotiable, $stockQuantity
            );

            if ($listingId) {
                if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
                    $this->handleImageUploads($listingId, $_FILES['images']);
                }
                echo json_encode(['status' => 'success', 'message' => 'Đăng tin thành công! Tin của bạn đang chờ duyệt.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Có lỗi xảy ra khi lưu tin đăng vào hệ thống.']);
            }
            exit();
            
        } else {
            $categories = $this->listingModel->getAllCategories();
            $conditions = $this->listingModel->getAllConditions();
            // Lấy danh sách Tỉnh Thành thay vì Phường Xã
            $provinces  = $this->listingModel->getProvinces(); 
            
            include 'view/post-product.php';
        }
    }

    private function handleImageUploads($listingId, $files)
    {
        $uploadDir = 'uploads/listings/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $totalImages = count($files['name']);
        $sortOrder = 1;

        for ($i = 0; $i < $totalImages; $i++) {
            if ($files['error'][$i] === UPLOAD_ERR_OK) {
                $fileExtension = strtolower(pathinfo($files['name'][$i], PATHINFO_EXTENSION));
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
                if (!in_array($fileExtension, $allowedExtensions)) continue;

                $newFileName = time() . '_' . rand(1000, 9999) . '.' . $fileExtension;
                $targetFilePath = $uploadDir . $newFileName;

                if (move_uploaded_file($files['tmp_name'][$i], $targetFilePath)) {
                    // ĐÃ FIX LỖI ẢNH: Xóa dấu / ở đầu
                    $imageUrl = $targetFilePath; 
                    $isPrimary = ($i === 0) ? 1 : 0;
                    $this->listingModel->addListingImage($listingId, $imageUrl, $sortOrder, $isPrimary);
                    $sortOrder++;
                }
            }
        }
    }

    public function search() {
        $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
        $categories = $this->listingModel->getAllCategories();
        $limit = 8; 
        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $limit;
        $totalListings = $this->listingModel->getTotalActiveListings($keyword);
        $totalPages = ceil($totalListings / $limit);
        $listings = $this->listingModel->getPaginatedListings($limit, $offset, $keyword);
        require_once __DIR__ . '/../view/app/home.php';
    }

    public function detail() {
        require_once __DIR__ . '/../model/VoucherModel.php';
        $voucherModel = new VoucherModel();
        $activeVouchers = $voucherModel->getActiveVouchers();
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $product = $this->listingModel->getListingDetail($id);
        if (!$product) {
            die("<h2 style='text-align:center; margin-top:50px; color:gray;'>Sản phẩm không tồn tại hoặc đã bị ẩn!</h2>");
        }
        $images = $this->listingModel->getListingImages($id);

        // --- GỌI MODEL ĐỂ LẤY DỮ LIỆU ĐÁNH GIÁ ---
        // 1. Thống kê sao
        $stats = $this->listingModel->getProductReviewStats($id);
        $avgRating = $stats['avg_rating'] ? round($stats['avg_rating'], 1) : 0;
        $totalReviews = $stats['total_reviews'] ?? 0;

        $filterStar = isset($_GET['star']) ? (int)$_GET['star'] : 0;

        // 2. Danh sách bình luận (có truyền thêm biến $filterStar xuống Model)
        $productReviews = $this->listingModel->getProductReviews($id, $filterStar);
        require_once __DIR__ . '/../view/app/listing-detail.php';
    }

    public function category() {
        $categoryId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $categories = $this->listingModel->getAllCategories();
        $limit = 8; 
        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $limit;
        $totalListings = $this->listingModel->getTotalActiveListingsByCategory($categoryId);
        $totalPages = ceil($totalListings / $limit);
        $listings = $this->listingModel->getPaginatedListingsByCategory($limit, $offset, $categoryId);

        $currentCategoryName = "Sản phẩm theo danh mục";
        foreach($categories as $c) {
            if($c['id'] == $categoryId) {
                $currentCategoryName = $c['name'];
                break;
            }
        }
        $_GET['keyword'] = "Danh mục: " . $currentCategoryName; 
        require_once __DIR__ . '/../view/app/home.php';
    }

    public function edit() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        
        if (!isset($_SESSION['user_id'])) {
            echo "<script>alert('Vui lòng đăng nhập!'); window.location.href='index.php?controller=auth&action=login';</script>";
            return;
        }
        $userId = $_SESSION['user_id']; 

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $product = $this->listingModel->getListingForEdit($id, $userId);

        if (!$product) {
            echo "<script>alert('Tin đăng không tồn tại hoặc bạn không có quyền sửa!'); history.back();</script>";
            return;
        }

        // BỔ SUNG: Lấy toàn bộ ảnh cũ của tin đăng này để truyền qua View
        $images     = $this->listingModel->getListingImages($id); 
        
        $categories = $this->listingModel->getAllCategories();
        $conditions = $this->listingModel->getAllConditions();
        $provinces  = $this->listingModel->getProvinces(); 
        
        // --- BỔ SUNG: Truy xuất ngược Quận, Phường cũ để đổ ra Edit form ---
        $districts = [];
        $wards = [];
        if (isset($product['province_id']) && $product['province_id']) {
            $districts = $this->listingModel->getDistrictsByProvince($product['province_id']);
        }
        if (isset($product['district_id']) && $product['district_id']) {
            $wards = $this->listingModel->getWardsByDistrict($product['district_id']);
        }

        include 'view/post-product.php';
    }

    public function update() {
        if (session_status() == PHP_SESSION_NONE) session_start();

        if (!isset($_SESSION['user_id'])) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'Vui lòng đăng nhập lại!']);
            }
            exit();
        }
        $userId = $_SESSION['user_id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json'); 
            
            $listingId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

            if ($listingId === 0) {
                echo json_encode(['status' => 'error', 'message' => 'Không tìm thấy ID tin đăng!']);
                exit();
            }

            $categoryId    = $_POST['category_id'] ?? null;
            $conditionId   = $_POST['condition_id'] ?? null;
            $wardId        = $_POST['ward_id'] ?? null;
            $title         = isset($_POST['title']) ? trim($_POST['title']) : '';
            $description   = isset($_POST['description']) ? trim($_POST['description']) : '';
            $price         = $_POST['price'] ?? 0;
            $stockQuantity = $_POST['stock_quantity'] ?? 1;

            $statusId = 1; 

            $result = $this->listingModel->updateListing(
                $listingId, $userId, $categoryId, $conditionId, $statusId, 
                $wardId, $title, $description, $price, $stockQuantity
            );

            if ($result) {
                if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
                    $this->listingModel->deleteListingImages($listingId);
                    $this->handleImageUploads($listingId, $_FILES['images']);
                }
                echo json_encode(['status' => 'success', 'message' => 'Cập nhật tin đăng thành công!']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Cập nhật thất bại (Hoặc bạn chưa thay đổi thông tin nào).']);
            }
            exit();
        }
    }

    // =======================================================
    // CÁC HÀM AJAX API LẤY DROPDOWN ĐỊA CHỈ
    // =======================================================
    public function getDistrictsAjax() {
        header('Content-Type: application/json');
        $provinceId = isset($_GET['province_id']) ? (int)$_GET['province_id'] : 0;
        if ($provinceId > 0) {
            $districts = $this->listingModel->getDistrictsByProvince($provinceId);
            echo json_encode($districts);
        } else {
            echo json_encode([]);
        }
        exit();
    }

    public function getWardsAjax() {
        header('Content-Type: application/json');
        $districtId = isset($_GET['district_id']) ? (int)$_GET['district_id'] : 0;
        if ($districtId > 0) {
            $wards = $this->listingModel->getWardsByDistrict($districtId);
            echo json_encode($wards);
        } else {
            echo json_encode([]);
        }
        exit();
    }

    public function suggestAjax() {
    header('Content-Type: application/json; charset=utf-8'); 
    $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
    
    if (strlen($keyword) < 2) { // Nếu gõ dưới 2 ký tự thì trả về rỗng ngay
        echo json_encode([]);
        exit;
    }

    // Gọi hàm mới tối ưu (siêu nhanh)
    $listings = $this->listingModel->getSearchSuggestions($keyword, 5);
    echo json_encode($listings);
    exit;
}
}
?>