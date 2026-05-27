<?php
require_once 'model/ManageListingModel.php';

class ManageListingController {
    private $manageModel;

    public function __construct() {
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

    public function changeStatus() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 2;
        $listingId = $_GET['id'] ?? null;
        $type = $_GET['type'] ?? '';

        if ($listingId) {
            $newStatusId = ($type === 'hide' || $type === 'sold') ? 4 : 2;
            $this->manageModel->updateListingStatus($listingId, $userId, $newStatusId);
            echo "<script>alert('Cập nhật trạng thái thành công!'); window.location.href='index.php?controller=manage_listing&action=index';</script>";
            exit();
        }
    }
}
?>