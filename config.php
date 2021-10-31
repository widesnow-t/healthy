<?php

//接続に必要な情報を定数として定義
define('DSN', 'mysql:host=mysql154.phy.lolipop.lan;dbname=LAA1325655-healthy1db;charset=utf8');
define('USER', 'LAA1325655');
define('PASSWORD', 'manbo1988');

//エラーメッセージを定数として定義
define('MSG_MEAS_DATE_REQUIRED', '検温日が入力されていません');
define('MSG_BODY_TEMP_REQUIRED', '体温が入力されてません');
define('MSG_MEAS_DATE_SAME', '入力された検温日のデータは既に存在します');