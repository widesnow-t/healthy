<?php
require_once __DIR__ . '/config.php';
//接続処理を行う関数
function connectDb()
{
    try {
        return new PDO(
            DSN,
            USER,
            PASSWORD,
            [PDO::ATTR_ERRMODE =>
            PDO::ERRMODE_EXCEPTION]
        );
    } catch (PDOException $e) {
        echo $e->getMessage();
        exit;
    }
}

//エスケープ処理を行う関数
function h($str)
{
    //ENT_QUOTES: シングルオートとダブルクオートを共に変換する
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}
//検温データに関連する年月の算出
function calcBtRelatedYm($ym)
{
    //先月と翌月を算出
    $last_month = date('Ym', strtotime('first day of previous month' . substr_replace($ym, '-', 4, 0)));
    $next_month = date('Ym', strtotime('first day of next month' . substr_replace($ym, '-', 4, 0)));

    //表示する年月算出(YYYY年mm月)
    $disp_ym = date('Y年m月', strtotime($ym . '01'));

    return [$last_month, $next_month, $disp_ym];
}

//該当年月を使用して検温データを抽出
function findBtbyYm($ym)
{
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

    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':ym', $ym, PDO::PARAM_STR);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function findBtById($id)
{
    //データベースに接続
    $dbh = connectDb();

    $sql = <<<EOM
    SELECT
        *
    FROM
        body_temperatures
    WHERE
        id = :id
    EOM;

    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':id', $id,  PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function validateRequired($measurement_date, $body_temperature)
{
    $errors = [];

    if ($measurement_date == '') {
        $errors[] = MSG_MEAS_DATE_REQUIRED;
    }
    if ($body_temperature == '') {
        $errors[] = MSG_BODY_TEMP_REQUIRED;
    }

    return $errors;
}

function validateSameMeasDate($measurement_date)
{
    $dbh = connectDb();

    $sql =<<<EOM
    SELECT
        *
    FROM
        body_temperatures
    WHERE
        measurement_date = :measurement_date
    EOM;

    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':measurement_date', $measurement_date, PDO::PARAM_STR);
    $stmt->execute();
    $bt = $stmt->fetch(PDO::FETCH_ASSOC);

    $errors = [];
    if ($bt) {
        $errors[] = MSG_MEAS_DATE_SAME;
    }

    return $errors;
}

function insertBt ($measurement_date, $body_temperature, $memo)
{
    $dbh = connectDb();

    $sql = <<<EOM
    INSERT INTO
        body_temperatures
    (
        measurement_date,
        body_temperature,
        memo
    )
    VALUES
    (
        :measurement_date,
        :body_temperature,
        :memo
    )
    EOM;

    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':measurement_date', $measurement_date, PDO::PARAM_STR);
    $stmt->bindParam(':body_temperature', $body_temperature, PDO::PARAM_STR);
    $stmt->bindParam(':memo', $memo, PDO::PARAM_STR);
    $stmt->execute();
}

function updateBt($id, $measurement_date, $body_temperature, $memo)
{
    $dbh = connectDb();

    $sql = <<<EOM
    UPDATE
        body_temperatures
    SET
        measurement_date = :measurement_date,
        body_temperature = :body_temperature,
        memo = :memo
    WHERE
        id = :id;
    EOM;

    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':measurement_date', $measurement_date, PDO::PARAM_STR);
    $stmt->bindParam(':body_temperature', $body_temperature, PDO::PARAM_STR);
    $stmt->bindParam(':memo', $memo, PDO::PARAM_STR);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
}

function deleteBt($id)
{
    $dbh = connectDb();

    $sql = <<<EOM
    DELETE FROM
        body_temperatures
    WHERE
        id = :id;
    EOM;

    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
}
function formatBtToJson($bts)
{
    //$array_days: 検温日の日の部分を配列で生成(['1日', '2日'････])
    //&array_bts: 体温を配列で生成([36.3, 36.8, 37.5････])
    $array_days =[];
    $array_bts = [];

    foreach ($bts as $bt) {
        //検温日の日の部分(8桁のうち後ろ2桁)を抽出｡ltirmで0を削除する(例08->8)
        $array_days[] = ltrim(substr($bt['measurement_date'], -2), '0') . '日';
        //グラフに表示するデータとクリックした時に表示するurlの設定
        $array_bts[] = [
            //体温を文字列からfloat型に変換
            //fetchALL(PDO::FETCH_ASSOC)でデータを取得すると､36､8のような文字列になってしまうので変換する
            'y' => (float)$bt['body_temperature'],
            'url' => 'show.php?id=' . $bt['id']
        ];
    }

    //Hightchartsに渡すデータはJSON形式でなければいけないので､連想配列をJSONに変換
    return [json_encode($array_days), json_encode($array_bts)];
}