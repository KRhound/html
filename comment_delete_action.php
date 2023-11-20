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

// 댓글 ID 가져오기
$commentId = isset($_GET['comment_id']) ? $_GET['comment_id'] : '';

// 게시물 정보 가져오기
$stmt = $conn->prepare("SELECT * FROM comments WHERE comment_id = ?");
$stmt->bind_param("i", $commentId);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$postId = $row['post_id'];

// 게시물 작성자와 현재 사용자가 동일한지 확인
if (strcmp($row['username'], $username)) {
    // 데이터베이스 연결 종료
    $stmt->close();
    $conn->close();

    echo '<script> alert("작성자와 일치하지 않습니다.") </script>';
    ob_flush();
    flush();
    echo '<script>window.location.href = "view.php?id=' . $postId . '";</script>';
    exit;
}

$status = 'inactive';

// 댓글 삭제
$stmt = $conn->prepare("UPDATE comments SET status = ? WHERE comment_id = ?");
$stmt->bind_param("si", $status, $commentId);
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    // 댓글 삭제 성공 시
    ob_flush();
    flush();
    echo '<script>alert("댓글이 삭제되었습니다.")</script>';
} else {
    // 댓글 삭제 실패 시
    ob_flush();
    flush();
    echo '<script>alert("댓글 삭제에 실패했습니다.")</script>';
}

// 데이터베이스 연결 종료
$stmt->close();
$conn->close();

ob_flush();
flush();
echo '<script>window.history.back();</script>';
exit;
?>