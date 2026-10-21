<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Máy tính điểm học phần</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f9f9f9; padding: 20px; }
        .container { max-width: 500px; margin: 0 auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 15px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; }
        input[type="text"], input[type="number"] { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        .error { color: red; font-size: 0.85em; display: block; margin-top: 5px; }
        .result { margin-top: 20px; padding: 15px; background-color: #e9ecef; border-left: 4px solid #28a745; border-radius: 4px; }
        button { background-color: #007bff; color: #fff; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; font-size: 1em; }
        button:hover { background-color: #0056b3; }
    </style>
</head>
<body>

<div class="container">
    <h2 style="text-align: center;">Máy Tính Điểm Học Phần</h2>

    <?php
    // Hàm kiểm tra điểm hợp lệ (từ 0 đến 10)
    function isValidScore($score): bool {
        return is_numeric($score) && $score >= 0 && $score <= 10;
    }

    // Hàm tính điểm tổng kết (10% cc, 30% giữa kỳ, 60% cuối kỳ)
    function finalScore(float $cc, float $mid, float $final): float {
        return $cc * 0.10 + $mid * 0.30 + $final * 0.60;
    }

    // Khởi tạo các biến để lưu trữ dữ liệu và lỗi
    $name = $cc = $mid = $final = '';
    $errors = [];
    $resultHtml = '';

    // Xử lý khi người dùng submit form
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Đọc dữ liệu và loại bỏ khoảng trắng thừa
        $name = trim($_POST['name'] ?? '');
        $cc = trim($_POST['cc'] ?? '');
        $mid = trim($_POST['mid'] ?? '');
        $final = trim($_POST['final'] ?? '');

        // Validation phía server
        if ($name === '') {
            $errors['name'] = 'Vui lòng nhập họ tên.';
        }
        if ($cc === '' || !isValidScore($cc)) {
            $errors['cc'] = 'Điểm chuyên cần phải là số từ 0 đến 10.';
        }
        if ($mid === '' || !isValidScore($mid)) {
            $errors['mid'] = 'Điểm giữa kỳ phải là số từ 0 đến 10.';
        }
        if ($final === '' || !isValidScore($final)) {
            $errors['final'] = 'Điểm cuối kỳ phải là số từ 0 đến 10.';
        }

        // Nếu không có lỗi, tiến hành tính điểm và xếp loại
        if (empty($errors)) {
            $totalScore = finalScore((float)$cc, (float)$mid, (float)$final);

            // Xác định xếp loại
            if ($totalScore >= 8) {
                $grade = 'Giỏi';
            } elseif ($totalScore >= 6.5) {
                $grade = 'Khá';
            } elseif ($totalScore >= 5) {
                $grade = 'Trung bình';
            } else {
                $grade = 'Chưa đạt';
            }

            // Mã hóa đầu ra để chống XSS
            $safeName = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
            $safeTotal = number_format($totalScore, 1);

            $resultHtml = "
                <div class='result'>
                    <h3 style='margin-top: 0;'>Kết quả học tập</h3>
                    <p><strong>Sinh viên:</strong> {$safeName}</p>
                    <p><strong>Điểm tổng kết:</strong> {$safeTotal}</p>
                    <p><strong>Xếp loại:</strong> {$grade}</p>
                </div>
            ";
        }
    }
    ?>

    <!-- Form nhập liệu -->
    <form method="post" action="">
        <div class="form-group">
            <label>Họ và tên</label>
            <input type="text" name="name" value="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>" placeholder="Nhập họ tên...">
            <?php if (isset($errors['name'])) echo "<span class='error'>{$errors['name']}</span>"; ?>
        </div>

        <div class="form-group">
            <label>Điểm chuyên cần (10%)</label>
            <input type="number" step="0.1" name="cc" value="<?= htmlspecialchars($cc, ENT_QUOTES, 'UTF-8') ?>" placeholder="0 - 10">
            <?php if (isset($errors['cc'])) echo "<span class='error'>{$errors['cc']}</span>"; ?>
        </div>

        <div class="form-group">
            <label>Điểm giữa kỳ (30%)</label>
            <input type="number" step="0.1" name="mid" value="<?= htmlspecialchars($mid, ENT_QUOTES, 'UTF-8') ?>" placeholder="0 - 10">
            <?php if (isset($errors['mid'])) echo "<span class='error'>{$errors['mid']}</span>"; ?>
        </div>

        <div class="form-group">
            <label>Điểm cuối kỳ (60%)</label>
            <input type="number" step="0.1" name="final" value="<?= htmlspecialchars($final, ENT_QUOTES, 'UTF-8') ?>" placeholder="0 - 10">
            <?php if (isset($errors['final'])) echo "<span class='error'>{$errors['final']}</span>"; ?>
        </div>

        <button type="submit">Tính Điểm</button>
    </form>

    <?= $resultHtml ?>

</div>

</body>
</html>