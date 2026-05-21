<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['title'] ?? 'Chi tiết sản phẩm') ?> - 2Life</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="layout/style.css">

    <style>
        /* =====================================================
           GIỮ NGUYÊN BIẾN MÀU TỪ style.css GỐC
           ===================================================== */
        :root {
            --bg-main:        #FAF7F2;
            --bg-section:     #D6EEF8;
            --bg-card:        #EFE6DD;
            --nav-color:      #1F3C5A;
            --btn-primary:    #FF7A3D;
            --btn-secondary:  #4DA8DA;
            --btn-hover:      #A7D0E8;
            --text-primary:   #2B2B2B;
            --text-secondary: #6B6B6B;
            --border-color:   #D9D9D9;
            --tag-color:      #7C8C6B;
            --error-color:    #FF5E5B;
        }

        /* =====================================================
           BASE
           ===================================================== */
        * { box-sizing: border-box; }
        body {
            background-color: var(--bg-main);
            color: var(--text-primary);
            font-family: 'Inter', sans-serif;
        }

        /* =====================================================
           NAVBAR 2LIFE (Bootstrap wrapper)
           ===================================================== */
        .navbar-2life {
            background-color: var(--nav-color);
            padding: 12px 0;
        }
        .navbar-2life .logo {
            font-size: 24px;
            font-weight: 700;
            color: var(--btn-primary);
            text-decoration: none;
        }
        .navbar-2life .search-input {
            border-radius: 20px;
            border: 1px solid var(--border-color);
            padding: 8px 18px;
            width: 100%;
            outline: none;
            font-size: 14px;
        }
        .navbar-2life .search-input:focus {
            border-color: var(--btn-primary);
            box-shadow: 0 0 0 3px rgba(255,122,61,.15);
        }
        .navbar-2life .nav-link-text {
            color: #fff;
            text-decoration: none;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 5px;
            white-space: nowrap;
        }
        .navbar-2life .nav-link-text:hover { color: var(--btn-hover); }

        /* === DROPDOWN OVERRIDE (fix vỡ layout) === */
        .nav-dropdown { position: relative; }
        .nav-dropdown-menu {
            display: none;
            position: absolute !important;
            top: calc(100% + 10px);
            right: 0;
            left: auto;
            background: #fff;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0,0,0,.14);
            min-width: 240px;
            width: 240px;
            z-index: 9999;
            overflow: hidden;
            white-space: normal;
            float: none;
        }
        .nav-dropdown:hover > .nav-dropdown-menu { display: block; }
        .nav-dropdown-item {
            display: flex !important;
            align-items: center;
            gap: 10px;
            padding: 11px 16px;
            font-size: 13px;
            color: var(--text-primary) !important;
            text-decoration: none;
            border-bottom: 1px solid var(--border-color);
            transition: background .15s;
            width: 100%;
        }
        .nav-dropdown-item:last-child { border-bottom: none; }
        .nav-dropdown-item:hover { background: var(--bg-main) !important; color: var(--btn-primary) !important; }
        .nav-dropdown-item i { font-size: 15px; color: var(--btn-secondary); flex-shrink: 0; }
        .nav-dropdown-item .item-sub { font-size: 11px; color: var(--text-secondary); display: block; margin-top: 1px; }

        /* =====================================================
           BUTTONS (giữ style gốc)
           ===================================================== */
        .btn-2life-primary {
            background-color: var(--btn-primary);
            color: #fff;
            border: none;
            border-radius: 25px;
            padding: 10px 22px;
            font-weight: 600;
            font-size: 14px;
            transition: .25s;
            cursor: pointer;
        }
        .btn-2life-primary:hover { background-color: var(--error-color); color: #fff; }

        .btn-2life-secondary {
            background-color: var(--btn-secondary);
            color: #fff;
            border: none;
            border-radius: 25px;
            padding: 10px 22px;
            font-weight: 600;
            font-size: 14px;
            transition: .25s;
            cursor: pointer;
        }
        .btn-2life-secondary:hover { background-color: var(--btn-hover); color: var(--text-primary); }

        .btn-2life-outline {
            background-color: transparent;
            color: var(--btn-primary);
            border: 1.5px solid var(--btn-primary);
            border-radius: 25px;
            padding: 9px 22px;
            font-weight: 600;
            font-size: 14px;
            transition: .25s;
            cursor: pointer;
        }
        .btn-2life-outline:hover { background-color: var(--btn-primary); color: #fff; }

        /* =====================================================
           BREADCRUMB
           ===================================================== */
        .breadcrumb-2life {
            font-size: 13px;
            color: var(--text-secondary);
            padding: 16px 0 8px;
        }
        .breadcrumb-2life a { color: var(--text-secondary); text-decoration: none; }
        .breadcrumb-2life a:hover { color: var(--btn-primary); text-decoration: underline; }
        .breadcrumb-2life .sep { margin: 0 6px; }

        /* =====================================================
           BADGE / TAG (giữ style gốc)
           ===================================================== */
        .tag-2life {
            display: inline-block;
            background-color: var(--tag-color);
            color: #fff;
            font-size: 12px;
            padding: 3px 10px;
            border-radius: 4px;
        }
        .tag-blue { background-color: var(--btn-secondary); }
        .tag-orange { background-color: var(--btn-primary); }
        .tag-red { background-color: var(--error-color); }

        /* =====================================================
           CARD CHUNG
           ===================================================== */
        .card-2life {
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
        }
        .card-white {
            background-color: #fff;
            border: 1px solid var(--border-color);
            border-radius: 12px;
        }

        /* =====================================================
           ẢNH CHÍNH
           ===================================================== */
        #mainImg {
            width: 100%;
            height: 420px;
            object-fit: cover;
            border-radius: 12px;
            border: 1px solid var(--border-color);
            transition: .3s;
        }
        .thumb-gallery {
            display: flex;
            gap: 10px;
            margin-top: 12px;
            flex-wrap: wrap;
        }
        .thumb-gallery img {
            width: 72px;
            height: 72px;
            object-fit: cover;
            border-radius: 8px;
            cursor: pointer;
            border: 2px solid transparent;
            opacity: .65;
            transition: .2s;
        }
        .thumb-gallery img:hover,
        .thumb-gallery img.active-thumb {
            opacity: 1;
            border-color: var(--btn-primary);
        }
        .img-wrapper { position: relative; }
        .img-badge {
            position: absolute;
            top: 12px; left: 12px;
            font-size: 11px;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 5px;
        }
        .img-badge-condition { background: var(--tag-color); color: #fff; }
        .img-badge-hot { background: var(--btn-primary); color: #fff; top: 12px; left: auto; right: 12px; }

        /* =====================================================
           GIÁ
           ===================================================== */
        .price-large {
            font-size: 34px;
            font-weight: 700;
            color: var(--btn-primary);
        }
        .price-original {
            font-size: 16px;
            color: var(--text-secondary);
            text-decoration: line-through;
        }
        .price-badge {
            display: inline-block;
            background: #fff3ee;
            color: var(--btn-primary);
            font-size: 12px;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 5px;
            border: 1px solid var(--btn-primary);
        }

        /* =====================================================
           THÔNG SỐ KỸ THUẬT
           ===================================================== */
        .spec-table td {
            padding: 7px 4px;
            font-size: 14px;
            border-bottom: 1px solid var(--border-color);
            vertical-align: top;
        }
        .spec-table tr:last-child td { border-bottom: none; }
        .spec-label { color: var(--text-secondary); width: 44%; }
        .spec-value { font-weight: 600; color: var(--text-primary); }

        /* =====================================================
           SELLER CARD
           ===================================================== */
        .seller-avatar {
            width: 52px; height: 52px;
            border-radius: 50%;
            object-fit: cover;
            flex-shrink: 0;
        }
        .seller-stat-box {
            background-color: var(--bg-main);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 10px 8px;
            text-align: center;
        }
        .seller-stat-box .num { font-size: 18px; font-weight: 700; color: var(--text-primary); }
        .seller-stat-box .lbl { font-size: 11px; color: var(--text-secondary); margin-top: 2px; }

        /* =====================================================
           ACTION BUTTONS (full width trên mobile)
           ===================================================== */
        .action-group { display: flex; gap: 12px; }
        .action-group .btn-2life-secondary,
        .action-group .btn-2life-primary { flex: 1; padding: 14px; font-size: 15px; border-radius: 12px; }

        /* =====================================================
           SAFETY BOX
           ===================================================== */
        .safety-item {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            font-size: 13px;
            color: var(--text-secondary);
            margin-bottom: 8px;
        }
        .safety-item i { color: var(--tag-color); font-size: 14px; margin-top: 1px; flex-shrink: 0; }

        /* =====================================================
           ĐÁNH GIÁ
           ===================================================== */
        .review-item {
            padding: 14px 0;
            border-bottom: 1px solid var(--border-color);
        }
        .review-item:last-child { border-bottom: none; }
        .rev-avatar {
            width: 36px; height: 36px;
            border-radius: 50%;
            background-color: var(--bg-section);
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 13px;
            color: var(--nav-color);
            flex-shrink: 0;
        }
        .stars-orange { color: #F5A623; font-size: 13px; letter-spacing: -.5px; }

        /* =====================================================
           RATING BAR
           ===================================================== */
        .rating-bar-track {
            height: 7px;
            background: var(--border-color);
            border-radius: 4px;
            overflow: hidden;
            flex: 1;
        }
        .rating-bar-fill { height: 100%; background: #F5A623; border-radius: 4px; }

        /* =====================================================
           RELATED PRODUCTS
           ===================================================== */
        .related-card {
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            overflow: hidden;
            transition: transform .25s, box-shadow .25s;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
            display: block;
        }
        .related-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 6px 18px rgba(0,0,0,.09);
            color: inherit;
        }
        .related-card img { width: 100%; height: 130px; object-fit: cover; }
        .related-card .rc-info { padding: 10px 12px; }
        .related-card .rc-title { font-size: 13px; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .related-card .rc-price { font-size: 14px; font-weight: 700; color: var(--btn-primary); margin-top: 3px; }
        .related-card .rc-loc { font-size: 11px; color: var(--text-secondary); }

        /* =====================================================
           MAP PLACEHOLDER
           ===================================================== */
        .map-placeholder {
            height: 130px;
            background: var(--bg-section);
            border: 1px dashed var(--border-color);
            border-radius: 10px;
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            color: var(--text-secondary);
            font-size: 13px;
            gap: 6px;
        }

        /* =====================================================
           Q&A
           ===================================================== */
        .qa-item { padding: 12px 0; border-bottom: 1px solid var(--border-color); }
        .qa-item:last-child { border-bottom: none; }
        .qa-question { font-size: 13px; font-weight: 600; color: var(--text-primary); }
        .qa-answer { font-size: 13px; color: var(--text-secondary); margin-top: 4px; padding-left: 14px; border-left: 3px solid var(--btn-primary); }
        .qa-meta { font-size: 11px; color: var(--text-secondary); margin-top: 4px; }

        /* =====================================================
           SIDEBAR STICKY
           ===================================================== */
        @media (min-width: 992px) {
            .sidebar-sticky { position: sticky; top: 20px; }
        }

        /* =====================================================
           RESPONSIVE FIXES
           ===================================================== */
        @media (max-width: 575px) {
            #mainImg { height: 280px; }
            .price-large { font-size: 26px; }
            .action-group { flex-direction: column; }
        }

        /* =====================================================
           FOOTER
           ===================================================== */
        footer {
            background-color: var(--nav-color);
            color: #ccc;
            font-size: 13px;
            margin-top: 60px;
        }
        footer a { color: #A7D0E8; text-decoration: none; }
        footer a:hover { color: var(--btn-primary); }
        footer .footer-logo { font-size: 22px; font-weight: 700; color: var(--btn-primary); }

        /* =====================================================
           UTILITY
           ===================================================== */
        .divider { border-top: 1px solid var(--border-color); margin: 16px 0; }
        .section-label { font-size: 11px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: var(--text-secondary); margin-bottom: 12px; }
        .like-share-bar { display: flex; gap: 10px; flex-wrap: wrap; }
        .icon-btn {
            display: inline-flex; align-items: center; gap: 5px;
            background: var(--bg-card); border: 1px solid var(--border-color);
            border-radius: 8px; padding: 6px 14px; font-size: 13px;
            cursor: pointer; color: var(--text-secondary); transition: .2s;
        }
        .icon-btn:hover { border-color: var(--btn-primary); color: var(--btn-primary); }
        .icon-btn.active { background: #fff3ee; border-color: var(--btn-primary); color: var(--btn-primary); }
    </style>
</head>
<body>
<header class="navbar-2life">
    <div class="container-fluid px-3 px-md-4">
        <div class="row align-items-center g-2">

            <div class="col-6 col-md-2">
                <a href="index.html" class="logo">2Life</a>
            </div>

            <div class="col-md-5 d-none d-md-block">
                <div class="d-flex align-items-center" style="background:#fff;border-radius:25px;padding:4px 4px 4px 16px;border:1px solid var(--border-color);">
                    <input type="text" placeholder="Tìm kiếm đồ cũ giá hời..." style="flex:1;border:none;outline:none;font-size:14px;background:transparent;color:var(--text-primary);">
                    <button class="btn-2life-primary" style="border-radius:20px;padding:7px 18px;white-space:nowrap;flex-shrink:0">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>

            <div class="col-6 col-md-5 d-flex justify-content-end align-items-center gap-2 gap-md-3">

                <a href="#" class="nav-link-text position-relative" title="Giỏ hàng">
                    <i class="bi bi-cart3" style="font-size:18px"></i>
                    <span class="nav-badge">3</span>
                    <span class="d-none d-lg-inline ms-1">Giỏ hàng</span>
                </a>

                <div class="nav-dropdown d-none d-sm-block">
                    <a href="#" class="nav-link-text" title="Quản lý">
                        <i class="bi bi-grid-3x3-gap" style="font-size:16px"></i>
                        <span class="d-none d-lg-inline">Quản lý</span>
                        <i class="bi bi-chevron-down" style="font-size:10px;opacity:.7"></i>
                    </a>
                    <div class="nav-dropdown-menu">
                        <a href="#" class="nav-dropdown-item">
                            <i class="bi bi-bag-check"></i>
                            <div>
                                <span>Đơn hàng của tôi</span>
                                <span class="item-sub">Theo dõi & quản lý đơn</span>
                            </div>
                        </a>
                        <a href="#" class="nav-dropdown-item">
                            <i class="bi bi-heart"></i>
                            <div>
                                <span>Sản phẩm yêu thích</span>
                                <span class="item-sub">Danh sách đã lưu</span>
                            </div>
                        </a>
                        <a href="#" class="nav-dropdown-item">
                            <i class="bi bi-clock-history"></i>
                            <div>
                                <span>Đã xem gần đây</span>
                                <span class="item-sub">Lịch sử duyệt sản phẩm</span>
                            </div>
                        </a>
                        <a href="#" class="nav-dropdown-item">
                            <i class="bi bi-wallet2"></i>
                            <div>
                                <span>Ví của tôi</span>
                                <span class="item-sub">Số dư & lịch sử giao dịch</span>
                            </div>
                        </a>
                        <a href="#" class="nav-dropdown-item">
                            <i class="bi bi-star"></i>
                            <div>
                                <span>Đánh giá của tôi</span>
                                <span class="item-sub">Xem & viết đánh giá</span>
                            </div>
                        </a>
                    </div>
                </div>

                <a href="#" class="nav-link-text position-relative d-none d-sm-flex" title="Thông báo">
                    <i class="bi bi-bell" style="font-size:17px"></i>
                    <span class="nav-badge">5</span>
                    <span class="d-none d-lg-inline ms-1">Thông báo</span>
                </a>

                <div class="nav-dropdown d-none d-md-block">
                    <a href="#" class="nav-link-text">
                        <i class="bi bi-headset" style="font-size:16px"></i>
                        <span class="d-none d-lg-inline">Hỗ trợ</span>
                        <i class="bi bi-chevron-down" style="font-size:10px;opacity:.7"></i>
                    </a>
                    <div class="nav-dropdown-menu cskh-dropdown-menu">
                        <a href="#" class="nav-dropdown-item">
                            <i class="bi bi-chat-dots" style="color:#2ecc71"></i>
                            <div>
                                <span>Chat trực tuyến</span>
                                <span class="item-sub">Phản hồi trong vài phút</span>
                            </div>
                        </a>
                        <a href="#" class="nav-dropdown-item">
                            <i class="bi bi-telephone"></i>
                            <div>
                                <span>Hotline 1800 2Life</span>
                                <span class="item-sub">Miễn phí · T2–T7, 8h–21h</span>
                            </div>
                        </a>
                        <a href="#" class="nav-dropdown-item">
                            <i class="bi bi-question-circle"></i>
                            <div>
                                <span>Trung tâm trợ giúp</span>
                                <span class="item-sub">FAQ & hướng dẫn</span>
                            </div>
                        </a>
                        <a href="#" class="nav-dropdown-item">
                            <i class="bi bi-flag"></i>
                            <div>
                                <span>Báo cáo vi phạm</span>
                                <span class="item-sub">Gian lận, hàng giả, lừa đảo</span>
                            </div>
                        </a>
                    </div>
                </div>

                <a href="#" class="nav-link-text">
                    <i class="bi bi-person-circle" style="font-size:18px"></i>
                    <span class="d-none d-lg-inline">Tài khoản</span>
                </a>

                <button class="btn-2life-primary d-md-none" style="padding:8px 12px">
                    <i class="bi bi-plus-lg"></i>
                </button>
            </div>

            <div class="col-12 d-md-none">
                <input type="text" class="search-input" placeholder="Tìm kiếm đồ cũ giá hời...">
            </div>

        </div>
    </div>
</header>

<main class="container" style="max-width:1140px;padding-top:8px;padding-bottom:40px">

    <nav class="breadcrumb-2life">
        <a href="index.php">Trang chủ</a><span class="