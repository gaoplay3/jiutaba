<?php
// 允许跨域请求
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Content-Type: application/json; charset=utf-8');

// 处理 OPTIONS 预检请求
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

// 数据库连接配置
$servername = "localhost";
$username = "root"; // 替换为你的数据库用户名
$password = "root"; // 替换为你的数据库密码
$dbname = "jiutaba"; // 替换为你的数据库名

// 创建数据库连接
$conn = new mysqli($servername, $username, $password, $dbname);

// 检查连接是否成功
if ($conn->connect_error) {
    die(json_encode(array(
        'success' => false,
        'message' => '数据库连接失败: '. $conn->connect_error
    )));
}

// 获取前端发送的 JSON 数据
$json = file_get_contents('php://input');
error_log('接收到的原始数据: '. $json);
$data = json_decode($json, true);
if ($data === null) {
    error_log('JSON 解析错误: '. json_last_error_msg());
    $response = array(
        'success' => false,
        'message' => 'JSON 解析错误: '. json_last_error_msg()
    );
    echo json_encode($response);
    $conn->close();
    exit;
}

$spice_content = $data['spice_content'] ?? '';
$user_id = $data['user_id'] ?? '';
$spice_img = $data['spice_img'] ?? [];

// 数据验证
if (empty($spice_content)) {
    $response = array(
        'success' => false,
        'message' => '内容不能为空'
    );
    echo json_encode($response);
    $conn->close();
    exit;
}

// 将转盘选项数组转换为 JSON 字符串
$spice_img = json_encode($spice_img);

// 假设这里有一个 user_id，你可以根据实际情况从会话或其他方式获取
// $user_id = 1; // 替换为实际的用户 ID

// 准备 SQL 语句
$sql = "INSERT INTO spices (spice_content, spice_img, user_id) VALUES (?,?,?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssi", $spice_content, $spice_img, $user_id);

// 执行 SQL 语句
if ($stmt->execute()) {
    $response = array(
        'success' => true,
        'message' => '添加成功'
    );
} else {
    $response = array(
        'success' => false,
        'message' => '添加失败: '. $stmt->error
    );
}

// 关闭数据库连接
$stmt->close();
$conn->close();

// 返回 JSON 响应
// 返回 JSON 响应
header('Content-Type: application/json');
if (json_encode($response) === false) {
    error_log('JSON 编码错误: '. json_last_error_msg());
    $response = array(
        'success' => false,
        'message' => '添加失败：JSON 编码错误'
    );
    echo json_encode($response);
} else {
    echo json_encode($response);
}
?>