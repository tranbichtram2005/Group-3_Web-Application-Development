# Group 3 – Web Application Development
> ĐỒ ÁN CUỐI KỲ HỌC PHẦN PHÁT TRIỂN ỨNG DỤNG WEB – GVHD. TS ĐẶNG NGỌC HOÀNG THÀNH  
> XÂY DỰNG WEBSITE QUẢN LÝ SÀN THƯƠNG MẠI ĐIỆN TỬ C2C (2Life)

---

## 👥 Thành viên nhóm 3

| Họ và tên | Vai trò | GitHub Profile |
| :--- | :--- | :--- |
| **Trần Bích Trâm** | Leader | [tranbichtram2005](https://github.com/tranbichtram2005) |
| **Huỳnh Nguyễn Nhật Nam** | Thành viên | [namhuynhgithub1403](https://github.com/namhuynhgithub1403) |
| **Lê Thành Vy** | Thành viên | [Vyle31231022150](https://github.com/Vyle31231022150) |

---

## Giới thiệu dự án

**2Life** là nền tảng web marketplace mô hình C2C (Consumer-to-Consumer), cho phép người dùng đăng bán và mua lại các mặt hàng đã qua sử dụng. Hệ thống được xây dựng bằng PHP thuần theo kiến trúc MVC, sử dụng cơ sở dữ liệu MySQL lưu trữ trên nền tảng đám mây **Aiven Cloud**, tích hợp thanh toán trực tuyến qua **VNPay**.

---

## Tính năng chính

### Giao diện chính
- Trang chủ dành cho người dùng (User)
- Trang chủ dành cho quản trị viên (Admin)

### Xác thực
- Đăng ký tài khoản (Email)
- Đăng nhập / Đăng xuất

### Quản lý tài khoản cá nhân
- Cập nhật thông tin cá nhân
- Đổi mật khẩu
- Đăng ký bán hàng

### Quản lý giỏ hàng
- Cập nhật số lượng sản phẩm
- Xóa sản phẩm khỏi giỏ

### Quản lý mua hàng
- Tra cứu sản phẩm
- Xem chi tiết sản phẩm
- Thêm sản phẩm vào giỏ hàng
- Đặt hàng
- Thanh toán (COD/VNPay)

### Quản lý đơn hàng mua
- Cập nhật thông tin đơn hàng
- Hủy đơn hàng
- Đánh giá sản phẩm

### Quản lý tin đăng (Seller)
- Xem danh sách tin đăng
- Tạo tin đăng sản phẩm
- Cập nhật tin đăng
- Phê duyệt tin đăng (Admin)

### Nhắn tin
- Hiển thị chi tiết hộp thoại
- Gửi tin nhắn văn bản
- Gửi hình ảnh
- Thương lượng giá

### Quản lý đơn hàng bán (Seller)
- Tra cứu đơn bán
- Xem chi tiết đơn bán
- Xác nhận đơn bán
- In phiếu giao hàng
- Hủy đơn hàng

### Báo cáo thống kê (Seller)
- Lọc báo cáo
- In báo cáo
- Xuất dữ liệu

### Quản trị hệ thống (Admin)
- Duyệt người bán
- Thêm / Xóa voucher
- Phê duyệt tin đăng

---

## Phân quyền hệ thống

| Role | Mô tả |
| :--- | :--- |
| Member (`role_id = 1`) | Thành viên (Buyer và Seller) |
| Admin (`role_id = 2`) | Quản trị viên – toàn quyền hệ thống |
| Seller | Member có hồ sơ trong `seller_profiles` – được phép đăng tin bán hàng |

---

## Công nghệ sử dụng

| Thành phần | Công nghệ |
| :--- | :--- |
| Ngôn ngữ backend | PHP |
| Kiến trúc | MVC (Model – View – Controller) |
| Cơ sở dữ liệu | HeidiSQL trên **Aiven Cloud** |
| Frontend | HTML, CSS, JavaScript |
| Gửi email | PHPMailer |
| Thanh toán | VNPay (Sandbox) |
| Web server | Apache (XAMPP / WAMP) |

---

## 📁 Cấu trúc thư mục

```
Group-3_Web-Application-Development/
├── control/        # Controllers – xử lý logic nghiệp vụ
│                   # (CartController, ChatController, AuthController, ...)
├── model/          # Models – tương tác cơ sở dữ liệu
│                   # (Database.php, ca.pem, .env, ...)
├── view/           # Views – giao diện người dùng và Admin (HTML/PHP)
├── layout/         # Header, footer, CSS, JS dùng chung
├── uploads/        # Ảnh sản phẩm và ảnh đại diện do người dùng tải lên
├── lib/
│   └── PHPMailer/  # Thư viện gửi email OTP
├── index.php       # Front controller (router chính)
├── test_db.php     # Kiểm tra kết nối database (chỉ dùng khi dev)
└── .gitignore
```

---

## Cài đặt và Chạy dự án

### Yêu cầu hệ thống
- **XAMPP**, **WAMP** hoặc bất kỳ local server nào hỗ trợ **PHP 7.4+**
- Trình duyệt web (Chrome, Edge, Firefox...)

### Bước 1 – Tải source code

Clone repository về máy hoặc tải file ZIP và giải nén vào thư mục `htdocs` (XAMPP) hoặc `www` (WAMP):

```bash
git clone https://github.com/tranbichtram2005/Group-3_Web-Application-Development.git
```

### Bước 2 – Cấu hình cơ sở dữ liệu

Loading...

### Bước 3 – Xác nhận chứng chỉ SSL

Loading...

### Bước 4 – Cấu hình VNPay

Hệ thống tích hợp cổng thanh toán **VNPay** (môi trường Sandbox để kiểm thử).

| Thông số | Giá trị |
| :--- | :--- |
| URL thanh toán (Sandbox) | `https://sandbox.vnpayment.vn/paymentv2/vpcpay.html` |
| Tài liệu tích hợp | https://sandbox.vnpayment.vn/apis/docs/thanh-toan-pay/pay.html |

Thông tin `vnp_TmnCode` và `vnp_HashSecret` được lưu trong file `.env` (không public). Liên hệ trưởng nhóm để lấy thông tin kết nối.

**Thẻ test (Sandbox):**

| Thông tin | Giá trị |
| :--- | :--- |
| Ngân hàng | NCB |
| Số thẻ | `9704198526191432198` |
| Tên chủ thẻ | `NGUYEN VAN A` |
| Ngày phát hành | `07/15` |
| Mật khẩu OTP | `123456` |

### Bước 5 – Khởi chạy dự án

1. Mở **XAMPP Control Panel**, khởi động module **Apache**.
2. Mở trình duyệt và truy cập:

```
http://localhost/Group-3_Web-Application-Development/
```

---

## Ghi chú

- File `.env` và `ca.pem` chứa thông tin nhạy cảm — **tuyệt đối không commit** lên repository.
- Thư mục `uploads/` đã được thêm vào `.gitignore`, không commit lên Git.
- Thông tin VNPay trên là môi trường **Sandbox** — chỉ dùng để kiểm thử, không dùng để giao dịch thật.
- File `test_db.php` chỉ dùng trong quá trình phát triển, không dùng trên môi trường production.
