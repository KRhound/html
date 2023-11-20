<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 사용자 ID 가져오기
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'none';
// 사용자 닉네임 가져오기
$nickname = isset($_SESSION['nickname']) ? $_SESSION['nickname'] : 'none';
// 사용자 권한 가져오기
$authority = isset($_SESSION['authority']) ? $_SESSION['authority'] : 0;
// 사용자 인증 여부 가져오기
$verify = isset($_SESSION['verify']) ? $_SESSION['verify'] : 0;

if (strcmp($username, 'none') === 0 || strcmp($nickname, 'none') === 0 || $authority === 0) {
    echo '<script>alert("로그인 필수!")</script>';
    ob_flush();
    flush();
    echo '<script>window.location.href = "login.php";</script>';
    exit;
} else {
    if ($verify === 0) {
        echo '<script>alert("회원 인증 필수!")</script>';
        ob_flush();
        flush();
        echo '<script>window.location.href = "re-send_mail.php";</script>';
        exit;
    }
}

// 데이터베이스 연결
include("./dbConnection.php");

// 게시물 ID 가져오기
$postId = isset($_GET['id']) ? $_GET['id'] : '';

// 댓글 내용 가져오기
$commentContent = isset($_POST['content']) ? $_POST['content'] : '';

// 입력값 검증과 XSS 방지 처리
$commentContent = htmlspecialchars($commentContent, ENT_QUOTES, 'UTF-8');

// 현재 시간
$currentTime = time();

// 현재 시간을 지정된 형식으로 변환
$dateFormat = 'Y-m-d H:i:s'; // 날짜 및 시간 문자열의 형식
$commentTime = date($dateFormat, $currentTime);

// comment 상태 활성화
$status = 'active';

// Prepared Statements를 사용하여 SQL Injection 방지
$stmt = $conn->prepare("INSERT INTO comments (post_id, username, nickname, content, created_at, status) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("isssss", $postId, $username, $nickname, $commentContent, $commentTime, $status);
$stmt->execute();

// 데이터베이스 연결 종료
$stmt->close();
$conn->close();

// 댓글 작성 후 댓글이 작성된 게시글 페이지로 리디렉션
header("Location: view.php?id=$postId");
exit();
?>