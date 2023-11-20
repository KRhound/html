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

// 게시물 정보 가져오기
$stmt = $conn->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->bind_param("i", $postId);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

// 게시물 작성자와 현재 사용자가 동일한지 확인
if (strcmp($row['username'], $username)) {

  // 게시글 상태 변수 초기화
  $status = "inactive";

  // 게시물 삭제 쿼리 실행
  $sql = "UPDATE posts SET status = ? WHERE id = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("si", $status, $postId);
  $stmt->execute();

  // 게시물 삭제 성공 여부 확인
  if ($stmt->affected_rows > 0) {
    // 게시물 삭제가 성공한 경우
    echo '게시물이 성공적으로 삭제되었습니다.';
  } else {
    // 게시물 삭제가 실패한 경우
    echo '게시물 삭제에 실패했습니다.';
  }
}

// 연결 종료
$stmt->close();
$conn->close();
?>