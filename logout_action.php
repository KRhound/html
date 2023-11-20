<script>
    // 브라우저 캐시를 제거하는 코드
    window.onload = function () {
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    };
</script>
<?php
session_start();
session_unset();
session_destroy();
header("Location: index.php");
exit;
?>