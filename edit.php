<?php
require_once __DIR__ . '/functions.php';

//初期化
$measurement_date = '';
$body_temperature = '';
$memo = '';
//エラーチェック用の配列
$errors = [];

$id = filter_input(INPUT_GET, 'id');

//データベースに接続
$dbh = connectDb();

$sql = <<<EOM
SELECT
    *
FROM
    body_temperatures
WHERE
    id= :id
EOM;

$stmt = $dbh->prepare($sql);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$bt = $stmt->fetch(PDO::FETCH_ASSOC);

//更新処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //フォームに入れたデータの受取
    $measurement_date = filter_input(INPUT_POST, 'measurement_date');
    $body_temperature = filter_input(INPUT_POST, 'body_temperature');
    $memo = filter_input(INPUT_POST, 'memo');

    //バリデーション
    if ($measurement_date == '') {
        $errors[] = '検温日が入力されてません';
    }
    if ($body_temperature == '') {
        $errors[] = '体温が入力されてません';
    }

//検温日を変更した場合は､同じ検温日のデータが存在しないかチェック
    if ($measurement_date &&
        $bt['measurement_date'] != $measurement_date) {
        $sql = <<<EOM
        SELECT
            *
        FROM
            body_temperatures
        WHERE
            measurement_date = :measurement_date
        EOM;
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':measurement-date', $measurement_date, PDO::PARAM_STR);
        $stmt->execute();
        $check_bt = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($check_bt) {
        $errors[] = '入力された検温日のデータは既に存在します';
        }
    }
    if (empty($errors)) {
        $sql = <<<EOM
        UPDATE
            body_temperatures
        SET
            measurement_date = :measurement_date,
            body_temperature = :body_temperature,
            memo = :memo
        WHERE
            id = :id
        EOM;

        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':measurement_date', $measurement_date, PDO::PARAM_STR);
        $stmt->bindParam(':body_temperature', $body_temperature, PDO::PARAM_STR);
        $stmt->bindParam(':memo', $memo, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        header('Location: index.php');
        exit;
    }
    $bt['measurement_date'] = $measurement_date;
    $bt['body_temperature'] = $body_temperature;
    $bt['memo'] = $memo;
}
?>
<!DOCTYPE html>
<html lang="ja">

<?php include_once __DIR__ . '/_head.html' ?>

<body>
    <?php include_once __DIR__ . '/_header.html' ?>

    <div class="form-wrapper">
        <div class="form-area">
            <h2 class="sub-title">EDIT</h2>
            <?php if ($errors) : ?>
                <ul class="errors">
                    <?php foreach ($errors as $error) : ?>
                        <li><?= h($error) ?></li>
                        <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            <form action="" method="post">
                <div class="input-area">
                    <label for="measurement_date">検温日</label>
                    <input type="date" id="measurement_date" name="measurement_date" value="<?= h($bt['measurement_date']) ?>">
                    <label for="body_temperature">体温</label>
                    <input type="number" step="0.1" id="body_temperature" name="body_temperature" value="<?= h($bt['body_temperature']) ?>">
                    <label for="memo">メモ</label>
                    <input type="text" id="memo" name="memo" value="<?= h($bt['memo']) ?>">
                </div>
                <div class="btn-area">
                    <input type="submit" class="btn submit-btn" value="UPDATE">
                    <a href="show.php?id=<?= h($bt['id']) ?>" class="btn return-btn">RETURN</a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>