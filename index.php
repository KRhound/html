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
?>


<!DOCTYPE html>
<html>

<head>
  <title>메인 페이지</title>
  <style>
    /* 스타일 적용 */
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

    .container {
      display: flex;
      align-items: center;
      justify-content: center;
      flex-direction: column;
      height: 100vh;
      text-align: center;
    }

    .container h1 {
      font-size: 28px;
      margin-bottom: 20px;
    }

    .container p {
      font-size: 18px;
    }

    .slideshow-container {
      position: relative;
      width: 100%;
      max-width: 600px;
      margin: 0 auto;
    }

    .slideshow-container .slide {
      display: none;
      position: absolute;
      width: 100%;
      height: auto;
      text-align: center;
      top: 50%;
      transform: translateY(-50%);
    }

    .slideshow-container .slide img {
      max-width: 100%;
      max-height: 100%;
      object-fit: contain;
    }

    /* 추가된 스타일 */
    .slideshow-container .slide.active {
      display: block;
    }

    .slide h1 {
      font-size: 24px;
      margin-top: 10px;
    }

    .slide p {
      font-size: 18px;
      margin-top: 10px;
    }
  </style>
</head>

<body>
  <div class="navbar">
    <a href="index.php">메인</a>
    <a href="notice.php">공지사항</a>
    <a href="board.php">자유게시판</a>
    <a href="qna.php">Q&amp;A</a>
    <div class="dropdown">
      <button class="dropbtn">계정</button>
      <div class="dropdown-content">
        <?php if (strcmp($username, 'none')) { ?>
          <a href="logout_action.php">로그아웃</a>
        <?php } else { ?>
          <a href="login.php">로그인</a>
          <a href="signup.php">회원가입</a>
        <?php } ?>
      </div>
    </div>
  </div>

  <div class="container">
    <div class="slideshow-container">
      <div class="slide active">
        <h1>제품명 1</h1>
        <img src="image1.jpg" alt="이미지 1">
        <p>제품에 대한 설명 1</p>
      </div>
      <div class="slide">
        <h1>제품명 2</h1>
        <img src="image2.jpg" alt="이미지 2">
        <p>제품에 대한 설명 2</p>
      </div>
      <div class="slide">
        <h1>제품명 3</h1>
        <img src="image3.jpg" alt="이미지 3">
        <p>제품에 대한 설명 3</p>
      </div>
    </div>
  </div>

  <script>
    // 자동 슬라이드 기능
    let slideIndex = 0;
    const slides = document.getElementsByClassName('slide');
    const slideCount = slides.length;

    function showSlides() {
      for (let i = 0; i < slideCount; i++) {
        slides[i].style.display = 'none';
      }

      slideIndex++;
      if (slideIndex > slideCount) {
        slideIndex = 1;
      }

      slides[slideIndex - 1].style.display = 'block';
      setTimeout(showSlides, 3000); // 3초마다 슬라이드 전환
    }

    showSlides();
  </script>
</body>

</html>