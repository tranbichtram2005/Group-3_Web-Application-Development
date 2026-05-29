<?php
require_once 'model/ManageListingModel.php';

class ManageListingController {
    private $manageModel;

    public function __construct() {
        $roleId = $_SESSION['role_id'] ?? 1;
        $isSeller = $_SESSION['is_seller'] ?? 0;
// Nếu KHÔNG PHẢI là Admin (role 2) VÀ CŨNG KHÔNG PHẢI là Người bán (is_seller 1)
        if ($roleId != 2 && $isSeller != 1) {
            
            // Gửi một tín hiệu vào Session để yêu cầu bật Modal
            $_SESSION['show_unauth_modal'] = true;
            
            // Đá người dùng về lại trang chủ an toàn
            header("Location: index.php?controller=home");
            exit; 
        }
        $this->manageModel = new ManageListingModel();
    }

    public function index() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 2;

        $currentTab = $_GET['tab'] ?? 'all';
        $searchKeyword = $_GET['search'] ?? '';

        // Phân trang
        $limit = 5; 
        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $limit;

        $totalListings = $this->manageModel->getTotalSellerListings($userId, $currentTab, $searchKeyword);
        $totalPages = ceil($totalListings / $limit);

        $listings = $this->manageModel->getSellerListings($userId, $currentTab, $searchKeyword, $limit, $offset);
        $counts = $this->manageModel->getStatusCounts($userId);

        include 'view/manage-listing.php';
    }

    // AJAX: Lấy data cho Modal
    public function ajaxGetDetail() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 2;
        $id = $_GET['id'] ?? 0;

        $listing = $this->manageModel->getListingById($id, $userId);
        if($listing) {
            echo json_encode(['status' => 'success', 'data' => $listing]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Không tìm thấy dữ liệu']);
        }
        exit;
    }

    // AJAX: Xóa tin đăng
    public function ajaxDelete() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 2;
        $id = $_POST['id'] ?? 0;

        $result = $this->manageModel->deleteListing($id, $userId);
        if($result) {
            echo json_encode(['status' => 'success', 'message' => 'Xóa tin đăng thành công!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Xóa thất bại!']);
        }
        exit;
    }

// AJAX: Cập nhật trạng thái tin đăng (Ẩn tin)
    public function changeStatus() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 2;
        $listingId = $_GET['id'] ?? null;
        $type = $_GET['type'] ?? '';

        // Báo cho trình duyệt biết dữ liệu trả về là JSON
        header('Content-Type: application/json');

        if ($listingId) {
            $newStatusId = ($type === 'hide' || $type === 'sold') ? 4 : 2;
            
            // Thực thi update
            $result = $this->manageModel->updateListingStatus($listingId, $userId, $newStatusId);
            
            // Trả về JSON để AJAX xử lý
            if ($result !== false) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Đã ẩn tin đăng thành công!',
                    'status_id' => $newStatusId
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Cập nhật thất bại. Vui lòng thử lại!'
                ]);
            }
            exit();
        }
        
        echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ!']);
        exit();
    }
}
?>