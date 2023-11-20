<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// POST 데이터 받아오기 및 XSS 방지 처리
$username = htmlspecialchars($_POST['username']);
$nickname = htmlspecialchars($_POST['nickname']);
$password = htmlspecialchars($_POST['password']);
$email = htmlspecialchars($_POST['email']);
$gender = htmlspecialchars($_POST['gender']);

// 생성한 인증 토큰
$token = generateToken();

// 사용자 권한, 이메일 인증 여부
$authority = 1;
$verify = 0;

// MySQL 서버에 연결
include("./dbConnection.php");

// 데이터 삽입 쿼리 작성 (Prepared Statement 사용)
$sql = "INSERT INTO users(username, nickname, password, email, gender, token, authority, verify)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssssii", $username, $nickname, $password, $email, $gender, $token, $authority, $verify);

// 쿼리 실행
if ($stmt->execute()) {
    // 회원가입이 성공적으로 이루어졌을 경우, 이메일 전송
    $emailSent = sendVerificationEmail($email, $token);
    if ($emailSent) {
        echo '<script>alert("회원가입이 완료되었습니다. 이메일을 확인하여 인증을 완료해주세요.")</script>';
        ob_flush();
        flush();
        echo '<script>window.location.href = "login.php";</script>';
        exit;
    } else {
        echo '<script>alert("회원가입이 완료되었습니다. 이메일 전송에 실패했습니다. 관리자에게 문의하세요.")</script>';
        ob_flush();
        flush();
        echo '<script>window.location.href = "re-send_mail.php";</script>';
        exit;
    }
} else {
    echo "오류: " . $stmt->error;
}

// 연결 종료
$stmt->close();
$conn->close();

/**
 * 인증 토큰 생성 함수
 */
function generateToken()
{
    // 토큰 생성 로직 작성 (예: 랜덤한 문자열 생성)
    // 적절한 방법으로 고유한 토큰을 생성하는 로직을 구현해야 합니다.
    // 예시로 현재 시간을 이용한 임시 방법으로 작성되었습니다.
    $timestamp = time();
    $token = md5($timestamp);
    return $token;
}

/**
 * 이메일 전송 함수
 */
function sendVerificationEmail($email, $token)
{
    // 이메일 전송 로직 작성
    // 실제로 이메일을 전송하는 코드를 작성해야 합니다.
    // 예시로 현재는 단순히 텍스트로 출력하는 방식으로 작성되었습니다.
    include_once('./mailer.lib.php');
    $verificationLink = "http://117.16.11.247:9777/verify.php?token=" . $token;
    $message = "인증을 완료하려면 아래 링크를 클릭하세요:\n\n" . $verificationLink;
    $subject = "회원가입 인증 이메일";

    // 메일 전송
    // mailer("보내는 사람 이름", "보내는 사람 메일주소", "받는 사람 메일주소", "제목", "내용", "1");
    $emailSent = mailer("admin", "wjdwlgns394@naver.com", $email, $subject, $message, 1);

    return $emailSent;
}
?>