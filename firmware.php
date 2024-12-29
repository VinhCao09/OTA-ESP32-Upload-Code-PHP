<?php
$firmwareDir = 'firmware/';
$versionFile = 'latest_version.txt'; // File lưu phiên bản mới nhất

// Kiểm tra nếu file version không tồn tại, tạo mặc định
if (!file_exists($versionFile)) {
    file_put_contents($versionFile, '1.0'); // Tạo file với phiên bản mặc định
}

$latestVersion = trim(file_get_contents($versionFile)); // Đọc phiên bản mới nhất từ file
$firmwareFile = $firmwareDir . 'firmware_v' . $latestVersion . '.bin';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['version'])) {
    $currentVersion = $_GET['version'];

    if ($currentVersion < $latestVersion) {
        // Trả về thông tin phiên bản mới
        header('Content-Type: application/json');
        echo json_encode([
            'update' => true,
            'version' => $latestVersion,
            'url' => 'http://192.168.1.6/ota/' . $firmwareFile
        ]);
    } else {
        // Phiên bản đã cập nhật
        header('Content-Type: application/json');
        echo json_encode(['update' => false]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['download'])) {
    // Cung cấp file firmware
    $file = $firmwareDir . basename($_GET['download']);
    if (file_exists($file)) {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($file) . '"');
        readfile($file);
    } else {
        http_response_code(404);
        echo "Firmware not found.";
    }
} else {
    echo "Invalid request.";
}
?>
