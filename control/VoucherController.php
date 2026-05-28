<?php
require_once __DIR__ . '/../model/VoucherModel.php';

class VoucherController {
    private $voucherModel;

    public function __construct() {
        // Ép múi giờ Việt Nam cho tất cả các thao tác ngày tháng
        date_default_timezone_set('Asia/Ho_Chi_Minh');

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Tạm tắt bảo vệ phân quyền để cậu test
        /*
        if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) { 
            header("Location: index.php?controller=auth&action=login");
            exit;
        }
        */
        
        $this->voucherModel = new VoucherModel();
    }

    public function index() {
        $vouchers = $this->voucherModel->getAllVouchers();
        // Đã sửa đường dẫn trỏ vào thư mục admin
        require_once __DIR__ . '/../view/admin/manage_voucher.php';
    }

    public function createVoucher() {
        $hasError = false;
        $errorMessage = "";

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $code          = strtoupper(trim($_POST['code'] ?? ''));
            $typeId        = intval($_POST['typeId'] ?? 2); 
            $discountValue = intval($_POST['discountValue'] ?? 0);
            $maxDiscount   = !empty($_POST['maxDiscount']) ? intval($_POST['maxDiscount']) : null;
            $minOrderValue = intval($_POST['minOrderValue'] ?? 0);
            $totalQty      = intval($_POST['totalQuantity'] ?? 100);
            $startsAt      = $_POST['startsAt'] ?? date('Y-m-d H:i:s');
            $expiryDate    = $_POST['expiryDate'] ?? '';

            if (empty($code) || $discountValue <= 0 || empty($expiryDate) || $totalQty <= 0) {
                $hasError     = true;
                $errorMessage = "Vui lòng điền đầy đủ các thông tin bắt buộc.";
            } elseif ($typeId == 1 && $discountValue > 100) { // Chặn % > 100
                $hasError     = true;
                $errorMessage = "Mức giảm phần trăm không được vượt quá 100%.";
            } elseif (strtotime($expiryDate) <= strtotime($startsAt)) {
                $hasError     = true;
                $errorMessage = "Ngày kết thúc phải sau ngày bắt đầu.";
            } else {
                $isCreated = $this->voucherModel->createVoucher($code, $typeId, $discountValue, $maxDiscount, $minOrderValue, $totalQty, $startsAt, $expiryDate);
                if ($isCreated) {
                    // Chuyển hướng kèm biến msg báo thành công
                    header("Location: index.php?controller=voucher&action=index&msg=create_success");
                    exit;
                } else {
                    $hasError     = true;
                    $errorMessage = "Mã voucher đã tồn tại hoặc xảy ra lỗi hệ thống.";
                }
            }
        }

        $vouchers = $this->voucherModel->getAllVouchers();
        require_once __DIR__ . '/../view/admin/manage_voucher.php';
    }

    public function deleteVoucher() {
        $voucherId = intval($_GET['id'] ?? 0);
        if ($voucherId > 0) {
            $isDeleted = $this->voucherModel->deleteVoucher($voucherId);
            if ($isDeleted) {
                header("Location: index.php?controller=voucher&action=index&msg=delete_success");
            } else {
                // Xóa thất bại (do đã được sử dụng)
                header("Location: index.php?controller=voucher&action=index&msg=delete_fail_used");
            }
            exit;
        }
        header("Location: index.php?controller=voucher&action=index");
        exit;
    }
}
?>