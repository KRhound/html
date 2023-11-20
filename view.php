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

// 파일 다운로드 함수
function downloadFile($file_path, $file_name)
{
  header("Content-Type: application/octet-stream");
  header("Content-Disposition: attachment; filename=" . $file_name);
  header("Content-Length: " . filesize($file_path));
  readfile($file_path);
  exit();
}

?>

<!DOCTYPE html>
<html>

<head>
  <title>게시글 보기</title>
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

    p {
      font-size: 16px;
      margin-bottom: 10px;
    }

    a {
      color: #555;
    }

    .file-link {
      display: block;
      margin-top: 5px;
    }

    .file-link:hover {
      color: #4CAF50;
    }

    .like-button {
      display: inline-block;
      padding: 6px 12px;
      background-color: #4CAF50;
      color: #fff;
      text-decoration: none;
      border-radius: 4px;
      cursor: pointer;
      margin-right: 2px;
    }

    .like-button:hover {
      background-color: #45a049;
    }

    .delete-button {
      display: inline-block;
      padding: 6px 12px;
      background-color: #f44336;
      color: #fff;
      text-decoration: none;
      border-radius: 4px;
      cursor: pointer;
      margin-right: 2px;
    }

    .delete-button:hover {
      background-color: #d32f2f;
    }

    .update-button {
      display: inline-block;
      padding: 6px 12px;
      background-color: #2196F3;
      color: #fff;
      text-decoration: none;
      border-radius: 4px;
      cursor: pointer;
      margin-right: 2px;
    }

    .update-button:hover {
      background-color: #1976D2;
    }

    /* 댓글 폼 스타일 */
    .comment-form-container {
      margin-top: 30px;
      border-top: 1px solid #ccc;
      padding-top: 20px;
    }

    .comment-form-container textarea {
      width: 100%;
      height: 100px;
      padding: 10px;
      font-size: 14px;
      border: 1px solid #ccc;
      border-radius: 4px;
      box-sizing: border-box;
      resize: vertical;
    }

    .comment-form-container .submit-button {
      display: inline-block;
      padding: 6px 12px;
      background-color: #4CAF50;
      color: #fff;
      text-decoration: none;
      border-radius: 4px;
      cursor: pointer;
      margin-top: 10px;
    }

    .comment-form-container .submit-button:hover {
      background-color: #45a049;
    }

    .comment-list {
      margin-top: 30px;
      border-top: 1px solid #ccc;
      padding-top: 20px;
    }

    .comment-item {
      margin-bottom: 10px;
    }

    .comment-item .comment-nickname {
      font-weight: bold;
      margin-bottom: 5px;
    }

    .comment-item .comment-content {
      font-size: 14px;
    }

    /* 댓글 삭제 버튼 스타일 */
    .delete-comment-button {
      display: inline-block;
      padding: 4px 8px;
      background-color: #f44336;
      color: #fff;
      text-decoration: none;
      border-radius: 4px;
      cursor: pointer;
      font-size: 12px;
      margin-left: 10px;
    }

    .delete-comment-button:hover {
      background-color: #d32f2f;
    }
  </style>
  <script>
    window.onload = function () {
      var nickname = "<?php echo $nickname; ?>";
      var nicknameField = document.getElementById("nickname");
      if (nicknameField) {
        nicknameField.textContent = nickname;
      }
    };

    function downloadFile() {
      var fileLink = document.querySelector(".file-link");
      window.location.href = fileLink.getAttribute("href");
    }
  </script>
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
    <?php
    include("./view_action.php");

    echo '<a class="update-button" href="update.php?id=' . $postId . '">수정</a>';
    echo '<a class="delete-button" href="delete_action.php?id=' . $postId . '">삭제</a>';
    ?>

    <!-- 댓글 폼 -->
    <div class="comment-form-container">
      <h2>댓글 작성</h2>
      <form action="comment_write_action.php?id=<?php echo $postId; ?>" method="POST">
        <input type="hidden" name="post_id" value="<?php echo $postId; ?>">
        <textarea name="content" placeholder="댓글을 작성해주세요"></textarea>
        <br>
        <button class="submit-button" type="submit">댓글 작성</button>
      </form>
    </div>

    <!-- 댓글 리스트 -->
    <div class="comment-list">
      <h2>댓글 목록</h2>
      <?php
      // 댓글 리스트를 가져와서 출력
      include("./comment_view_action.php");
      ?>
    </div>
</body>

</html>