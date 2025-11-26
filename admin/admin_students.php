<?php
session_start();
// Bảo vệ trang
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Quản lý học viên</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="css/admin_styles.css">
</head>
<body>

    <?php include 'templates/sidebar.php'; ?>

    <div class="main-wrapper">
        <header class="main-header">
            <div class="header-title">
                <h1>Quản lý học viên</h1>
                <p>Quản lý và theo dõi thông tin học viên</p>
            </div>
        </header>

        <main class="main-content">
            <section class="stat-cards-grid">
                <div class="stat-card">
                    <div class="card-info">
                        <span class="card-title">Tổng học viên</span>
                        <span class="card-value">6</span>
                    </div>
                    <div class="card-icon icon-blue">
                        <i class="fa-solid fa-users"></i>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="card-info">
                        <span class="card-title">Đang hoạt động</span>
                        <span class="card-value">5</span>
                    </div>
                    <div class="card-icon icon-green">
                        <i class="fa-solid fa-user-check"></i>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="card-info">
                        <span class="card-title">Mới tháng này</span>
                        <span class="card-value">12</span>
                    </div>
                    <div class="card-icon icon-time"> <i class="fa-solid fa-user-clock"></i>
                    </div>
                </div>
            </section>

            <section class="table-container">
                <div class="table-header">
                    <h2>Danh sách học viên</h2>
                    <div class="table-controls">
                        <div class="search-box">
                            <i class="fa-solid fa-search"></i>
                            <input type="text" placeholder="Tìm kiếm học viên...">
                        </div>
                        <button class="btn btn-primary">
                            <i class="fa-solid fa-plus"></i>
                            <span>Thêm học viên</span>
                        </button>
                    </div>
                </div>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Học viên</th>
                            <th>Email</th>
                            <th>Khóa học ĐK</th>
                            <th>Ngày tham gia</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                        <tr>
                            <td>
                                <div class="avatar-cell">
                                    <div class="avatar avatar-n">N</div> <span>Nguyễn Văn Nam</span>
                                </div>
                            </td>
                            <td>nam.nv@email.com</td>
                            <td>0901234567</td>
                            <td>3</td>
                            <td>1</td>
                            <td>15/01/2025</td>
                            <td><span class="status status-active">Đang hoạt động</span></td>
                            <td>
                                <button class="btn-icon btn-edit"><i class="fa-solid fa-pencil"></i></button>
                                <button class="btn-icon btn-delete"><i class="fa-solid fa-trash"></i></button>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="avatar-cell">
                                    <div class="avatar avatar-m">M</div> <span>Trần Thị Mai</span>
                                </div>
                            </td>
                            <td>mai.tt@email.com</td>
                            <td>0902345678</td>
                            <td>5</td>
                            <td>3</td>
                            <td>20/01/2025</td>
                            <td><span class="status status-active">Đang hoạt động</span></td>
                            <td>
                                <button class="btn-icon btn-edit"><i class="fa-solid fa-pencil"></i></button>
                                <button class="btn-icon btn-delete"><i class="fa-solid fa-trash"></i></button>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="avatar-cell">
                                    <div class="avatar avatar-a">A</div> <span>Lê Hoàng Anh</span>
                                </div>
                            </td>
                            <td>anh.lh@email.com</td>
                            <td>0903456789</td>
                            <td>2</td>
                            <td>0</td>
                            <td>25/01/2025</td>
                            <td><span class="status status-active">Đang hoạt động</span></td>
                            <td>
                                <button class="btn-icon btn-edit"><i class="fa-solid fa-pencil"></i></button>
                                <button class="btn-icon btn-delete"><i class="fa-solid fa-trash"></i></button>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="avatar-cell">
                                    <div class="avatar avatar-l">L</div> <span>Phạm Thị Lan</span>
                                </div>
                            </td>
                            <td>lan.pt@email.com</td>
                            <td>0904567890</td>
                            <td>4</td>
                            <td>2</td>
                            <td>01/02/2025</td>
                            <td><span class="status status-inactive">Không hoạt động</span></td>
                            <td>
                                <button class="btn-icon btn-edit"><i class="fa-solid fa-pencil"></i></button>
                                <button class="btn-icon btn-delete"><i class="fa-solid fa-trash"></i></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </section>
        </main>
    </div>

</body>
</html>