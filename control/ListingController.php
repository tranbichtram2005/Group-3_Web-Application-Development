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
}
