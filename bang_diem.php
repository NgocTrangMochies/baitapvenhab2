<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Bảng điểm 3 sinh viên</title>
    <style>
        table {
            width: 60%;
            border-collapse: collapse;
            margin: 20px auto;
            font-family: Arial, sans-serif;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #f4f4f4;
        }
        .dat {
            color: green;
            font-weight: bold;
        }
        .chua-dat {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <h2 style="text-align: center;">Bảng Điểm Sinh Viên</h2>

    <table>
        <thead>
            <tr>
                <th>Họ và tên</th>
                <th>Giữa kỳ</th>
                <th>Cuối kỳ</th>
                <th>Trung bình</th>
                <th>Kết quả</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // 1. Tạo mảng $students gồm 3 sinh viên với name, midterm, final
            $students = [
                ['name' => 'Nguyễn Văn A', 'midterm' => 8.0, 'final' => 7.5],
                ['name' => 'Trần Thị B', 'midterm' => 4.0, 'final' => 4.5],
                ['name' => 'Lê Văn C', 'midterm' => 6.0, 'final' => 5.0] // Đã sửa lại tên bình thường
            ];

            // 2. Viết hàm calculateAverage() tính điểm trung bình
            function calculateAverage(float $midterm, float $final): float {
                return ($midterm + $final) / 2;
            }

            // 3. Dùng foreach để in bảng HTML
            foreach ($students as $student) {
                // Gọi hàm tính điểm
                $avg = calculateAverage($student['midterm'], $student['final']);
                
                // 4. Thêm cột kết quả: Đạt nếu điểm trung bình >= 5
                if ($avg >= 5) {
                    $ketQua = '<span class="dat">Đạt</span>';
                } else {
                    $ketQua = '<span class="chua-dat">Chưa đạt</span>';
                }
                
                // 5. Mã hóa tên sinh viên bằng htmlspecialchars() trước khi in để bảo mật
                $safeName = htmlspecialchars($student['name'], ENT_QUOTES, 'UTF-8');
                
                // In ra các dòng của bảng
                echo "<tr>";
                echo "<td>{$safeName}</td>";
                echo "<td>{$student['midterm']}</td>";
                echo "<td>{$student['final']}</td>";
                echo "<td>" . number_format($avg, 1) . "</td>";
                echo "<td>{$ketQua}</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>

</body>
</html>