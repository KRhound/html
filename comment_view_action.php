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

$postId = isset($_GET['id']) ? $_GET['id'] : '';

// 댓글 리스트를 가져와서 출력
$stmt = $conn->prepare("SELECT * FROM comments WHERE post_id = ?");
$stmt->bind_param("i", $postId);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $commentId = $row['comment_id'];
    $commentNickname = $row['nickname'];
    $commentContent = $row['content'];
    $commentDate = $row['created_at'];

    // 댓글이 삭제된 경우
    if (!strcmp($row['status'], 'inactive')) {
        echo '<div class="comment-item">';
        echo '<div class="comment-nickname">이미 삭제된 댓글입니다.</div>';
        echo '</div>';
    } else {
        echo '<div class="comment-item">';
        echo '<div class="comment-nickname">' . htmlspecialchars($commentNickname, ENT_QUOTES, 'UTF-8') . '</div>';
        echo '<div class="comment-content">' . htmlspecialchars($commentContent, ENT_QUOTES, 'UTF-8') . '</div>';
        echo '<div class="comment-date">' . htmlspecialchars($commentDate, ENT_QUOTES, 'UTF-8') . '</div>';
        echo '<a class="delete-comment-button" href="comment_delete_action.php?comment_id=' . $commentId . '">삭제</a>';
        echo '</div>';
    }
}

$stmt->close();
?>