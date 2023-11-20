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

  // 게시판 유형 및 입력값 가져오기
  $title = isset($_POST['title']) ? $_POST['title'] : '';
  $content = isset($_POST['content']) ? $_POST['content'] : '';

  // 입력값 검증과 XSS 방지 처리
  $title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
  $content = htmlspecialchars($content, ENT_QUOTES, 'UTF-8');

  // 파일 첨부 처리
  $fileDestination = '';
  $fileName = '';
  if (isset($_FILES['file'])) {
    $file = $_FILES['file'];

    // 파일 정보 가져오기
    $fileName = $file['name'];
    $fileTmpName = $file['tmp_name'];
    $fileSize = $file['size'];
    $fileType = $file['type'];

    // 파일 저장 경로 설정
    $uploadDir = '/home/server/바탕화면/uploads/';

    if ($fileSize > 0) {
      // 파일 확장자 추출
      $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

      // 파일 저장 이름 생성
      $fileSaveName = uniqid() . '.' . $fileExtension;

      // 파일을 지정된 경로로 이동
      move_uploaded_file($fileTmpName, $uploadDir . $fileSaveName);

      $fileDestination = $uploadDir . $fileSaveName;
    }
  }

  // 게시물 정보 업데이트
  $stmt = $conn->prepare("UPDATE posts SET title = ?, content = ?, file_path = ?, file_name = ? WHERE id = ?");
  $stmt->bind_param("ssssi", $title, $content, $fileDestination, $fileName, $postId);
  $stmt->execute();

  // 게시물 유형이 qna인 경우, 공개 여부 업데이트
  if ($row['board_type'] === 'qna') {
    $publicity = isset($_POST['publicity']) ? $_POST['publicity'] : '';
    $stmt = $conn->prepare("UPDATE posts SET publicity = ? WHERE id = ?");
    $stmt->bind_param("si", $publicity, $postId);
    $stmt->execute();
  }

  // 데이터베이스 연결 종료
  $stmt->close();
  $conn->close();

  // 성공 메시지 출력
  echo '<script>alert("게시물이 성공적으로 수정되었습니다.)</script>';
  ob_flush();
  flush();
  echo '<script>window.location.href = "view.php?id=' . $postId . '";</script>';
  exit;
} else {
  // 데이터베이스 연결 종료
  $stmt->close();
  $conn->close();

  echo '<script> alert("작성자와 일치하지 않습니다.") </script>';
  ob_flush();
  flush();
  echo '<script>window.location.href = "view.php?id=' . $postId . '";</script>';
  exit;
}
?>