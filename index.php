<?php
require_once __DIR__ . '/functions.php';

date_default_timezone_set('Asia/Tokyo');

//パラメータの取得
$ym = filter_input(INPUT_GET, 'ym');

//システム日付から対象年月を算出
//$ym = date('Ym');
//パラメータ未設定(初期表示)の場合はシステム日付から対象年月日を算出
if (empty($ym)) {
    $ym = date('Ym');
}

//先月と翌月を算出
$last_month = date('Ym', strtotime('first day of previous month' . substr_replace($ym, '-', 4, 0)));
//$last_month = date('Ym', strtotime('first day of previous month' . substr_replace($ym, '-', 4, 0)));
$next_month = date('Ym', strtotime('first day of next month' . substr_replace($ym, '-', 4, 0)));
//$next_month = date('Ym', strtotime('first day of next month' . substr_replace($ym, '-', 4, 0)));

//表示する年月日算出(YYYY年mm月)
$disp_ym = date('Y年m月', strtotime($ym . '01'));

//データベースに接続
$dbh = connectDb();

//SQLの作成
$sql = <<<EOM
SELECT
    *
FROM
    body_temperatures
WHERE
    date_format(measurement_date, '%Y%m') = :ym
ORDER BY
    measurement_date
EOM;

//準備
$stmt = $dbh->prepare($sql);

//パラメータのバインドの結びつけ
$stmt->bindParam(':ym', $ym, PDO::PARAM_STR);

//実行
$stmt->execute();

//取得したデータを変数に代入
$bts = $stmt->fetchAll(PDO::FETCH_ASSOC);
//var_dump($bts);
?>
<!DOCTYPE html>
<html lang="ja">

<!--<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Healthy</title>-->
<!-- CSS 
    <link rel="stylesheet" href="https://unpkg.com/ress@3.0.0/dist/ress.min.css">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Aclonica&family=M+PLUS+1p&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/bd44591dc3.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="style.css">
</head>-->
<?php include_once __DIR__ . '/_head.html' ?>

<body>
    <!-- <header>
            <h1 class="title"><a href="index.php">Healthy</a></h1>
        </header>-->
    <?php include_once __DIR__ . '/_header.html' ?>
    <div class="wrapper">
        <section class="search-ym-area">
            <a href="index.php?ym=<?= h($last_month) ?>"><i class="fas fa-angle-left"></i></a>
            <span class="show-ym"><?= h($disp_ym) ?></span>
            <a href="index.php?ym=<?= h($next_month) ?>"><i class="fas fa-angle-right"></i></a>
        </section>
        <table class="bt-list">
            <thead>
                <tr>
                    <th>検温日</th>
                    <th>体温</th>
                    <th>メモ</th>
                </tr>
            </thead>
            <tbody>
                <!--<tr>
                        <td><a href="">YYYY-MM-01</a></td>
                        <td>XX.1 ℃</td>
                        <td>テストメモ1</td>
                    </tr>
                    <tr>
                        <td><a href="">YYYY-MM-02</a></td>
                        <td>XX.2 ℃</td>
                        <td>テストメモ2</td>
                    </tr>
                    <tr>
                        <td><a href="">YYYY-MM-03</a></td>
                        <td>XX.3 ℃</td>
                        <td>テストメモ3</td>
                    </tr>-->
                <?php foreach ($bts as $bt) : ?>
                    <tr>
                        <td><a href="show.php?id=<?= h($bt['id']) ?>"><?= h($bt['measurement_date']) ?></a></td>
                        <td><?= h($bt['body_temperature']) ?> ℃</td>
                        <td><?= h($bt['memo']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <a href="new.php"><i class="fas fa-plus-circle"></i></a>
    </div>
</body>

</html>