<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 토큰 값 받아오기 및 XSS 방지 처리
$token = htmlspecialchars($_GET['token']);

// MySQL 서버에 연결
include("./dbConnection.php");

// 토큰을 이용하여 해당 유저 찾기 (Prepared Statement 사용)
$sql = "SELECT * FROM users WHERE token = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $token);
$stmt->execute();

$result = $stmt->get_result();

// 토큰에 해당하는 유저가 존재하는지 확인
if ($result->num_rows > 0) {
    // 유저의 인증 상태를 업데이트
    $sql = "UPDATE users SET verify = 1 WHERE token = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $token);

    if ($stmt->execute()) {
        echo "회원가입이 인증되었습니다. 이제 로그인하여 서비스를 이용해주세요.";
    } else {
        echo "오류: " . $stmt->error;
    }
} else {
    echo "유효하지 않은 인증 링크입니다.";
}

// 연결 종료
$stmt->close();
$conn->close();
?>