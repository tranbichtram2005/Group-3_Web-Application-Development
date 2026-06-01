# XÂY DỰNG WEBSITE QUẢN LÝ SÀN THƯƠNG MẠI ĐIỆN TỬ C2C (2Life)
> ĐỒ ÁN CUỐI KỲ HỌC PHẦN PHÁT TRIỂN ỨNG DỤNG WEB – GVHD. TS ĐẶNG NGỌC HOÀNG THÀNH

---

## 👥 Thành viên nhóm 3

| Họ và tên | Vai trò | GitHub Profile | Email |
| :--- | :--- | :--- | :--- |
| **Trần Bích Trâm** | Leader | [tranbichtram2005](https://github.com/tranbichtram2005) | tranbichtram.work@gmail.com |
| **Huỳnh Nguyễn Nhật Nam** | Thành viên | [namhuynhgithub1403](https://github.com/namhuynhgithub1403) | namhuynh703@gmail.com |
| **Lê Thành Vy** | Thành viên | [Vyle31231022150](https://github.com/Vyle31231022150) | vyv877561@gmail.com |

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
- Thanh toán (COD / VNPay)

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
| Cơ sở dữ liệu | MySQL trên **Aiven Cloud** (quản lý qua HeidiSQL) |
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
└── .gitignore
```

---

## Cài đặt và Chạy dự án

### Yêu cầu hệ thống
- **XAMPP**, **WAMP** hoặc bất kỳ local server nào hỗ trợ **PHP 7.4+**
- Trình duyệt web (Chrome, Edge, Firefox...)

---

### Bước 1 – Cài đặt môi trường máy chủ ảo (XAMPP)

1. Truy cập trang chủ [apachefriends.org](https://www.apachefriends.org) và tải bản cài đặt phù hợp với hệ điều hành của bạn.
2. Chạy file cài đặt và làm theo các bước hướng dẫn mặc định.

---

### Bước 2 – Tải và thiết lập mã nguồn dự án

**Cách 1 – Tải ZIP (khuyến nghị cho người dùng thông thường):**

1. Truy cập repo: [https://github.com/tranbichtram2005/Group-3_Web-Application-Development](https://github.com/tranbichtram2005/Group-3_Web-Application-Development)
2. Nhấn nút **Code** → chọn **Download ZIP**.
3. Giải nén file vào đường dẫn:
   ```
   C:\xampp\htdocs\Group-3_Web-Application-Development
   ```

**Cách 2 – Clone bằng Git:**

```bash
git clone https://github.com/tranbichtram2005/Group-3_Web-Application-Development.git
```

Sau đó chuyển thư mục vừa clone vào `C:\xampp\htdocs\`.

---

### Bước 3 – Cấu hình cơ sở dữ liệu (.env)

1. Mở thư mục `model/` bên trong dự án.
2. Tạo một file mới tên là `.env` (không có phần mở rộng).
3. Dán nội dung sau vào file và lưu lại:

```env
DB_PASS="AVNS_R0EBY7inWWQN3_SM53Y"
```

> **Lưu ý:** File `.env` đã được thêm vào `.gitignore` và không được commit lên Git. Bạn phải tạo thủ công mỗi khi clone về máy mới.

---

### Bước 4 – Xác nhận chứng chỉ SSL

File `ca.pem` (chứng chỉ SSL kết nối Aiven Cloud) đã được tích hợp sẵn trong thư mục `model/`. Không cần thao tác thêm.

---

### Bước 5 – Khởi chạy dự án

1. Mở **XAMPP Control Panel**.
2. Nhấn **Start** tại dòng **Apache**.
3. Mở trình duyệt và truy cập:

```
http://localhost/Group-3_Web-Application-Development/
```

---

### Bước 6 – Cấu hình thanh toán VNPay (tuỳ chọn)

Hệ thống tích hợp cổng thanh toán **VNPay** (môi trường Sandbox để kiểm thử).

| Thông số | Giá trị |
| :--- | :--- |
| URL thanh toán (Sandbox) | `https://sandbox.vnpayment.vn/paymentv2/vpcpay.html` |
| Tài liệu tích hợp | https://sandbox.vnpayment.vn/apis/docs/thanh-toan-pay/pay.html |
| Merchant Admin | https://sandbox.vnpayment.vn/merchantv2/ |
| Tên đăng nhập | `tramtran.31231024970@st.ueh.edu.vn` |
| Mật khẩu | `Web@123456` |

> Thông tin `vnp_TmnCode` và `vnp_HashSecret` được lưu trong file `.env`. Liên hệ trưởng nhóm để lấy thông tin.

**Thẻ test (Sandbox NCB):**

| Thông tin | Giá trị |
| :--- | :--- |
| Ngân hàng | NCB |
| Số thẻ | `9704198526191432198` |
| Tên chủ thẻ | `NGUYEN VAN A` |
| Ngày phát hành | `07/15` |
| Mật khẩu OTP | `123456` |

---

## Kết nối Cơ sở dữ liệu đám mây (Dành cho Quản trị viên)

Nếu muốn truy cập và quản lý cơ sở dữ liệu trực tiếp bằng **HeidiSQL**, thực hiện theo cấu hình sau:

**Thông tin kết nối:**

| Thông số | Giá trị |
| :--- | :--- |
| Tên máy chủ / IP | `c2c-web-c2c-web.i.aivencloud.com` |
| Người dùng | `avnadmin` |
| Mật khẩu | `AVNS_R0EBY7inWWQN3_SM53Y` |
| Cổng (Port) | `19707` |

**Cấu hình SSL:**

1. Mở HeidiSQL → Tạo phiên kết nối mới → Điền thông tin ở trên.
2. Chuyển sang tab **SSL** → Tích chọn **Sử dụng SSL**.
3. Tại mục **Chứng chỉ xác thực SSL**, nhấn biểu tượng thư mục và trỏ tới file `ca.pem` trong thư mục `model/`.
4. Nhấn **Mở (Open)** để hoàn tất kết nối.

---

## ⚠️ Lưu ý quan trọng

- Thông tin VNPay là môi trường **Sandbox** — chỉ dùng để kiểm thử, không dùng cho giao dịch thật.
- **Lưu ý về máy chủ:** Do dự án sử dụng cơ sở dữ liệu đám mây trên Aiven.io, máy chủ sẽ tự động chuyển sang chế độ ngủ nếu lâu không hoạt động. Nếu web báo lỗi **"Không thể kết nối cơ sở dữ liệu"**, vui lòng liên hệ nhóm phát triển để mở lại server:
  - Huỳnh Nguyễn Nhật Nam: namhuynh703@gmail.com
  - Lê Thành Vy: vyv877561@gmail.com
  - Trần Bích Trâm: tranbichtram.work@gmail.com
