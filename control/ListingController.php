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
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Bóc tách dữ liệu từ Form
            $userId        = $_SESSION['user']['id'] ?? 2; // Giả lập user 2 nếu chưa đăng nhập
            $categoryId    = $_POST['category_id'] ?? null;
            $conditionId   = $_POST['condition_id'] ?? null;
            $statusId      = 1; // 1 = Chờ duyệt (Pending)
            $wardId        = $_POST['ward_id'] ?? null;
            $title         = isset($_POST['title']) ? trim($_POST['title']) : '';
            $description   = isset($_POST['description']) ? trim($_POST['description']) : '';
            $price         = $_POST['price'] ?? 0;
            $isNegotiable  = isset($_POST['is_negotiable']) ? 1 : 0;
            $stockQuantity = $_POST['stock_quantity'] ?? 1;

            // Truyền chính xác 10 biến theo đúng thứ tự khai báo trong ListingModel
            $listingId = $this->listingModel->createListing(
                $userId,
                $categoryId,
                $conditionId,
                $statusId,
                $wardId,
                $title,
                $description,
                $price,
                $isNegotiable,
                $stockQuantity
            );

            if ($listingId) {
                if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
                    $this->handleImageUploads($listingId, $_FILES['images']);
                }
                
                // Trả về JSON thông báo Thành công thay vì xuất thẻ <script>
                header('Content-Type: application/json');
                echo json_encode(['status' => 'success', 'message' => 'Đăng tin thành công! Tin của bạn đang chờ duyệt.']);
                exit();
            } else {
                
                // Trả về JSON thông báo Lỗi
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'Có lỗi xảy ra khi lưu tin đăng vào hệ thống.']);
                exit();
            }
        } else {
            // ĐÃ SỬA LỖI: Gọi đúng tên hàm get data từ Model để hiển thị giao diện
            $categories = $this->listingModel->getAllCategories();
            $conditions = $this->listingModel->getAllConditions();
            $wards      = $this->listingModel->getWards();

            include 'view/post-product.php';
        }
    }
    
    // =======================================================
    // HÀM PHỤ TRỢ: XỬ LÝ UPLOAD HÌNH ẢNH
    // =======================================================
    private function handleImageUploads($listingId, $files)
    {
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

    // Chức năng 2: Xử lý tìm kiếm sản phẩm phân trang
    public function search() {
        $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
        $categories = $this->listingModel->getAllCategories();

        $limit = 8; 
        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $limit;

        $totalListings = $this->listingModel->getTotalActiveListings($keyword);
        $totalPages = ceil($totalListings / $limit);

        $listings = $this->listingModel->getPaginatedListings($limit, $offset, $keyword);

        // Tái sử dụng lại view home để render kết quả tìm kiếm cho sạch code
        require_once __DIR__ . '/../view/app/home.php';
    }


    // =======================================================
    // CHỨC NĂNG 3: XEM CHI TIẾT SẢN PHẨM (ĐÃ CHÈN LOGIC ĐÁNH GIÁ VÀO ĐÂY)
    // =======================================================
    public function detail() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        // Lấy data sản phẩm + thông tin người bán
        $product = $this->listingModel->getListingDetail($id);
        
        if (!$product) {
            die("<h2 style='text-align:center; margin-top:50px; color:gray;'>Sản phẩm không tồn tại hoặc đã bị ẩn!</h2>");
        }

        // Lấy danh sách ảnh của sản phẩm
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

    // Chức năng 4: Xem sản phẩm theo Danh mục
    public function category() {
        $categoryId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        // Vẫn phải lấy danh sách các danh mục để hiển thị thanh scroll ở home
        $categories = $this->listingModel->getAllCategories();

        $limit = 8; 
        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $limit;

        $totalListings = $this->listingModel->getTotalActiveListingsByCategory($categoryId);
        $totalPages = ceil($totalListings / $limit);

        $listings = $this->listingModel->getPaginatedListingsByCategory($limit, $offset, $categoryId);

        // Lấy tên danh mục đang chọn để hiển thị ra cho đẹp (tuỳ chọn)
        $currentCategoryName = "Sản phẩm theo danh mục";
        foreach($categories as $c) {
            if($c['id'] == $categoryId) {
                $currentCategoryName = $c['name'];
                break;
            }
        }
        // Gắn vào biến $_GET giả để file home.php nhận diện và in ra tiêu đề
        $_GET['keyword'] = "Danh mục: " . $currentCategoryName;

        // Tái sử dụng lại view home
        require_once __DIR__ . '/../view/app/home.php';
    }

   // Chức năng: Live Search Ajax (Bản bảo mật)
    public function suggestAjax() {
        // Mở mắt cho PHP để nó báo lỗi thật nếu có
        error_reporting(E_ALL); 
        ini_set('display_errors', 1);
        
        if (ob_get_length()) {
            ob_clean();
        }
        
        header('Content-Type: application/json; charset=utf-8'); 
        
        // BÍ KÍP CHỐNG LỖI: Bắt cả GET lẫn POST, bắt cả biến 'keyword' lẫn biến 'q'
        $keyword = isset($_REQUEST['keyword']) ? trim($_REQUEST['keyword']) : (isset($_REQUEST['q']) ? trim($_REQUEST['q']) : '');
        
        if (empty($keyword)) {
            echo json_encode([]);
            exit;
        }

        try {
            // Lấy 5 sản phẩm khớp tên
            $listings = $this->listingModel->getPaginatedListings(5, 0, $keyword);
            
            // Ép mảng về JSON
            echo json_encode($listings);
        } catch (Exception $e) {
            // Nếu Database gào thét, nhét câu chửi của nó vào JSON để mình dễ đọc
            echo json_encode([
                'error_cua_phung' => 'Lỗi Database: ' . $e->getMessage()
            ]); 
        }
        exit;
    }
}