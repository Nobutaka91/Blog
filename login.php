<?php 
    //includeは指定ファイルを読み込む関数
    include 'lib/connect.php';

    // エラーメッセージ
    $err = null;

    /*
    POSTメソッドでnameとpasswordが送信されてきた場合、
    ユーザー名からユーザーを取得する処理に移る。
    */
    if (isset($_POST['name']) && isset($_POST['password'])){
        $db = new connect();

        //実行したいSQL
        $select = "SELECT * FROM users WHERE name=:name";
        //第2引数で土のパラメーターにどの変数を割り当てるかを決める
        $stmt = $db->query($select, array(':name'=> $_POST['name']));
        // レコード1件を連想配列として取得する
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result && password_verify($_POST['password'], $result['password'])){
            //結果が存在し、パスワードも正しい場合
            session_start();
            $_SESSION['id'] = $result['id'];
            header('Location: backend.php');
        } else {
            $err = "ログインできませんでした。";
        }
    }
?>


<!doctype html>
<html lang="ja">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Blog</title>

    <!-- Bootstrap core CSS -->
    <link href="./css/bootstrap.min.css" rel="stylesheet">

    <style>
        .bd-placeholder-img {
            font-size: 1.125rem;
            text-anchor: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            user-select: none;
        }

        @media (min-width: 768px) {
            .bd-placeholder-img-lg {
                font-size: 3.5rem;
            }
        }
    </style>

    <!-- Custom styles for this template -->
    <link href="./css/signin.css" rel="stylesheet">
</head>

<body class="text-center">

    <main class="form-signin">
        <form action="login.php" method="post">
            <h1 class="h3 mb-3 fw-normal">ログインする</h1>
        <?php 
            if (!is_null($err)){
                echo '<div class="alert alert-danger">'.$err.'</div>';
            }
        ?>
            <label class="visually-hidden">ユーザ名</label>
            <input type="text" name="name" class="form-control" placeholder="ユーザ名" required autofocus>
            <label class="visually-hidden">パスワード</label>
            <input type="password" name="password" class="form-control" placeholder="パスワード" required>
            <button class="w-100 btn btn-lg btn-primary" type="submit">ログインする</button>
            <p class="mt-5 mb-3 text-muted">&copy; 2022</p>
        </form>
    </main>

</body>

</html>