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
$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';

// Prepared Statements를 사용하여 SQL Injection 방지
// 게시글에 대한 사용자별 추천 여부 확인
$stmt = $conn->prepare("SELECT * FROM post_likes WHERE post_id = ? AND username = ?");
$stmt->bind_param("is", $postId, $username);
$stmt->execute();
$result = $stmt->get_result();
$isLiked = $result->num_rows > 0;

if (!$isLiked) {
  // 게시글에 대한 추천 기록 삽입
  $stmt = $conn->prepare("INSERT INTO post_likes (post_id, username) VALUES (?, ?)");
  $stmt->bind_param("is", $postId, $username);
  $stmt->execute();

  // 게시글 추천 수 업데이트
  $stmt = $conn->prepare("UPDATE posts SET likes = likes + 1 WHERE id = ?");
  $stmt->bind_param("i", $postId);
  $stmt->execute();

  // 데이터베이스 연결 종료
  $stmt->close();
  $conn->close();

  // 추천 완료 메시지 출력
  echo '<script>alert("게시물이 추천되었습니다.")</script>';
  ob_flush();
  flush();
  echo '<script>window.location.href = "view.php?id=' . $postId . '";</script>';
  exit;
} else {
  // 데이터베이스 연결 종료
  $stmt->close();
  $conn->close();

  // 이미 추천한 경우 메시지 출력
  echo '<script>alert("이미 추천한 게시물입니다.")</script>';
  ob_flush();
  flush();
  echo '<script>window.location.href = "view.php?id=' . $postId . '";</script>';
  exit;
}
?>