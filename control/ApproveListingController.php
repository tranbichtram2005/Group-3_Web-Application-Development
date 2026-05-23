<?php
require_once 'model/ApproveListingModel.php';

class ApproveListingController {
    private $approveListingModel;

    public function __construct() {
        $this->approveListingModel = new ApproveListingModel();
        
        // Bảo vệ route: Chỉ Admin mới được vào
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        // if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
        //     echo "<script>alert('Không có quyền truy cập!'); window.location.href='index.php?controller=home';</script>";
        //     exit();
        // }
    }

    // Hiển thị danh sách tin đăng theo Tab
    public function index() {
        // Tab mặc định là pending (Chờ duyệt = 1)
        $tab = isset($_GET['tab']) ? $_GET['tab'] : 'pending';
        $status_id = 1; 

        if ($tab == 'active') $status_id = 2;
        elseif ($tab == 'rejected') $status_id = 3;
        elseif ($tab == 'hidden') $status_id = 4;

        // Lấy dữ liệu từ Model
        $listings = $this->approveListingModel->getListingsByStatus($status_id);

        $pageTitle = "Phê duyệt tin đăng - Admin";
        include 'view/admin/approve_listing.php';
    }

    // Hiển thị chi tiết tin đăng
  // Hiển thị chi tiết tin đăng (Đã cập nhật lấy hình ảnh)
    public function detail() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id > 0) {
            $listing = $this->approveListingModel->getListingDetail($id);
            if ($listing) {
                // Gọi thêm Model để lấy mảng hình ảnh của tin đăng này
                $images = $this->approveListingModel->getListingImages($id);
                
                $pageTitle = "Chi tiết tin đăng - Admin";
                include 'view/admin/approve_listing_detail.php';
                return;
            }
        }
        echo "<script>alert('Không tìm thấy tin đăng!'); window.location.href='index.php?controller=approvelisting';</script>";
    }

    // Hàm thực thi đổi trạng thái (Duyệt / Từ chối / Ẩn)
    public function changeStatus() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $type = isset($_GET['type']) ? $_GET['type'] : '';
        
        if ($id > 0) {
            $status_id = 1;
            $msg = "";

            if ($type == 'approve') {
                $status_id = 2; // Duyệt -> Đang bán
                $msg = "Đã phê duyệt tin đăng thành công!";
            } elseif ($type == 'reject') {
                $status_id = 3; // Từ chối
                $msg = "Đã từ chối tin đăng!";
            } elseif ($type == 'hide') {
                $status_id = 4; // Ẩn tin
                $msg = "Đã gỡ tin đăng thành công!";
            }

            if ($this->approveListingModel->updateListingStatus($id, $status_id)) {
                echo "<script>alert('$msg'); window.location.href='index.php?controller=approvelisting';</script>";
                return;
            }
        }
        echo "<script>alert('Có lỗi xảy ra, vui lòng thử lại!'); history.back();</script>";
    }
}
?>