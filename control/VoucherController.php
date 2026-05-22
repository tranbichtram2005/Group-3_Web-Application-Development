<?php
require_once __DIR__ . '/../model/VoucherModel.php';

class VoucherController {
    private $voucherModel;

    public function __construct() {
        // Kiểm tra quyền Admin (Role = 3) bảo mật cho module
        if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 3) {
            header("Location: index.php?controller=auth&action=login");
            exit;
        }
        $this->voucherModel = new VoucherModel();
    }

    public function manageVouchers() {
        $vouchers = $this->voucherModel->getAllVouchers();
        require_once __DIR__ . '/../view/admin/manage_voucher.php';
    }

    public function createVoucher() {
        $hasError = false;
        $errorMessage = "";

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $code = strtoupper(trim($_POST['code'] ?? ''));
            $discountValue = intval($_POST['discountValue'] ?? 0);
            $minOrderValue = intval($_POST['minOrderValue'] ?? 0);
            $expiryDate = $_POST['expiryDate'] ?? '';

            if (empty($code) || $discountValue <= 0 || empty($expiryDate)) {
                $hasError = true;
                $errorMessage = "Vui lòng điền đầy đủ và chính xác thông tin voucher.";
            } else {
                $isCreated = $this->voucherModel->createVoucher($code, $discountValue, $minOrderValue, $expiryDate);
                if ($isCreated) {
                    header("Location: index.php?controller=voucher&action=manageVouchers");
                    exit;
                } else {
                    $hasError = true;
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
            $this->voucherModel->deleteVoucher($voucherId);
        }
        header("Location: index.php?controller=voucher&action=manageVouchers");
        exit;
    }
}