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

// POST 데이터 받아오기 및 XSS 방지 처리
$usernameInput = htmlspecialchars($_POST['username']);
$passwordInput = htmlspecialchars($_POST['password']);

// MySQL 서버에 연결
include("./dbConnection.php");

// 사용자명과 비밀번호를 사용하여 데이터베이스에서 회원 정보 조회
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
$stmt->bind_param("ss", $usernameInput, $passwordInput);
$stmt->execute();
$result = $stmt->get_result();


if ($result->num_rows == 1) {
    // 로그인 성공
    $row = $result->fetch_assoc();
    $_SESSION['username'] = $row['username'];
    $_SESSION['nickname'] = $row['nickname'];
    $_SESSION['authority'] = $row['authority'];
    $_SESSION['verify'] = $row['verify'];

    // 회원 인증 확인
    if ($_SESSION['verify'] === 0) {
        echo '<script>alert("회원 인증 필수!")</script>';
        header("Location: re-send_mail.php");
    }

    // 로그인 성공 시 리다이렉트할 페이지로 이동
    header("Location: index.php");
} else {
    // 로그인 실패
    ?>
    <script>
        alert("로그인에 실패했습니다. 사용자명 또는 비밀번호를 확인해주세요.")
    </script>
    <?php
}

// 연결 종료
$stmt->close();
$conn->close();
?>