<?php
require_once 'model/ProductModel.php';

class ProductController {
    public function detail() {
        // Kiểm tra xem có truyền id lên không
        if (!isset($_GET['id']) || empty($_GET['id'])) {
            // Redirect về trang chủ hoặc báo lỗi 404
            header('Location: index.php');
            exit;
        }

        $product_id = intval($_GET['id']);
        $productModel = new ProductModel();

        // Tăng lượt xem
        $productModel->incrementViews($product_id);

        // Fetch data
        $product = $productModel->getProductDetail($product_id);
        
        if (!$product) {
            echo "Sản phẩm không tồn tại hoặc đã bị ẩn.";
            exit;
        }

        $images = $productModel->getProductImages($product_id);
        $specs = $productModel->getProductSpecs($product_id);
        $relatedProducts = $productModel->getRelatedProducts($product['category_id'], $product_id);

        // Định dạng thời gian đăng tin (VD: "2 giờ trước", "Hôm qua")
        // Có thể dùng một hàm helper, ở đây tạm pass nguyên data

        // Gọi View và truyền data sang
        require_once 'view/product_detail.php';
    }
}
?>