<?php
    session_start();

    // Hapus semua session
    $_SESSION = [];
    session_unset();
    session_destroy();
    ?>

    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <title>Logout</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body>
        <script>
            alert("Berhasil logout!");
            window.location.href = "login.php";
        </script>
    </body>
</html>
