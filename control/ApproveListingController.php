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
        // 1. Xác định tab hiện tại
        $tab = isset($_GET['tab']) ? $_GET['tab'] : 'pending';
        $status_id = 1; 

        if ($tab == 'active') $status_id = 2;
        elseif ($tab == 'rejected') $status_id = 3;
        elseif ($tab == 'hidden') $status_id = 4;

        // 2. Logic Phân trang
        $limit = 10; // Tối thiểu 10 dòng
        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) $page = 1;
        
        $offset = ($page - 1) * $limit;

        // Tính tổng số trang cho tab hiện tại
        $totalRecords = $this->approveListingModel->countListingsByStatus($status_id);
        $totalPages = ceil($totalRecords / $limit);

        // Lấy dữ liệu danh sách cho trang hiện tại
        $listings = $this->approveListingModel->getListingsByStatus($status_id, $limit, $offset);

        // 3. Lấy số lượng báo hiệu cho tất cả các tab
        $countPending = $this->approveListingModel->countListingsByStatus(1);
        $countActive = $this->approveListingModel->countListingsByStatus(2);
        $countRejected = $this->approveListingModel->countListingsByStatus(3);
        $countHidden = $this->approveListingModel->countListingsByStatus(4);

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
    // Hàm thực thi đổi trạng thái (Duyệt / Từ chối / Ẩn) bằng AJAX
    public function changeStatus() {
        // Khai báo header trả về định dạng JSON
        header('Content-Type: application/json');
        
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
                // Trả về JSON thành công kèm theo status_id mới để FE cập nhật giao diện
                echo json_encode([
                    'success' => true,
                    'message' => $msg,
                    'status_id' => $status_id
                ]);
                exit();
            }
        }
        
        // Trả về JSON thất bại nếu có lỗi xảy ra
        echo json_encode([
            'success' => false,
            'message' => 'Có lỗi xảy ra, vui lòng thử lại!'
        ]);
        exit();
    }
}
?>