<!DOCTYPE html>
<html>
<head>
    <title>동영상 재생</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        #videoContainer {
            width: 100vw;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        video {
            width: 80vw;
            height: 60vh;
            border: 1px solid #ccc;
        }
    </style>
</head>
<body>
    <div id="videoContainer">
        <?php
            // 동영상 파일 경로
            $videoPath = "./fuzz-test.mp4";
        ?>

        <video controls>
            <source src="<?php echo $videoPath; ?>" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    </div>

    <script>
        // 브라우저 화면 크기 변경 시 동영상 화면 크기 업데이트
        window.addEventListener('resize', () => {
            const videoContainer = document.getElementById('videoContainer');
            const video = document.querySelector('video');
            const containerWidth = videoContainer.offsetWidth;
            const containerHeight = videoContainer.offsetHeight;
            const containerAspectRatio = containerWidth / containerHeight;

            video.style.width = 'auto';
            video.style.height = 'auto';

            const videoWidth = video.offsetWidth;
            const videoHeight = video.offsetHeight;
            const videoAspectRatio = videoWidth / videoHeight;

            if (videoAspectRatio > containerAspectRatio) {
                video.style.width = '80vw';
            } else {
                video.style.height = '60vh';
            }
        });
    </script>
</body>
</html>
