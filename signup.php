<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
?>

<!DOCTYPE html>
<html>

<head>
  <title>회원가입</title>
  <style>
    body {
      background-color: #f8f8f8;
      font-family: Arial, sans-serif;
    }

    .container {
      width: 400px;
      margin: 0 auto;
      padding: 40px;
      background-color: #fff;
      box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    }

    .container h2 {
      text-align: center;
      margin-bottom: 30px;
      color: #333;
    }

    .container .form-group {
      margin-bottom: 20px;
    }

    .container .form-group label {
      display: block;
      margin-bottom: 5px;
      font-size: 16px;
      color: #333;
    }

    .container .form-group input,
    .container .form-group select {
      display: block;
      width: 100%;
      padding: 10px;
      font-size: 16px;
      border: 1px solid #ccc;
      border-radius: 4px;
      box-sizing: border-box;
    }

    .container .form-group input[type="submit"] {
      background-color: #555;
      color: #fff;
      cursor: pointer;
    }

    .container .form-group input[type="submit"]:hover {
      background-color: #4CAF50;
    }
  </style>
</head>

<body>
  <div class="container">
    <h2>회원가입</h2>
    <form action="signup_action.php" method="POST">
      <div class="form-group">
        <label for="username">사용자명</label>
        <input type="text" id="username" name="username" required>
      </div>

      <div class="form-group">
        <label for="nickname">닉네임</label>
        <input type="text" id="nickname" name="nickname" required>
      </div>

      <div class="form-group">
        <label for="password">비밀번호</label>
        <input type="password" id="password" name="password" required>
      </div>

      <div class="form-group">
        <label for="email">이메일</label>
        <input type="email" id="email" name="email" required>
      </div>

      <div class="form-group">
        <label for="gender">성별</label>
        <select id="gender" name="gender" required>
          <option value="male">남성</option>
          <option value="female">여성</option>
        </select>
      </div>

      <div class="form-group">
        <input type="submit" value="가입하기">
      </div>
    </form>
  </div>
</body>

</html>