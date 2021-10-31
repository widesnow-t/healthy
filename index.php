<?php
require_once __DIR__ . '/functions.php';

//パラメータの取得
$ym = filter_input(INPUT_GET, 'ym');

//システム日付から対象年月を算出
//$ym = date('Ym');
//パラメータ未設定(初期表示)の場合はシステム日付から対象年月日を算出
if (empty($ym)) {
    $ym = date('Ym');
}

//先月と翌月を算出
//$last_month = date('Ym', strtotime('first day of previous month' . substr_replace($ym, '-', 4, 0)));
//$last_month = date('Ym', strtotime('first day of previous month' . substr_replace($ym, '-', 4, 0)));
//$next_month = date('Ym', strtotime('first day of next month' . substr_replace($ym, '-', 4, 0)));
//$next_month = date('Ym', strtotime('first day of next month' . substr_replace($ym, '-', 4, 0)));

//表示する年月日算出(YYYY年mm月)
//$disp_ym = date('Y年m月', strtotime($ym . '01'));
//検温データに関連する年月の算出
[$last_month, $next_month, $disp_ym] = calcBtRelatedYm($ym);

//データベースに接続
//$dbh = connectDb();

//SQLの作成
//$sql = <<<EOM
//SELECT
//    *
//FROM
//    body_temperatures
//WHERE
//    date_format(measurement_date, '%Y%m') = :ym
//ORDER BY
//    measurement_date
//EOM;

//準備
//$stmt = $dbh->prepare($sql);

//パラメータのバインドの結びつけ
//$stmt->bindParam(':ym', $ym, PDO::PARAM_STR);

//実行
//$stmt->execute();

//取得したデータを変数に代入
//$bts = $stmt->fetchAll(PDO::FETCH_ASSOC);
//var_dump($bts);
//該当年月を使用して検温データ抽出
$bts = findBtbyYm($ym);

//Highcharts(Javascript)に渡す値の作成
//$array_days: 検温日の日の部分を配列で生成(｢'1日', '2日',････｢)
//array_bts: 体温を配列で生成([36.3, 36.8, ･･･])
//$array_days = [];
//$array_bts = [];

//foreach ($bts as $bt) {
    //検温日の日の部分(8桁のうち後ろ2桁)を抽出｡ltirmで0を削除する(例 08->8)
    //$array_days[] = ltrim(substr($bt['measurement_date'], -2), '0') . '日';
    //$array_days[] = ltrim(substr($bt['measurement_date'], -2), '0') . '日';
//グラフに表示するデータの設定
//[変更]グラフに表示するデータとクリックしたときに表示するURLの設定
    //$array_bts[] = [
        //体温を文字列からfloat型に変換
        //fetchALL8PDO::FETCH_ASSOC)でデ-タを取得すると､'36､8'のように文字列になってしまうので変換する
        //(float)$bt['body_temperature']
        //'y' => (float)$bt['body_temperature'],
        //'url' => 'show.php?id=' . $bt['id']
    //];
//}
                            //Highchartsに渡すデータはJSON形式でなければいけないので､連想配列をJSONに変換
                            // https://www.php.net/manual/ja/function.json-encode.php
                            //$json_days = json_encode($array_days);
                            //$json_bts = json_encode($array_bts);
                            //$json_days = json_encode($array_days);
                            //$json_bts = json_encode($array_bts);
                            //Highcharts(JavaScript)に渡す値の作成
                            [$json_days, $json_bts] = formatBtToJson($bts);
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
        <div id="container"></div>
        <a href="new.php"><i class="fas fa-plus-circle"></i></a>
    </div>
    <!--以下がHighcharts用のコード-->
    <script language="JavaScript">
        document.addEventListener('DOMContentLoaded', function(){
            const chart = Highcharts.chart('container', {
                //chart: {
                    //type: 'bar'
                //},
                title: {
                    //text: 'Fruit Consumption'
                    text: ''
                },
                xAxis: {
                    //categories: ['Apples', 'Bananas', 'Oranges']
                    //categories: ['1日', '3日', '5日']
                    categories: <?= $json_days ?>
                },
                yAxis: {
                    title: {
                        //text: 'Fruit eaten'
                        text: '体温(℃)'
                    }
                },
                tooltip: {
                    valueSuffix: '℃'
                },
                plotOptions: {
                    series: {
                        cursor: 'pointer',
                        point: {
                            events: {
                                click: function() {
                                    location.href = this.options.url;
                                }
                            }
                        }
                    }
                },
                series: [{
                    //name: 'Jane',
                    //data: [1, 0, 4]
                //}, {
                    //name: 'John',
                    //data: [5, 7, 3]
                    name: '体温',
                    //data: [36.7, 36.2, 36.5]
                    data: <?= $json_bts ?>,
                    color: '#49d3e9'
                }],
                credits: {
                    enabled: false
                }
            });
        });
    </script>
</body>

</html>