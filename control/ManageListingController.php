<?php
require_once 'model/ManageListingModel.php';

class ManageListingController {
    private $manageModel;

    public function __construct() {
        $this->manageModel = new ManageListingModel();
    }

    // Hiển thị trang quản lý tin đăng
    public function index() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Giả lập User ID = 2 (Lê Văn Bình / Trần Thị Lan đang login để test dữ liệu mẫu)
        $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 2;

        // Nhận tham số Tab lọc và Từ khóa tìm kiếm từ URL
        $currentTab = $_GET['tab'] ?? 'all';
        $searchKeyword = $_GET['search'] ?? '';

        // Lấy dữ liệu từ Model
        $listings = $this->manageModel->getSellerListings($userId, $currentTab, $searchKeyword);
        $counts = $this->manageModel->getStatusCounts($userId);

        // Include file View giao diện quản lý
        include 'view/manage-listing.php';
    }

    // Xử lý thay đổi nhanh trạng thái tin (Ẩn tin hoặc Đánh dấu Đã bán)
    public function changeStatus() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 2;

        $listingId = $_GET['id'] ?? null;
        $type = $_GET['type'] ?? '';

        if ($listingId) {
            $newStatusId = 2; // Mặc định Đang bán
            if ($type === 'hide') {
                $newStatusId = 4; // 4 = hidden (Đã ẩn)
            } elseif ($type === 'sold') {
                $newStatusId = 4; // Hoặc nếu DB của bạn có trạng thái 'Đã bán' riêng biệt, bạn điền ID vào đây. Tạm thời gom vào Đã đóng/ẩn.
            }

            $result = $this->manageModel->updateListingStatus($listingId, $userId, $newStatusId);
            
            if ($result) {
                echo "<script>alert('Cập nhật trạng thái tin đăng thành công!'); window.location.href='index.php?controller=manage_listing&action=index';</script>";
            } else {
                echo "<script>alert('Có lỗi xảy ra, vui lòng thử lại.'); history.back();</script>";
            }
            exit();
        }
    }
}
?>