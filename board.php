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
    <title>자유게시판</title>
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

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .container h1 {
            font-size: 28px;
            margin-bottom: 20px;
        }

        .container table {
            width: 100%;
            border-collapse: separate;
            border-radius: 10px;
            overflow: hidden;
        }

        .container table th,
        .container table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
            /* 가운데 정렬 추가 */
        }

        .container table th:first-child,
        .container table td:first-child {
            width: 50px;
        }

        .container table th:nth-child(2),
        .container table td:nth-child(2) {
            width: 700px;
        }

        .container table th:nth-child(4),
        .container table td:nth-child(4),
        .container table th:nth-child(5),
        .container table td:nth-child(5) {
            width: 80px;
        }

        .search-container {
            float: right;
            margin-bottom: 10px;
        }

        .search-container input[type="text"] {
            padding: 6px;
            font-size: 14px;
            border: none;
            border-radius: 20px;
        }

        .search-container button {
            padding: 6px 10px;
            font-size: 14px;
            border: none;
            background-color: #555;
            color: #fff;
            border-radius: 20px;
            cursor: pointer;
        }

        .search-container button:hover {
            background-color: #45a049;
        }

        /* 추가 스타일 */
        .container table {
            border-spacing: 0;
            border-top: 2px solid #ddd;
            border-bottom: 2px solid #ddd;
        }

        .container table th,
        .container table td {
            border: 1px solid #ddd;
            border-top: none;
            border-bottom: none;
        }

        .container table th:first-child,
        .container table td:first-child {
            border-top-left-radius: 10px;
            border-bottom-left-radius: 10px;
        }

        .container table th:last-child,
        .container table td:last-child {
            border-top-right-radius: 10px;
            border-bottom-right-radius: 10px;
        }

        /* 정렬 스타일 적용 */
        .sort-container select {
            padding: 6px;
            font-size: 14px;
            border: none;
            border-radius: 20px;
        }

        .sort-container button {
            padding: 6px 10px;
            font-size: 14px;
            border: none;
            background-color: #555;
            color: #fff;
            border-radius: 20px;
            cursor: pointer;
        }

        .sort-container select:focus {
            outline: none;
            box-shadow: 0 0 0 2px #45a049;
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
        <h1>자유게시판</h1>

        <div class="search-container">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="GET">
                <input type="text" name="keyword" placeholder="검색어 입력">
                <button type="submit">검색</button>
            </form>
        </div>

        <div class="sort-container">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="GET">
                <select name="sort_by">
                    <option value="id">순번순</option>
                    <option value="views">조회순</option>
                    <option value="likes">추천순</option>
                </select>

                <select name="sort_order">
                    <option value="asc">오름차순</option>
                    <option value="desc">내림차순</option>
                </select>

                <button type="submit">정렬</button>
            </form>
        </div>

        <table>
            <tr>
                <th>번호</th>
                <th>제목</th>
                <th>작성일</th>
                <th>조회수</th>
                <th>추천수</th>
            </tr>
            <?php
            //데이터베이스 연결
            include("./dbConnection.php");

            // 검색어 처리
            $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';

            $sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'id';
            $sort_order = isset($_GET['sort_order']) ? $_GET['sort_order'] : 'asc';

            // 공지사항 조회 쿼리
            // 공지사항 조회 쿼리
            $sql = "SELECT * FROM posts WHERE board_type = 'board' AND status = 'active'";
            if (!empty($keyword)) {
                $sql .= " AND title LIKE '%" . $conn->real_escape_string($keyword) . "%'";
            }
            $sql .= " ORDER BY $sort_by $sort_order";
            $result = $conn->query($sql);

            if ($result) {
                if ($result->num_rows > 0) {
                    // 조회된 공지사항을 테이블에 추가
                    $counter = 1;
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($counter) . "</td>";
                        echo '<td><a href="view.php?id=' . htmlspecialchars($row["id"]) . '">' . htmlspecialchars($row["title"]) . "</a></td>";
                        echo "<td>" . htmlspecialchars($row["created_at"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["views"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["likes"]) . "</td>";
                        echo "</tr>";
                        $counter++;
                    }
                } else {
                    echo "<tr><td colspan='5'>자유게시글이 없습니다.</td></tr>";
                }
            } else {
                echo "<tr><td colspan='5'>자유게시글 조회에 실패했습니다.</td></tr>";
            }

            // 데이터베이스 연결 종료
            $conn->close();
            ?>
        </table>
        <div class="search-container">
            <button onclick="location.href='write.php'">글 작성</button>
        </div>
    </div>
</body>

</html>