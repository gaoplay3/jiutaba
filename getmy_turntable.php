<?php
// 允许跨域请求，可根据实际情况调整
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// 数据库连接配置
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "jiutaba";

// 创建数据库连接
$conn = new mysqli($servername, $username, $password, $dbname);

// 检查连接是否成功
if ($conn->connect_error) {
    die("数据库连接失败: " . $conn->connect_error);
}

// 获取前端传递的 user_id
$user_id = $_GET['user_id']?? null;

// 验证 user_id 是否有效
if ($user_id === null) {
    $response = [
        'status' => 'error',
        'message' => '未提供有效的 user_id'
    ];
    // 设置响应头为 JSON 格式
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// 准备 SQL 查询语句
$sql = "SELECT * FROM my_turntables WHERE user_id =?";
$stmt = $conn->prepare($sql);

// 检查 SQL 语句准备是否成功
if (!$stmt) {
    $response = [
        'status' => 'error',
        'message' => 'SQL 语句准备失败: '. $conn->error
    ];
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// 绑定参数
$stmt->bind_param("i", $user_id);

// 执行查询
if (!$stmt->execute()) {
    $response = [
        'status' => 'error',
        'message' => '查询执行失败: '. $stmt->error
    ];
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// 获取查询结果
$result = $stmt->get_result();

// 处理查询结果
if ($result->num_rows > 0) {
    $turntables = [];
    while ($row = $result->fetch_assoc()) {
        $turntables[] = $row;
    }
    $response = [
        'status' =>'success',
        'message' => '查询成功',
        'data' => $turntables
    ];
} else {
    $response = [
        'status' => 'error',
        'message' => '未找到相关转盘记录'
    ];
}

// 关闭数据库连接和语句
$stmt->close();
$conn->close();

// 返回 JSON 响应
header('Content-Type: application/json');
echo json_encode($response);
?>