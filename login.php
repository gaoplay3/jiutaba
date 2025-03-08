<?php
    // 允许所有来源的请求，可根据实际需求调整
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Allow-Headers: Content-Type');

    // 数据库连接配置
    $servername = "localhost";
    $username = "root";
    $password = "root";
    $dbname = "jiutaba";

    // 创建连接
    $conn = new mysqli($servername, $username, $password, $dbname);

    // 检查连接
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
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
    // $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // 查询数据库中是否存在该用户
    $sql = "SELECT * FROM users WHERE name =? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $hashedPassword = $row['password'];
        $user_id = $row['user_id'];

        // 验证密码
        if (password_verify($password, $hashedPassword)) {
            $response = array(
                'status' =>'success',
                'message' => '登录成功',
                'user_id' => $user_id
            );
        } else {
            $response = array(
                'status' => 'error',
                'message' => '密码错误'
            );
        }
    } else {
        $response = array(
            'status' => 'error',
            'message' => '用户不存在'
        );
    }

    // 关闭数据库连接
    $stmt->close();
    $conn->close();

    // 返回 JSON 响应
    header('Content-Type: application/json');
    echo json_encode($response);
?>