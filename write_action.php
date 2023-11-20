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

// 마지막 자동 증가 값 가져오기
// 테이블명
$tableName = 'posts';

// SQL 문 실행
$sql = "SELECT table_name, auto_increment FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = '$tableName'";
$result = $conn->query($sql);

// 결과 가져오기
$currentId = (int) ($result->fetch_row()[1]) + 1;

// 게시판 유형 및 입력값 가져오기
$boardType = isset($_POST['board_type']) ? $_POST['board_type'] : '';
$title = isset($_POST[$boardType . '_title']) ? $_POST[$boardType . '_title'] : '';
$content = isset($_POST[$boardType . '_content']) ? $_POST[$boardType . '_content'] : '';
$nickname = isset($_SESSION['nickname']) ? $_SESSION['nickname'] : '';
$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';

// 입력값 검증과 XSS 방지 처리
$title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
$content = htmlspecialchars($content, ENT_QUOTES, 'UTF-8');

// 파일 첨부 처리
$fileDestination = '';
if (isset($_FILES[$boardType . '_file'])) {
  $file = $_FILES[$boardType . '_file'];

  // 파일 정보 가져오기
  $fileName = $file['name'];
  $fileTmpName = $file['tmp_name'];
  $fileSize = $file['size'];
  $fileType = $file['type'];

  // 파일 저장 경로 설정
  $uploadDir = '/home/server/Desktop/uploads/' . (string) $currentId . '/';
  var_dump($uploadDir);

  // 디렉토리 생성
  if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
  }

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

// 게시판 유형에 따른 추가 처리
$status = 'active';
$publicity = 'none';
if ($boardType === 'qna') {
  $visibility = isset($_POST['qna_visibility']) ? $_POST['qna_visibility'] : '';
  // 비밀글 여부에 따른 처리
  if ($visibility === 'private') {
    $publicity = 'Private';
  } else {
    $publicity = 'Public';
  }
}

// 현재 시간
$createdAt = date('Y-m-d H:i:s');

// 조회수 및 추천수 초기값
$views = 0;
$likes = 0;

// Prepared Statements를 사용하여 SQL Injection 방지
$stmt = $conn->prepare("INSERT INTO posts (board_type, title, content, file_path, file_name, created_at, views, likes, nickname, username, status, publicity) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssssiissss", $boardType, $title, $content, $fileDestination, $fileName, $createdAt, $views, $likes, $nickname, $username, $status, $publicity);
$stmt->execute();

// 데이터베이스 연결 종료
$stmt->close();
$conn->close();

// 성공 메시지 출력
// echo '<script>alert("글이 성공적으로 작성되었습니다.")</script>';
// ob_flush();
// flush();
// echo '<script>window.location.href = "' . $boardType . '.php";</script>';
// exit;
?>