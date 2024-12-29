<?php
$firmwareDir = 'firmware/';
$latestVersionFile = 'latest_version.txt';

// Khởi tạo file phiên bản nếu chưa tồn tại
if (!file_exists($latestVersionFile)) {
    file_put_contents($latestVersionFile, '1.0');
}

$latestVersion = trim(file_get_contents($latestVersionFile));

// Xử lý API cập nhật firmware
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['version'])) {
    $currentVersion = $_GET['version'];
    if ($currentVersion < $latestVersion) {
        header('Content-Type: application/json');
        echo json_encode([
            'update' => true,
            'version' => $latestVersion,
            'url' => 'http://192.168.1.6/ota/' . $firmwareDir . 'firmware_v' . $latestVersion . '.bin'
        ]);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['update' => false]);
    }
    exit;
}

// Xử lý tải xuống firmware
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['download'])) {
    $file = $firmwareDir . basename($_GET['download']);
    if (file_exists($file)) {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($file) . '"');
        readfile($file);
    } else {
        http_response_code(404);
        echo "Firmware not found.";
    }
    exit;
}

// Xử lý chỉnh sửa phiên bản mới nhất
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_version'])) {
    $newVersion = $_POST['new_version'];
    file_put_contents($latestVersionFile, $newVersion);
    $latestVersion = $newVersion;
    echo "<script>alert('Cập nhật phiên bản thành công!'); window.location.href = '';</script>";
}

// Xử lý upload firmware mới
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['firmware_file'])) {
    $version = $_POST['firmware_version'];
    $file = $_FILES['firmware_file'];
    $filePath = $firmwareDir . 'firmware_v' . $version . '.bin';

    if (move_uploaded_file($file['tmp_name'], $filePath)) {
        echo "<script>alert('Upload firmware thành công!'); window.location.href = '';</script>";
    } else {
        echo "<script>alert('Lỗi khi upload firmware!'); window.location.href = '';</script>";
    }
}

// Xử lý xóa firmware
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['delete'])) {
    $fileToDelete = $firmwareDir . basename($_GET['delete']);
    if (file_exists($fileToDelete)) {
        unlink($fileToDelete);
        echo "<script>alert('Xóa firmware thành công!'); window.location.href = '';</script>";
    } else {
        echo "<script>alert('File không tồn tại!'); window.location.href = '';</script>";
    }
    exit;
}

// Xử lý sửa tên firmware
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_file'])) {
    $oldFileName = $firmwareDir . basename($_POST['old_file']);
    $newFileName = $firmwareDir . 'firmware_v' . $_POST['new_version'] . '.bin';

    if (file_exists($oldFileName)) {
        rename($oldFileName, $newFileName);
        echo "<script>alert('Sửa tên firmware thành công!'); window.location.href = '';</script>";
    } else {
        echo "<script>alert('File không tồn tại!'); window.location.href = '';</script>";
    }
    exit;
}

// Lấy danh sách firmware
$firmwareFiles = array_diff(scandir($firmwareDir), ['.', '..']);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Quản lý Firmware</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
            text-align: left;
        }
        #editModal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            border: 1px solid #ccc;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            padding: 20px;
            z-index: 1000;
        }
        #editModal button {
            margin: 5px;
        }
    </style>
</head>
<body>
    <h1>Quản lý Firmware ESP32</h1>

    <h2>Phiên bản mới nhất: <?php echo $latestVersion; ?></h2>
    <form method="POST">
        <label for="new_version">Cập nhật phiên bản mới nhất:</label>
        <input type="text" id="new_version" name="new_version" value="<?php echo $latestVersion; ?>" required>
        <button type="submit">Cập nhật</button>
    </form>

    <h2>Upload Firmware mới</h2>
    <form method="POST" enctype="multipart/form-data">
        <label for="firmware_version">Phiên bản:</label>
        <input type="text" id="firmware_version" name="firmware_version" required>
        <label for="firmware_file">Chọn file:</label>
        <input type="file" id="firmware_file" name="firmware_file" required>
        <button type="submit">Upload</button>
    </form>

    <h2>Danh sách Firmware</h2>
    <table>
        <thead>
            <tr>
                <th>Tên file</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($firmwareFiles as $file): ?>
                <tr>
                    <td><?php echo $file; ?></td>
                    <td>
                        <a href="?download=<?php echo $file; ?>">Tải xuống</a> |
                        <a href="?delete=<?php echo $file; ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa firmware này?');">Xóa</a> |
                        <button onclick="openEditModal('<?php echo $file; ?>')">Sửa</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div id="editModal">
        <h3>Sửa firmware</h3>
        <form method="POST">
            <input type="hidden" name="old_file" id="old_file">
            <label for="new_version">Phiên bản mới:</label>
            <input type="text" id="new_version" name="new_version" required>
            <button type="submit" name="edit_file">Sửa</button>
            <button type="button" onclick="closeEditModal()">Hủy</button>
        </form>
    </div>

    <script>
        function openEditModal(fileName) {
            document.getElementById('old_file').value = fileName;
            document.getElementById('editModal').style.display = 'block';
        }
        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }
    </script>
</body>
</html>
