<?php
require_once __DIR__ . '/functions.php';

$dbh = connectDb();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Healthy</title>
    <!-- CSS -->
    <link rel="stylesheet" href="https://unpkg.com/ress@3.0.0/dist/ress.min.css">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Aclonica&family=M+PLUS+1p&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/bd44591dc3.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1 class="title"><a href="index.php">Healthy</a></h1>
    </header>

    <div class="wrapper">
        <table class="bt-list">
            <thead>
                <tr>
                    <th>検温日</th>
                    <th>体温</th>
                    <th>メモ</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><a href="">YYYY-MM-01</a></td>
                    <td>XX.1℃</td>
                    <td>テストメモ</td>
                </tr>
                <tr>
                    <td><a href="">YYYY-MM-02</a></td>
                    <td>XX.2℃</td>
                    <td>テストメモ</td>
                </tr>
                <tr>
                    <td><a href="">YYYY-MM-03</a></td>
                    <td>XX.3℃</td>
                    <td>テストメモ</td>
                </tr>
            </tbody>
        </table>
        <a href="new.php"><i class="fas fa-plus-circle"></i></a>
    </div>
</body>

</html>