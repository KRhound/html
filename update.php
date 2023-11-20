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

// 게시물 정보 가져오기
$title = $row['title'];
$content = $row['content'];
$fileDestination = $row['file_path'];
$fileName = $row['file_name'];
$publicity = $row['publicity'];

// 데이터베이스 연결 종료
$stmt->close();
$conn->close();

if (!strcmp($username, $row['username'])) {
  echo '<script> alert("작성자와 일치하지 않습니다.") </script>';
  ob_flush();
  flush();
  echo '<script>window.location.href = "view.php?id=' . $postId . '";</script>';
  exit;
}
?>

<!DOCTYPE html>
<html>

<head>
  <title>게시글 수정</title>
  <style>
    .navbar {
      background-color: #333;
      overflow: hidden;
    }

    .navbar a {
      float: left;
      color: #f2f2f2;
      text-align: center;
      padding: 14px 16px;
      text-decoration: none;
      font-size: 17px;
    }

    .navbar a:hover,
    .dropdown:hover .dropbtn {
      background-color: #ddd;
      color: black;
    }

    .dropdown {
      float: right;
      overflow: hidden;
      margin-right: 20px;
    }

    .dropdown .dropbtn {
      font-size: 17px;
      border: none;
      outline: none;
      color: #f2f2f2;
      padding: 14px 16px;
      background-color: inherit;
      font-family: inherit;
      margin: 0;
    }

    .navbar a:hover,
    .dropdown:hover .dropbtn {
      background-color: #ddd;
      color: black;
    }

    .dropdown-content {
      display: none;
      position: absolute;
      background-color: #f9f9f9;
      min-width: 160px;
      box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
      z-index: 1;
    }

    .dropdown-content a {
      float: none;
      color: black;
      padding: 12px 16px;
      text-decoration: none;
      display: block;
      text-align: left;
    }

    .dropdown-content a:hover {
      background-color: #ddd;
    }

    .dropdown:hover .dropdown-content {
      display: block;
    }

    body {
      background-color: #f8f8f8;
      font-family: Arial, sans-serif;
      color: #333;
    }

    .container {
      width: 90%;
      max-width: 1500px;
      margin: 0 auto;
      padding: 40px;
      background-color: #fff;
      box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    }

    h1 {
      text-align: center;
      margin-bottom: 20px;
      color: #333;
    }

    form {
      margin-top: 20px;
    }

    .form-container {
      margin-bottom: 20px;
    }

    label {
      display: block;
      margin-bottom: 5px;
      font-weight: bold;
      font-size: 16px;
    }

    select,
    input[type="text"],
    textarea {
      width: 100%;
      padding: 10px;
      font-size: 14px;
      border: 1px solid #ccc;
      border-radius: 4px;
      box-sizing: border-box;
      resize: vertical;
    }

    .button-container {
      text-align: center;
      margin-top: 20px;
    }

    button {
      padding: 10px 20px;
      background-color: #555;
      color: #fff;
      border: none;
      border-radius: 4px;
      font-size: 16px;
      font-family: Arial, sans-serif;
      cursor: pointer;
    }

    button:hover {
      background-color: #4CAF50;
    }

    .nickname-container {
      margin-bottom: 10px;
    }

    .nickname-label {
      display: block;
      margin-bottom: 5px;
      font-weight: bold;
      font-size: 16px;
    }

    .nickname-value {
      font-weight: bold;
      font-size: 16px;
    }

    @media screen and (max-width: 600px) {
      .container {
        padding: 20px;
      }

      select,
      input[type="text"],
      textarea {
        padding: 5px;
        font-size: 12px;
      }

      button {
        padding: 5px 10px;
        font-size: 14px;
      }
    }
  </style>
</head>

<body>
  <div class="navbar">
    <a href="index.php">메인</a>
    <a class="active" href="notice.php">공지사항</a>
    <a href="board.php">자유게시판</a>
    <a href="qna.php">Q&amp;A</a>
    <div class="dropdown">
      <button class="dropbtn">계정</button>
      <div class="dropdown-content">
        <a href="logout_action.php">로그아웃</a>
      </div>
    </div>
  </div>

  <div class="container">
    <h1>게시글 수정</h1>
    <form action="update_action.php?id=<?php echo $postId; ?>" method="POST" enctype="multipart/form-data">
      <div class="form-container">
        <label for="title">제목</label>
        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?>">

        <label for="content">내용</label>
        <textarea id="content" name="content"
          rows="15"><?php echo htmlspecialchars($content, ENT_QUOTES, 'UTF-8'); ?></textarea>

        <label for="file">파일 첨부</label>
        <input type="file" id="file" name="file">
        <?php if (!empty($fileDestination)): ?>
          <p>현재 첨부된 파일: <a href="<?php echo $fileDestination; ?>" class="file-link" download><?php echo $fileName; ?></a>
          </p>
        <?php endif; ?>

        <?php if ($row['board_type'] === 'qna'): ?>
          <label for="publicity">공개 여부</label>
          <select id="publicity" name="publicity">
            <option value="Public" <?php if ($publicity === 'Public')
              echo 'selected'; ?>>공개</option>
            <option value="Private" <?php if ($publicity === 'Private')
              echo 'selected'; ?>>비공개</option>
          </select>
        <?php endif; ?>
      </div>

      <div class="button-container">
        <button type="submit">수정</button>
      </div>
    </form>
  </div>
</body>

</html>