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
$stmt = $conn->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->bind_param("i", $postId);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

//access_time 검사 및 정보 삽입
$stmt = $conn->prepare("SELECT * FROM user_access WHERE post_id = ? AND username = ?");
$stmt->bind_param("is", $postId, $username);
$stmt->execute();
$result_access = $stmt->get_result();
$row_access = $result_access->fetch_assoc();
$isAccess = $result_access->num_rows > 0;

// 현재 시간
$access_time = time();

// 현재 시간을 지정된 형식으로 변환
$date_format = 'Y-m-d H:i:s'; // 날짜 및 시간 문자열의 형식
$access_date = date($date_format, $access_time);

if (!$isAccess) {
  // 게시글에 대한 조회 기록 삽입
  $stmt = $conn->prepare("INSERT INTO user_access (post_id, username, access_time) VALUES (?, ?, ?)");
  $stmt->bind_param("iss", $postId, $username, $access_date);
  $stmt->execute();

  // 게시글 조회 수 업데이트
  $stmt = $conn->prepare("UPDATE posts SET views = views + 1 WHERE id = ?");
  $stmt->bind_param("i", $postId);
  $stmt->execute();
} else {
  // 데이터베이스에 저장된 시간을 타임스탬프로 변환
  $db_saved_timestamp = strtotime($row_access['access_time']);

  // 분 단위로 지난 시간 계산
  $time_diff_minutes = ($access_time - $db_saved_timestamp) / 60;

  // 1시간 이상 지났는지 확인
  if ($time_diff_minutes >= 60) {
    // 조회수 증가
    $stmt = $conn->prepare("UPDATE posts SET views = views + 1 WHERE id = ?");
    $stmt->bind_param("i", $postId);
    $stmt->execute();

    $stmt = $conn->prepare("UPDATE user_access SET access_time = ? WHERE post_id = ? AND username = ?");
    $stmt->bind_param("sis", $access_date, $postId, $username);
    $stmt->execute();
    echo "조회수 증가";
  } else {
    echo "60분 이후 조회수 증가 => " . $time_diff_minutes;
  }
}

// 게시판 유형에 따른 추가 처리
$visibility = 'none';
if ($row['board_type'] === 'qna') {
  if ($row['publicity'] === 'Private') {
    $visibility = '비밀글';
  } else if ($row['publicity'] === 'Public') {
    $visibility = '공개글';
  }
}

// 추천 수 가져오기
$likes = isset($row['likes']) ? $row['likes'] : '';

// 게시글 정보 출력
echo '<h1>' . htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8') . '</h1>';
echo '<p>작성자: ' . htmlspecialchars($row['nickname'], ENT_QUOTES, 'UTF-8') . '</p>';
echo '<p>작성일: ' . htmlspecialchars($row['created_at'], ENT_QUOTES, 'UTF-8') . '</p>';
echo '<p>조회수: ' . htmlspecialchars($row['views'], ENT_QUOTES, 'UTF-8') . '</p>';
echo '<p>내용: ' . htmlspecialchars($row['content'], ENT_QUOTES, 'UTF-8') . '</p>';

// 파일 첨부가 있는 경우에만 출력 및 다운로드 링크 생성
if (!empty($row['file_path'])) {
  echo '<p>첨부 파일: <a href="file_download.php" class="file-link">' . htmlspecialchars($row['file_name'], ENT_QUOTES, 'UTF-8') . '</a></p>';
  echo '<script>
    // 파일 다운로드 함수 호출
    function downloadFile() {
      window.location.href = "file_download.php?id=' . $postId . '";
    }

    // 파일 다운로드 링크 클릭 이벤트 처리
    var fileLink = document.querySelector(".file-link");
    fileLink.addEventListener("click", function(event) {
      event.preventDefault();
      downloadFile();
    });
  </script>';
}

// 게시판 유형에 따른 추가 정보 출력
if ($row['board_type'] === 'qna') {
  echo '<p>공개 여부: ' . $visibility . '</p>';
}

// 추천 버튼
echo '<p>추천 수: ' . $likes . '</p>';
echo '<a href="like_counting.php?id=' . $postId . '" class="like-button">추천</a>';

// 데이터베이스 연결 종료
$stmt->close();
$conn->close();
?>