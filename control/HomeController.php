<?php
class HomeController {
    private $listingModel;

    public function __construct() {
        require_once __DIR__ . '/../model/Database.php';
        require_once __DIR__ . '/../model/ListingModel.php'; 
        $this->listingModel = new ListingModel(); // Khởi tạo model vị cứu tinh
    }

    public function index() {
        // 1. Lấy danh mục
        $categories = $this->listingModel->getAllCategories();

        // 2. Phân trang
        $limit = 8; // 8 sản phẩm 1 trang cho đẹp
        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $limit;

        $totalListings = $this->listingModel->getTotalActiveListings();
        $totalPages = ceil($totalListings / $limit);

        // 3. Lấy sản phẩm
        $listings = $this->listingModel->getPaginatedListings($limit, $offset);
        
        // Kéo dữ liệu Voucher cho trang chủ
        require_once __DIR__ . '/../model/VoucherModel.php';
        $voucherModel = new VoucherModel();
        $activeVouchers = $voucherModel->getActiveVouchers();
        // Nạp View
        require_once __DIR__ . '/../view/app/home.php';
    }
}
?>