<?php

require_once __DIR__ . '/functions.php';

$id = filter_input(INPUT_GET, 'id');

//データベースに接続
$dbh = connectDb();

$sql = <<<EOM
DELETE
FROM
    body_temperatures
WHERE
    id = :id
EOM;

$stmt = $dbh->prepare($sql);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();

header('Location: index.php');
exit;