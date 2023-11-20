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

if (strcmp($username, 'none') === 1) {
  echo '<script>alert("이미 로그인 완료!")</script>';
  header("Location: login.php");
}

?>
<!DOCTYPE html>
<html>

<head>
  <title>로그인</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f2f2f2;
      margin: 0;
      padding: 0;
    }

    .container {
      width: 400px;
      margin: 100px auto;
      background-color: #fff;
      border-radius: 5px;
      padding: 20px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
    }

    h1 {
      text-align: center;
      margin-bottom: 30px;
      color: #333;
    }

    .container .form-group {
      margin-bottom: 20px;
    }

    .container .form-group label {
      display: block;
      font-size: 16px;
      margin-bottom: 5px;
    }

    .container .form-group input {
      width: 100%;
      font-size: 16px;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 4px;
      box-sizing: border-box;
    }

    .container .form-group .btn {
      display: block;
      width: 100%;
      padding: 10px;
      background-color: #555;
      color: #fff;
      border: none;
      border-radius: 3px;
      cursor: pointer;
    }

    .container .form-group .btn:hover {
      background-color: #4caf50;
    }

    .container .form-group .text-center {
      text-align: center;
    }
  </style>
</head>

<body>
  <div class="container">
    <h1>로그인</h1>
    <form action="login_action.php" method="POST">
      <div class="form-group">
        <label for="username">사용자명:</label>
        <input type="text" id="username" name="username" required>
      </div>
      <div class="form-group">
        <label for="password">비밀번호:</label>
        <input type="password" id="password" name="password" required>
      </div>
      <div class="form-group">
        <input type="submit" class="btn" value="로그인">
      </div>
      <div class="form-group text-center">
        <p>계정이 없으신가요? <a href="signup.php">회원가입</a></p>
      </div>
    </form>
  </div>
</body>

</html>