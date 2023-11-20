<?php
$mysqlServer = "127.0.0.1"; // MySQL 서버 호스트명
$mysqlUser = "server"; // MySQL 사용자명
$mysqlPass = "jjh3733990!"; // MySQL 비밀번호
$mysqlDb = "test"; // 사용할 데이터베이스 이름

// MySQL 서버에 연결
$conn = new mysqli($mysqlServer, $mysqlUser, $mysqlPass, $mysqlDb);

// 연결 확인
if ($conn->connect_error) {
  die("MySQL 연결 실패: " . $conn->connect_error);
}

// 연결 성공 시, 여기에 작업을 수행할 수 있습니다.

// 연결 종료
$conn->set_charset("utf8");
?>