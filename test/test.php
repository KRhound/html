<?php
include("./dbConnection.php");

//SQL 인젝션 방어

$filtered = array(

    'id' => mysqli_real_escape_string($conn, $_POST['username']),

    'email' => mysqli_real_escape_string($conn, $_POST['email']),

    'nickName' => mysqli_real_escape_string($conn, $_POST['nickname']),

    'pw' => mysqli_real_escape_string($conn, $_POST['password'])

);

 

// $_POST 변수들이 설정되었는지 확인

if (isset($_POST['username'])) {

    $filtered['id'] = mysqli_real_escape_string($conn, $_POST['username']);

} else {

    echo "Username is not set.<br>";

}

 

if (isset($_POST['email'])) {

    $filtered['email'] = mysqli_real_escape_string($conn, $_POST['email']);

} else {

    echo "Email is not set.<br>";

}

 

if (isset($_POST['nickname'])) {

    $filtered['nickName'] = mysqli_real_escape_string($conn, $_POST['nickname']);

} else {

    echo "Nickname is not set.<br>";

}

 

if (isset($_POST['password'])) {

    $filtered['pw'] = mysqli_real_escape_string($conn, $_POST['password']);

} else {

    echo "Password is not set.<br>";

}

 

print_r($filtered['id']);

print_r($filtered['email']);

print_r($filtered['nickName']);

print_r($filtered['pw']);

 

function goSignUpPage($alert) {

  echo $alert.'<br>';

  echo "<a href='register.html'>회원가입 폼으로 이동</a>";

  return;

}

 

// 유효성 검사

// 이메일 검사

if(!filter_var($filtered['email'], FILTER_VALIDATE_EMAIL)) {

    goSignUpPage('올바른 이메일이 아닙니다.');

    exit;

}

 

// 한글로 구성되어 있는지 정규식 검사

$nickNameRegPattern = '/^[가-힣]{1,}$/';

  if (!preg_match($filtered['nickNameRegPattern'], $filtered['nickName'])) {

  goSignUpPage('닉네임은 한글로만 입력해 주세요.');

  exit;

}

 

// 비밀번호 검사

if ($filtered['pw'] == null || $filtered['pw'] == '') {

  goSignUpPage('비밀번호를 입력해 주세요');

  exit;

}

 

$filtered['pw'] = sha1('php200'.$filtered['pw']);



// 이메일 중복 검사

$isEmailCheck = false;

 

$sql = "SELECT email FROM member WHERE email = '{$filtered['email']}'";

$result = $conn->query($sql);

 

if($result) {

  $count = $result->num_rows;

  if($count == 0) {

    $isEmailCheck = true;

  } else {

    goSignUpPage('이미 존재하는 이메일 입니다.');

    exit;

  }

} else {

    goSignUpPage('에러발생 : 관리자 문의 요망');

    exit;

}

 

// 닉네임 중복 검사

  $isNickNameCheck = false;

 

  $sql = "SELECT nickName FROM member WHERE nickname = '{$filtered['nickName']}'";

  $result = $conn->query($sql);

 

  if($result) {

    $count = $result->num_rows;

    if($count == 0) {

      $isNickNameCheck = true;

    } else {

      goSignUpPage('이미 존재하는 닉네임 입니다.');

      exit;

    }

  } else {

      goSignUpPage('에러발생 : 관리자 문의 요망.');

      exit;

    }

 

    if($isEmailCheck == true && $isNickNameCheck == true) {

      $regDate = time();

      $sql = "INSERT INTO member(id, password, nickname, email)";

      $sql .= "VALUES('{$filtered['id']}', '{$filtered['pw']}', '{$filtered['nickName']}', '{$filtered['email']})";

      $result = $conn->query($sql);

 

      if($result) {

        $_SESSION['id'] = $id;

        $_SESSION['nickname'] = $nickName;

        Header("Location:../index.php");

      } else {

        goSignUpPage('회원가입 실패 - 관리자에게 문의');

        exit;

      }

    } else {

      goSignUpPage('이메일 또는 닉네임이 중복값입니다.');

      exit;

    }

?>