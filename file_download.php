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

// Prepared Statements를 사용하여 SQL Injection 방지
$stmt = $conn->prepare("SELECT file_path, file_name FROM posts WHERE id = ?");
$stmt->bind_param("i", $postId);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($result->num_rows > 0) {
  $file_path = $row['file_path'];
  $file_name = $row['file_name'];

  // 파일이 존재하는지 확인
  if (file_exists($file_path)) {
    // 다운로드 처리
    header("Content-Type: application/octet-stream");
    header("Content-Disposition: attachment; filename=" . $file_name);
    header("Content-Length: " . filesize($file_path));
    readfile($file_path);
    exit;
  } else {
    echo "파일을 찾을 수 없습니다.";
  }
} else {
  echo "게시물을 찾을 수 없습니다.";
}

// 데이터베이스 연결 종료
$stmt->close();
$conn->close();
?>