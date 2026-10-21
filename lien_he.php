<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Form Liên Hệ An Toàn</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; }
        .container { max-width: 500px; margin: 0 auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 15px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; }
        input[type="text"], input[type="email"], textarea, input[type="file"] { 
            width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; 
        }
        .error { color: red; font-size: 0.85em; display: block; margin-top: 5px; }
        .success { color: green; font-weight: bold; margin-bottom: 15px; padding: 10px; background: #e6ffe6; border: 1px solid #b3ffb3; border-radius: 4px;}
        button { background-color: #28a745; color: #fff; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; font-size: 1em; width: 100%;}
        button:hover { background-color: #218838; }
    </style>
</head>
<body>

<div class="container">
    <h2 style="text-align: center;">Liên hệ</h2>
    <p style="text-align: center; color: #666;">Vui lòng nhập đầy đủ thông tin bên dưới.</p>

    <?php
    // Hàm hỗ trợ mã hóa dữ liệu đầu ra để chống XSS
    function e($value) {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    // Khởi tạo mảng dữ liệu và lỗi
    $data = ['name' => '', 'email' => '', 'subject' => '', 'message' => ''];
    $errors = [];
    $successMsg = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // 1. Sanitization: Làm sạch khoảng trắng thừa
        $data['name'] = trim($_POST['name'] ?? '');
        $data['email'] = trim($_POST['email'] ?? '');
        $data['subject'] = trim($_POST['subject'] ?? '');
        $data['message'] = trim($_POST['message'] ?? '');

        // 2. Validation: Kiểm tra dữ liệu hợp lệ
        if ($data['name'] === '') {
            $errors['name'] = 'Vui lòng nhập họ tên.';
        }

        if ($data['email'] === '') {
            $errors['email'] = 'Vui lòng nhập email.';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email không hợp lệ.';
        }

        if ($data['message'] === '') {
            $errors['message'] = 'Vui lòng nhập nội dung.';
        } elseif (strlen($data['message']) < 10 || strlen($data['message']) > 500) {
            $errors['message'] = 'Nội dung phải từ 10 đến 500 ký tự.';
        }

        // 3. Xử lý Upload file an toàn (3 kiểm tra bắt buộc)
        $avatar = $_FILES['avatar'] ?? null;
        $extension = '';
        
        if ($avatar && $avatar['error'] !== UPLOAD_ERR_NO_FILE) {
            if ($avatar['error'] !== UPLOAD_ERR_OK) {
                $errors['avatar'] = 'Có lỗi xảy ra trong quá trình tải ảnh lên.';
            } else {
                // Kiểm tra 1: Kích thước tối đa (ví dụ 2MB)
                $maxSize = 2 * 1024 * 1024;
                if ($avatar['size'] > $maxSize) {
                    $errors['avatar'] = 'Kích thước ảnh không được vượt quá 2MB.';
                }

                // Kiểm tra 2: Định dạng ảnh cho phép
                $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];
                $fileInfo = pathinfo($avatar['name']);
                $extension = strtolower($fileInfo['extension'] ?? '');
                
                if (!in_array($extension, $allowedExts)) {
                    $errors['avatar'] = 'Chỉ cho phép tải lên định dạng JPG, JPEG, PNG, GIF.';
                }
            }
        }

        // Nếu không có lỗi, tiến hành lưu trữ
        if (empty($errors)) {
            // Kiểm tra 3: Đổi tên file an toàn và lưu file nếu có upload
            if ($avatar && $avatar['error'] === UPLOAD_ERR_OK) {
                $uploadDir = 'uploads/';
                // Đổi tên file ngẫu nhiên để tránh ghi đè và tên file nguy hiểm
                $safeFileName = uniqid('avatar_') . '.' . $extension;
                $targetFile = $uploadDir . $safeFileName;

                if (!move_uploaded_file($avatar['tmp_name'], $targetFile)) {
                    $errors['avatar'] = 'Không thể lưu ảnh vào máy chủ.';
                }
            }

            if (empty($errors)) {
                $successMsg = 'Gửi liên hệ thành công!';
                // Reset form sau khi gửi thành công
                $data = ['name' => '', 'email' => '', 'subject' => '', 'message' => ''];
            }
        }
    }
    ?>

    <?php if ($successMsg !== ''): ?>
        <div class="success"><?= $successMsg ?></div>
    <?php endif; ?>

    <!-- Form yêu cầu thuộc tính enctype để có thể upload file -->
    <form method="post" action="" enctype="multipart/form-data">
        <div class="form-group">
            <label>Họ tên</label>
            <!-- Giữ lại dữ liệu đã nhập và mã hóa bằng hàm e() để chống XSS -->
            <input type="text" name="name" value="<?= e($data['name']) ?>" placeholder="Nhập họ tên...">
            <?php if (isset($errors['name'])) echo "<span class='error'>{$errors['name']}</span>"; ?>
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" value="<?= e($data['email']) ?>" placeholder="Ví dụ: haovanle@edu.vn">
            <?php if (isset($errors['email'])) echo "<span class='error'>{$errors['email']}</span>"; ?>
        </div>

        <div class="form-group">
            <label>Chủ đề</label>
            <input type="text" name="subject" value="<?= e($data['subject']) ?>" placeholder="Nhập chủ đề...">
        </div>

        <div class="form-group">
            <label>Nội dung</label>
            <textarea name="message" rows="5" placeholder="Nhập nội dung liên hệ..."><?= e($data['message']) ?></textarea>
            <?php if (isset($errors['message'])) echo "<span class='error'>{$errors['message']}</span>"; ?>
        </div>

        <div class="form-group">
            <label>Ảnh đại diện (Tùy chọn)</label>
            <input type="file" name="avatar" accept="image/png, image/jpeg, image/gif">
            <?php if (isset($errors['avatar'])) echo "<span class='error'>{$errors['avatar']}</span>"; ?>
        </div>

        <button type="submit">Gửi liên hệ</button>
    </form>
</div>

</body>
</html>