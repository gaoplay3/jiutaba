<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Content-Type: application/json; charset=utf-8");

$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "jiutaba";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => '数据库连接失败']));
}

$sql = "SELECT s.*, u.* FROM spices AS s JOIN users AS u ON s.user_id = u.user_id";
$result = $conn->query($sql);

if ($result) {
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $spiceImg = [];
        if (!empty($row['spice_img'])) {
            // 直接使用 JSON 数据（假设存储为 Base64 数组）
            $spiceImg = json_decode($row['spice_img'], true);
            if ($spiceImg === null) {
                error_log('JSON 解析失败: ' . json_last_error_msg());
                $spiceImg = [];
            }
        }
        $data[] = [
            'id' => $row['id'],
           'spice_content' => $row['spice_content'],
            'spice_img' => $spiceImg,
            'name' => $row['name'],
            'user_id' => $row['user_id']
        ];
    }
    echo json_encode([
        'success' => true,
        'message' => '查询成功',
        'data' => $data
    ]);
}

$conn->close();
?>