<?php
require_once 'model/ApproveSellerModel.php';

class ApproveSellerController {
    private $sellerModel;

    public function __construct() {
        $this->sellerModel = new ApproveSellerModel();
    }

    public function index() {
        $tab = $_GET['tab'] ?? 'pending';
        $isVerified = ($tab === 'approved') ? 1 : 0;
        
        $requests = $this->sellerModel->getRequestsByStatus($isVerified);
        
        $pageTitle = "Duyệt Người Bán - Admin";
        include 'view/admin/approve_seller.php'; // Đã đổi tên view
    }

    public function detail() {
        $id = $_GET['id'] ?? null;
        $request = $this->sellerModel->getRequestById($id);

        if (!$request) {
            die("Không tìm thấy hồ sơ đăng ký này!");
        }

        $pageTitle = "Chi tiết hồ sơ Shop - Admin";
        include 'view/admin/approve_seller_detail.php'; // Đã đổi tên view
    }

    public function approve() {
        $id = $_GET['id'] ?? null;
        $userId = $_GET['user_id'] ?? null;

        if ($id && $userId) {
            if ($this->sellerModel->approveRequest($id, $userId)) {
                echo "<script>alert('Đã phê duyệt thành công!'); window.location.href='index.php?controller=approveseller&action=index&tab=approved';</script>";
            } else {
                echo "<script>alert('Có lỗi hệ thống xảy ra.'); history.back();</script>";
            }
        }
    }

    public function reject() {
        $id = $_GET['id'] ?? null;

        if ($id) {
            if ($this->sellerModel->rejectRequest($id)) {
                echo "<script>alert('Đã từ chối và xóa hồ sơ.'); window.location.href='index.php?controller=approveseller&action=index';</script>";
            } else {
                echo "<script>alert('Có lỗi xảy ra.'); history.back();</script>";
            }
        }
    }
}
?>