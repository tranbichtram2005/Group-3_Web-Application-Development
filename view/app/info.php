<?php
require_once __DIR__ . '/../partials/user-header.php';

// Xác định tab đang active
$tab = $_GET['tab'] ?? 'rules';
$validTabs = ['rules', 'privacy', 'contact'];
if (!in_array($tab, $validTabs)) $tab = 'rules';

$tabs = [
    'rules'   => ['icon' => 'bi-journal-text',   'label' => 'Quy chế hoạt động'],
    'privacy' => ['icon' => 'bi-shield-lock',     'label' => 'Chính sách bảo mật'],
    'contact' => ['icon' => 'bi-headset',         'label' => 'Liên hệ hỗ trợ'],
];
?>

<!-- HERO nhỏ -->
<section class="info-hero">
    <div class="container text-center">
        <i class="bi <?= $tabs[$tab]['icon'] ?> info-hero-icon"></i>
        <h1 class="info-hero-title"><?= $tabs[$tab]['label'] ?></h1>
        <p class="info-hero-sub">2Life Marketplace · Chợ đồ cũ sinh viên uy tín</p>
    </div>
</section>

<main class="container py-4 py-md-5">
    <div class="row g-4">

        <!-- SIDEBAR TABS -->
        <div class="col-12 col-md-3">
            <div class="info-sidebar">
                <?php foreach ($tabs as $key => $t): ?>
                <a href="index.php?controller=info&action=index&tab=<?= $key ?>"
                   class="info-tab-link <?= $tab === $key ? 'active' : '' ?>">
                    <i class="bi <?= $t['icon'] ?>"></i>
                    <span><?= $t['label'] ?></span>
                    <i class="bi bi-chevron-right info-tab-arrow"></i>
                </a>
                <?php endforeach; ?>

                <!-- Widget liên hệ nhanh -->
                <div class="info-quick-contact mt-3">
                    <div class="info-quick-title">Hỗ trợ nhanh</div>
                    <a href="mailto:support@2life.vn" class="info-quick-item">
                        <i class="bi bi-envelope-fill"></i> support@2life.vn
                    </a>
                    <a href="tel:19001234" class="info-quick-item">
                        <i class="bi bi-telephone-fill"></i> 1900 1234
                    </a>
                    <div class="info-quick-item text-muted" style="cursor:default;">
                        <i class="bi bi-clock-fill"></i> 8:00 – 22:00 hằng ngày
                    </div>
                </div>
            </div>
        </div>

        <!-- NỘI DUNG CHÍNH -->
        <div class="col-12 col-md-9">
            <div class="info-card">

                <?php if ($tab === 'rules'): ?>
                <!-- ======= QUY CHẾ HOẠT ĐỘNG ======= -->
                <div class="info-content">
                    <div class="info-badge"><i class="bi bi-journal-text"></i> Cập nhật: 01/01/2026</div>
                    <h2 class="info-section-heading">Quy chế hoạt động 2Life Marketplace</h2>
                    <p class="info-lead">2Life là nền tảng kết nối người mua và người bán đồ cũ trong cộng đồng sinh viên. Mọi thành viên tham gia đều phải tuân thủ các quy định dưới đây.</p>

                    <div class="info-rule-block">
                        <div class="info-rule-num">01</div>
                        <div>
                            <h5>Điều kiện tham gia</h5>
                            <ul>
                                <li>Thành viên phải từ 16 tuổi trở lên và cung cấp thông tin cá nhân chính xác khi đăng ký.</li>
                                <li>Mỗi cá nhân chỉ được sở hữu một tài khoản. Tài khoản trùng lặp sẽ bị khoá không báo trước.</li>
                                <li>Tài khoản dùng để đăng tin phải là thông tin thật, không mạo danh tổ chức hoặc cá nhân khác.</li>
                            </ul>
                        </div>
                    </div>

                    <div class="info-rule-block">
                        <div class="info-rule-num">02</div>
                        <div>
                            <h5>Quy định đăng tin bán hàng</h5>
                            <ul>
                                <li>Tiêu đề và mô tả sản phẩm phải rõ ràng, trung thực, không gây hiểu lầm.</li>
                                <li>Hình ảnh phải là hình thật của sản phẩm, không dùng ảnh lấy từ internet hoặc ảnh của sản phẩm khác.</li>
                                <li>Giá niêm yết phải là giá thực tế người bán muốn bán, không được cố tình khai thấp để thu hút rồi tăng giá sau.</li>
                                <li>Nghiêm cấm đăng tin trùng lặp hoặc spam để đẩy tin lên đầu trang.</li>
                            </ul>
                        </div>
                    </div>

                    <div class="info-rule-block">
                        <div class="info-rule-num">03</div>
                        <div>
                            <h5>Hàng hóa bị cấm</h5>
                            <ul>
                                <li>Vũ khí, chất nổ, ma túy và các chất kích thích bị pháp luật cấm.</li>
                                <li>Hàng giả, hàng nhái, hàng vi phạm bản quyền.</li>
                                <li>Động vật hoang dã và các sản phẩm từ động vật thuộc danh sách bảo tồn.</li>
                                <li>Tài khoản game, phần mềm crack, nội dung số vi phạm bản quyền.</li>
                                <li>Mọi hàng hóa bị pháp luật Việt Nam cấm lưu hành.</li>
                            </ul>
                        </div>
                    </div>

                    <div class="info-rule-block">
                        <div class="info-rule-num">04</div>
                        <div>
                            <h5>Trách nhiệm giao dịch</h5>
                            <ul>
                                <li>2Life là nền tảng kết nối — mọi giao dịch xảy ra giữa người mua và người bán, 2Life không là bên thứ ba trong hợp đồng mua bán.</li>
                                <li>Người bán chịu trách nhiệm về chất lượng sản phẩm đúng với mô tả đã đăng.</li>
                                <li>Người mua có trách nhiệm kiểm tra hàng trước khi xác nhận hoàn thành đơn hàng.</li>
                                <li>Tranh chấp phát sinh sẽ được đội ngũ hỗ trợ 2Life xem xét và hoà giải theo quy trình nội bộ.</li>
                            </ul>
                        </div>
                    </div>

                    <div class="info-rule-block">
                        <div class="info-rule-num">05</div>
                        <div>
                            <h5>Xử lý vi phạm</h5>
                            <ul>
                                <li><strong>Cảnh cáo:</strong> áp dụng lần vi phạm đầu đối với lỗi nhỏ như thông tin không chính xác.</li>
                                <li><strong>Gỡ tin / hạn chế tính năng:</strong> áp dụng khi phát hiện tin đăng vi phạm hoặc hành vi gian lận.</li>
                                <li><strong>Khoá tài khoản vĩnh viễn:</strong> áp dụng với trường hợp lừa đảo, vi phạm nghiêm trọng hoặc tái phạm nhiều lần.</li>
                            </ul>
                        </div>
                    </div>

                    <div class="info-note-box">
                        <i class="bi bi-info-circle-fill me-2"></i>
                        2Life có quyền cập nhật quy chế bất kỳ lúc nào. Thay đổi sẽ được thông báo qua email và thông báo trên nền tảng trước ít nhất 7 ngày.
                    </div>
                </div>

                <?php elseif ($tab === 'privacy'): ?>
                <!-- ======= CHÍNH SÁCH BẢO MẬT ======= -->
                <div class="info-content">
                    <div class="info-badge"><i class="bi bi-shield-lock"></i> Cập nhật: 01/01/2026</div>
                    <h2 class="info-section-heading">Chính sách bảo mật</h2>
                    <p class="info-lead">2Life cam kết bảo vệ thông tin cá nhân của bạn. Tài liệu này mô tả cách chúng tôi thu thập, sử dụng và bảo vệ dữ liệu của bạn.</p>

                    <div class="info-privacy-block">
                        <div class="info-privacy-icon"><i class="bi bi-database-fill"></i></div>
                        <div>
                            <h5>1. Thông tin chúng tôi thu thập</h5>
                            <p>Khi bạn đăng ký và sử dụng 2Life, chúng tôi thu thập các thông tin sau:</p>
                            <ul>
                                <li><strong>Thông tin tài khoản:</strong> họ tên, email, số điện thoại, địa chỉ.</li>
                                <li><strong>Thông tin giao dịch:</strong> lịch sử mua bán, đơn hàng, thanh toán.</li>
                                <li><strong>Thông tin thiết bị:</strong> địa chỉ IP, trình duyệt, hệ điều hành (phục vụ bảo mật).</li>
                                <li><strong>Nội dung người dùng tạo:</strong> tin đăng, tin nhắn, đánh giá.</li>
                            </ul>
                        </div>
                    </div>

                    <div class="info-privacy-block">
                        <div class="info-privacy-icon"><i class="bi bi-gear-fill"></i></div>
                        <div>
                            <h5>2. Mục đích sử dụng thông tin</h5>
                            <ul>
                                <li>Xác minh danh tính và quản lý tài khoản người dùng.</li>
                                <li>Xử lý đơn hàng và hỗ trợ giải quyết tranh chấp.</li>
                                <li>Cải thiện trải nghiệm người dùng trên nền tảng.</li>
                                <li>Gửi thông báo về giao dịch, cập nhật dịch vụ (có thể tắt trong cài đặt).</li>
                                <li>Phát hiện và ngăn chặn các hành vi gian lận, vi phạm.</li>
                            </ul>
                        </div>
                    </div>

                    <div class="info-privacy-block">
                        <div class="info-privacy-icon"><i class="bi bi-share-fill"></i></div>
                        <div>
                            <h5>3. Chia sẻ thông tin với bên thứ ba</h5>
                            <p>2Life <strong>không bán</strong> thông tin cá nhân của bạn. Thông tin chỉ được chia sẻ trong các trường hợp:</p>
                            <ul>
                                <li>Đối tác thanh toán (MoMo, VNPay) để xử lý giao dịch — chỉ thông tin cần thiết.</li>
                                <li>Cơ quan pháp luật khi có yêu cầu hợp lệ theo quy định pháp luật Việt Nam.</li>
                                <li>Đơn vị vận chuyển để giao hàng (nếu áp dụng trong tương lai).</li>
                            </ul>
                        </div>
                    </div>

                    <div class="info-privacy-block">
                        <div class="info-privacy-icon"><i class="bi bi-lock-fill"></i></div>
                        <div>
                            <h5>4. Bảo mật dữ liệu</h5>
                            <ul>
                                <li>Mật khẩu được mã hóa bằng bcrypt, không ai có thể xem được kể cả quản trị viên.</li>
                                <li>Kết nối tới 2Life sử dụng HTTPS (mã hóa SSL/TLS).</li>
                                <li>Quyền truy cập vào dữ liệu nhạy cảm được kiểm soát chặt chẽ theo vai trò.</li>
                            </ul>
                        </div>
                    </div>

                    <div class="info-privacy-block">
                        <div class="info-privacy-icon"><i class="bi bi-person-check-fill"></i></div>
                        <div>
                            <h5>5. Quyền của bạn</h5>
                            <ul>
                                <li><strong>Truy cập:</strong> yêu cầu xem toàn bộ dữ liệu cá nhân chúng tôi lưu trữ.</li>
                                <li><strong>Chỉnh sửa:</strong> cập nhật thông tin cá nhân qua trang "Tài khoản của tôi".</li>
                                <li><strong>Xóa:</strong> yêu cầu xóa tài khoản và dữ liệu — xử lý trong vòng 30 ngày.</li>
                                <li><strong>Phản đối:</strong> từ chối nhận email marketing bất kỳ lúc nào.</li>
                            </ul>
                        </div>
                    </div>

                    <div class="info-note-box">
                        <i class="bi bi-envelope me-2"></i>
                        Mọi yêu cầu về quyền riêng tư, vui lòng gửi email đến <strong>privacy@2life.vn</strong>. Chúng tôi phản hồi trong vòng 3 ngày làm việc.
                    </div>
                </div>

                <?php elseif ($tab === 'contact'): ?>
                <!-- ======= LIÊN HỆ HỖ TRỢ ======= -->
                <div class="info-content">
                    <div class="info-badge"><i class="bi bi-headset"></i> Hỗ trợ 7/7</div>
                    <h2 class="info-section-heading">Liên hệ & Hỗ trợ</h2>
                    <p class="info-lead">Đội ngũ hỗ trợ 2Life luôn sẵn sàng giúp bạn. Chọn kênh phù hợp bên dưới hoặc gửi câu hỏi trực tiếp cho chúng tôi.</p>

                    <!-- Thẻ kênh liên hệ -->
                    <div class="row g-3 mb-4">
                        <div class="col-12 col-sm-6">
                            <div class="info-contact-card">
                                <div class="info-contact-icon" style="background: #FFF0E8; color: #FF7A3D;">
                                    <i class="bi bi-headset"></i>
                                </div>
                                <div class="info-contact-label">Chat hỗ trợ trực tiếp</div>
                                <div class="info-contact-value">Bấm nút cam góc phải màn hình</div>
                                <div class="info-contact-hours">8:00 – 22:00 hằng ngày</div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="info-contact-card">
                                <div class="info-contact-icon" style="background: #E8F4FD; color: #4DA8DA;">
                                    <i class="bi bi-envelope-fill"></i>
                                </div>
                                <div class="info-contact-label">Email hỗ trợ</div>
                                <div class="info-contact-value">
                                    <a href="mailto:support@2life.vn">support@2life.vn</a>
                                </div>
                                <div class="info-contact-hours">Phản hồi trong 24 giờ</div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="info-contact-card">
                                <div class="info-contact-icon" style="background: #E8F8F0; color: #28a745;">
                                    <i class="bi bi-telephone-fill"></i>
                                </div>
                                <div class="info-contact-label">Đường dây hỗ trợ</div>
                                <div class="info-contact-value">
                                    <a href="tel:19001234">1900 1234</a>
                                </div>
                                <div class="info-contact-hours">8:00 – 18:00 (Thứ 2 – Thứ 6)</div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="info-contact-card">
                                <div class="info-contact-icon" style="background: #F0E8FF; color: #7c4dff;">
                                    <i class="bi bi-facebook"></i>
                                </div>
                                <div class="info-contact-label">Facebook</div>
                                <div class="info-contact-value">
                                    <a href="#" target="_blank">fb.com/2lifemarketplace</a>
                                </div>
                                <div class="info-contact-hours">Phản hồi trong vài giờ</div>
                            </div>
                        </div>
                    </div>

                    <!-- FAQ nhanh -->
                    <h5 class="fw-bold mb-3" style="color: var(--nav-color);">Câu hỏi thường gặp</h5>
                    <div class="accordion info-faq" id="faqAccordion">

                        <?php
                        $faqs = [
                            ['q' => 'Làm sao để đăng tin bán hàng?', 'a' => 'Bạn cần đăng nhập, sau đó bấm nút "Đăng tin" trên thanh điều hướng. Điền đầy đủ thông tin sản phẩm, tải ảnh lên và bấm Đăng tin là xong!'],
                            ['q' => 'Tôi bị lừa, phải làm gì?', 'a' => 'Vào trang tin đăng hoặc hồ sơ người bán đó, bấm nút "Tố cáo". Cung cấp bằng chứng giao dịch. Đội ngũ 2Life sẽ xử lý trong vòng 24–48 giờ.'],
                            ['q' => 'Thanh toán có an toàn không?', 'a' => '2Life hợp tác với MoMo và VNPay là các cổng thanh toán uy tín. Thông tin thẻ/ví của bạn không được lưu trên máy chủ 2Life.'],
                            ['q' => 'Tôi có thể xóa tài khoản không?', 'a' => 'Có. Vào phần "Tài khoản của tôi" → Cài đặt → Xóa tài khoản. Hoặc gửi yêu cầu qua email privacy@2life.vn, chúng tôi sẽ xử lý trong 30 ngày.'],
                            ['q' => 'Đơn hàng bị giao trễ thì sao?', 'a' => 'Liên hệ chat hỗ trợ và cung cấp mã đơn hàng. Nếu quá 7 ngày không nhận được hàng, bạn có thể yêu cầu hủy đơn và hoàn tiền.'],
                        ];
                        foreach ($faqs as $i => $faq):
                        ?>
                        <div class="accordion-item info-faq-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button <?= $i > 0 ? 'collapsed' : '' ?> info-faq-btn"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#faq<?= $i ?>">
                                    <i class="bi bi-question-circle me-2" style="color: var(--btn-primary);"></i>
                                    <?= $faq['q'] ?>
                                </button>
                            </h2>
                            <div id="faq<?= $i ?>" class="accordion-collapse collapse <?= $i === 0 ? 'show' : '' ?>" data-bs-parent="#faqAccordion">
                                <div class="accordion-body info-faq-body">
                                    <?= $faq['a'] ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="info-note-box mt-4">
                        <i class="bi bi-clock me-2"></i>
                        Đội ngũ hỗ trợ hoạt động từ <strong>8:00 – 22:00</strong> mỗi ngày, kể cả cuối tuần và ngày lễ.
                    </div>
                </div>
                <?php endif; ?>

            </div><!-- /info-card -->
        </div><!-- /col-9 -->
    </div><!-- /row -->
</main>

<style>
/* ---- HERO ---- */
.info-hero {
    background: linear-gradient(135deg, var(--nav-color) 0%, #2a5080 100%);
    padding: 48px 0 40px;
    text-align: center;
    position: relative;
    overflow: hidden;
}
.info-hero::after {
    content: '';
    position: absolute;
    bottom: -1px; left: 0; right: 0;
    height: 30px;
    background: var(--bg-main);
    clip-path: ellipse(55% 100% at 50% 100%);
}
.info-hero-icon {
    font-size: 2.4rem;
    color: var(--btn-primary);
    display: block;
    margin-bottom: 10px;
}
.info-hero-title {
    font-size: clamp(1.4rem, 4vw, 2rem);
    font-weight: 800;
    color: #fff;
    margin-bottom: 6px;
    letter-spacing: -0.5px;
}
.info-hero-sub {
    color: rgba(255,255,255,0.55);
    font-size: 13.5px;
    margin: 0;
}

/* ---- SIDEBAR ---- */
.info-sidebar {
    position: sticky;
    top: 76px;
}
.info-tab-link {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 11px 14px;
    border-radius: 10px;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    color: var(--text-primary);
    transition: background 0.18s, color 0.18s;
    margin-bottom: 4px;
    border: 1.5px solid transparent;
}
.info-tab-link i:first-child { font-size: 16px; color: var(--text-secondary); transition: color 0.18s; }
.info-tab-link span { flex: 1; }
.info-tab-arrow { font-size: 11px; opacity: 0; transition: opacity 0.18s; color: var(--btn-primary); }

.info-tab-link:hover {
    background: #fff;
    color: var(--btn-primary);
    border-color: var(--border-color);
}
.info-tab-link:hover i:first-child { color: var(--btn-primary); }
.info-tab-link:hover .info-tab-arrow { opacity: 1; }

.info-tab-link.active {
    background: #fff;
    color: var(--btn-primary);
    border-color: var(--btn-primary);
    font-weight: 700;
}
.info-tab-link.active i:first-child { color: var(--btn-primary); }
.info-tab-link.active .info-tab-arrow { opacity: 1; }

/* Quick contact widget */
.info-quick-contact {
    background: #fff;
    border: 1px solid var(--border-color);
    border-radius: 12px;
    padding: 14px;
}
.info-quick-title {
    font-size: 11px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--text-secondary);
    margin-bottom: 10px;
}
.info-quick-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    color: var(--text-primary);
    text-decoration: none;
    padding: 5px 0;
    transition: color 0.15s;
}
.info-quick-item i { color: var(--btn-primary); font-size: 14px; width: 16px; }
.info-quick-item:hover { color: var(--btn-primary); }

/* ---- CARD NỘI DUNG ---- */
.info-card {
    background: #fff;
    border: 1px solid var(--border-color);
    border-radius: 16px;
    padding: 32px;
    min-height: 400px;
}
@media (max-width: 576px) {
    .info-card { padding: 20px 16px; }
}

.info-content {}
.info-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    font-weight: 600;
    color: var(--btn-secondary);
    background: #EBF6FD;
    padding: 4px 12px;
    border-radius: 20px;
    margin-bottom: 14px;
}
.info-section-heading {
    font-size: clamp(1.2rem, 3vw, 1.6rem);
    font-weight: 800;
    color: var(--nav-color);
    margin-bottom: 10px;
    letter-spacing: -0.3px;
}
.info-lead {
    color: var(--text-secondary);
    font-size: 14.5px;
    line-height: 1.65;
    margin-bottom: 28px;
    border-left: 3px solid var(--btn-primary);
    padding-left: 14px;
}

/* Rules */
.info-rule-block {
    display: flex;
    gap: 18px;
    margin-bottom: 24px;
    padding-bottom: 24px;
    border-bottom: 1px dashed var(--border-color);
}
.info-rule-block:last-of-type { border-bottom: none; }
.info-rule-num {
    font-size: 28px;
    font-weight: 900;
    color: var(--btn-primary);
    opacity: 0.18;
    line-height: 1;
    min-width: 36px;
    flex-shrink: 0;
    margin-top: 2px;
}
.info-rule-block h5 {
    font-size: 15px;
    font-weight: 700;
    color: var(--nav-color);
    margin-bottom: 8px;
}
.info-rule-block ul {
    padding-left: 18px;
    margin: 0;
    color: var(--text-secondary);
    font-size: 14px;
    line-height: 1.7;
}

/* Privacy */
.info-privacy-block {
    display: flex;
    gap: 16px;
    margin-bottom: 22px;
    padding-bottom: 22px;
    border-bottom: 1px dashed var(--border-color);
}
.info-privacy-block:last-of-type { border-bottom: none; }
.info-privacy-icon {
    width: 42px;
    height: 42px;
    background: #FFF0E8;
    color: var(--btn-primary);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
    margin-top: 2px;
}
.info-privacy-block h5 {
    font-size: 15px;
    font-weight: 700;
    color: var(--nav-color);
    margin-bottom: 8px;
}
.info-privacy-block p, .info-privacy-block ul {
    color: var(--text-secondary);
    font-size: 14px;
    line-height: 1.7;
}
.info-privacy-block ul { padding-left: 18px; margin: 6px 0 0; }

/* Note box */
.info-note-box {
    background: #FFF8F5;
    border: 1.5px solid #FFD5BB;
    border-radius: 10px;
    padding: 14px 18px;
    font-size: 13.5px;
    color: #7a4a30;
    line-height: 1.6;
    margin-top: 28px;
}

/* Contact cards */
.info-contact-card {
    background: var(--bg-main);
    border: 1.5px solid var(--border-color);
    border-radius: 14px;
    padding: 18px;
    height: 100%;
    transition: border-color 0.2s, box-shadow 0.2s;
}
.info-contact-card:hover {
    border-color: var(--btn-primary);
    box-shadow: 0 4px 16px rgba(255,122,61,0.1);
}
.info-contact-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    margin-bottom: 12px;
}
.info-contact-label {
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    color: var(--text-secondary);
    margin-bottom: 4px;
}
.info-contact-value {
    font-size: 14.5px;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 4px;
}
.info-contact-value a { color: var(--btn-primary); text-decoration: none; }
.info-contact-value a:hover { text-decoration: underline; }
.info-contact-hours { font-size: 12.5px; color: var(--text-secondary); }

/* FAQ */
.info-faq { }
.info-faq-item {
    border: 1.5px solid var(--border-color) !important;
    border-radius: 10px !important;
    margin-bottom: 8px;
    overflow: hidden;
}
.info-faq-btn {
    font-size: 14px;
    font-weight: 600;
    color: var(--text-primary) !important;
    background: #fff !important;
    box-shadow: none !important;
    padding: 13px 16px;
}
.info-faq-btn:not(.collapsed) {
    color: var(--btn-primary) !important;
    background: #FFF8F5 !important;
}
.info-faq-btn::after { filter: none; }
.info-faq-body {
    font-size: 14px;
    color: var(--text-secondary);
    line-height: 1.7;
    background: #FAFAFA;
    padding: 12px 16px 14px;
}

/* Mobile: sidebar nằm ngang */
@media (max-width: 767px) {
    .info-sidebar {
        position: static;
        display: flex;
        flex-wrap: nowrap;
        gap: 6px;
        overflow-x: auto;
        padding-bottom: 4px;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
    }
    .info-sidebar::-webkit-scrollbar { display: none; }
    .info-quick-contact { display: none; } /* ẩn widget trên mobile */
    .info-tab-link {
        flex-shrink: 0;
        white-space: nowrap;
        padding: 8px 14px;
        margin-bottom: 0;
    }
    .info-tab-arrow { display: none; }
    .info-rule-block, .info-privacy-block { flex-direction: column; gap: 8px; }
    .info-rule-num { font-size: 20px; }
}
</style>

<?php require_once __DIR__ . '/../partials/user-footer.php'; ?>