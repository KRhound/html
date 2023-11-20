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
?>

<!DOCTYPE html>
<html>

<head>
  <title>글 작성</title>
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
  <script>
    function changeForm() {
      var selectedOption = document.getElementById("board-type").value;

      document.getElementById("notice-fields").style.display = "none";
      document.getElementById("board-fields").style.display = "none";
      document.getElementById("qna-fields").style.display = "none";

      if (selectedOption === "notice") {
        document.getElementById("notice-fields").style.display = "block";
      } else if (selectedOption === "board") {
        document.getElementById("board-fields").style.display = "block";
      } else if (selectedOption === "qna") {
        document.getElementById("qna-fields").style.display = "block";
      }
    }

    window.onload = function () {
      var nickname = "<?php echo isset($_SESSION['nickname']) ? $_SESSION['nickname'] : ''; ?>";
      var nicknameField = document.getElementById("nickname");
      if (nicknameField) {
        nicknameField.textContent = nickname;
      }
    };
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
    <h1>글 작성</h1>
    <div class="nickname-container">
      <label class="nickname-label">작성자:</label>
      <span class="nickname-value" id="nickname"></span>
    </div>
    <form action="write_action.php" method="POST" enctype="multipart/form-data">
      <div class="form-container">
        <label for="board-type">게시판 선택</label>
        <select id="board-type" name="board_type" onchange="changeForm()">
          <?php if ($authority >= 2): ?>
            <option value="notice">공지사항</option>
          <?php endif; ?>
          <option value="board">자유게시판</option>
          <option value="qna">Q&amp;A</option>
        </select>
      </div>

      <div id="notice-fields" class="form-container form-field">
        <label for="notice-title">제목</label>
        <input type="text" id="notice-title" name="notice_title" placeholder="제목을 입력하세요">

        <label for="notice-content">내용</label>
        <textarea id="notice-content" name="notice_content" rows="15" placeholder="내용을 입력하세요"></textarea>

        <label for="notice-file">파일 첨부</label>
        <input type="file" id="notice-file" name="notice_file">
      </div>

      <div id="board-fields" class="form-container form-field" style="display: none;">
        <label for="board-title">제목</label>
        <input type="text" id="board-title" name="board_title" placeholder="제목을 입력하세요">

        <label for="board-content">내용</label>
        <textarea id="board-content" name="board_content" rows="15" placeholder="내용을 입력하세요"></textarea>

        <label for="board-file">파일 첨부</label>
        <input type="file" id="board-file" name="board_file">
      </div>

      <div id="qna-fields" class="form-container form-field" style="display: none;">
        <label for="qna-title">제목</label>
        <input type="text" id="qna-title" name="qna_title" placeholder="제목을 입력하세요">

        <label for="qna-content">내용</label>
        <textarea id="qna-content" name="qnatitle" rows="15" placeholder="내용을 입력하세요"></textarea>

        <label for="qna-file">파일 첨부</label>
        <input type="file" id="qna-file" name="qna_file">

        <label for="qna-visibility">공개 여부</label>
        <select id="qna-visibility" name="qna_visibility">
          <option value="public">공개</option>
          <option value="private">비밀글</option>
        </select>
      </div>

      <div class="button-container">
        <button type="submit">글 작성</button>
      </div>
    </form>
  </div>
</body>

</html>