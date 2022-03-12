<?php 
/*
ログインしているかどうかをセッションを使って判断する。
なお、セッションを開始するためには最初にsession_start()を使う必要がある。
*/
session_start();
if (!isset($_SESSION['id'])){
    header('Location: login.php');
}
