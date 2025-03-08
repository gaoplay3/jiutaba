<?php
// 允许所有来源的请求，可根据实际需求调整
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// 处理 JSON 数据
$contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
if (strpos($contentType, 'application/json') === 0) {
    $inputJSON = file_get_contents('php://input');
    $inputArray = json_decode($inputJSON, TRUE);
    if (json_last_error() === JSON_ERROR_NONE) {
        $_POST = array_merge($_POST, $inputArray);
    }
}

// 数据库连接配置
$servername = "localhost";
$username = "root"; 
$password = "root"; 
$dbname = "jiutaba"; 

// 创建数据库连接
$conn = new mysqli($servername, $username, $password, $dbname);

// 检查连接是否成功
if ($conn->connect_error) {
    die("数据库连接失败: ". $conn->connect_error);
}

// 获取前端发送的数据
$name = $_POST['name']?? '';
$password = $_POST['password']?? '';

// 数据验证
if (empty($name) || empty($password)) {
    $response = array(
       'status' => 'error',
       'message' => '用户名和密码不能为空'
    );
    echo json_encode($response);
    exit;
}

// 对密码进行哈希处理，提高安全性
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// 准备 SQL 语句
$sql = "INSERT INTO users (name, password) VALUES ('$name', '$hashedPassword')";

// 执行 SQL 语句
if ($conn->query($sql) === TRUE) {
    $response = array(
       'status' =>'success',
       'message' => '注册成功'
    );
} else {
    $response = array(
       'status' => 'error',
       'message' => '注册失败: '. $conn->error
    );
}

// 关闭数据库连接
$conn->close();

// 返回 JSON 响应
header('Content-Type: application/json');
echo json_encode($response);
?>