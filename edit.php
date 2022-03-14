<?php
// ログイン状態の確認のために読み込む
include 'lib/secure.php';
//DBに接続するために読み込む
include 'lib/connect.php';

include 'lib/queryArticle.php';
include 'lib/article.php';

$title = "";        // タイトル
$body = "";         // 本文
$id = "";
$title_alert = "";  // タイトルのエラー文言
$body_alert = "";   // 本文のエラー文言

if (isset($_GET['id'])){
    $queryArticle = new QueryArticle();
    $article = $queryArticle->find($_GET['id']);

    if ($article){
        // 編集する記事データが存在したとき、フォームに埋め込む
        $id = $article->getId();;
        $title = $article->getTitle();
        $body = $article->getBody();
    } else {
        // 編集する記事データが存在しないとき
        header('Location: backend.php');
        exit;
    }

} else if (!empty($_POST['id']) && !empty($_POST['title']) && !empty($_POST['body'])) {
    // id, titleとbodyがPOSTメソッドで送信されたとき
    $title = $_POST['title'];
    $body = $_POST['body'];

    $queryArticle = new QueryArticle();
    $article = $queryArticle->find($_POST['id']);
    if ($article){
        //記事データが存在していれば、タイトルと本文を変更して上書き保存
        $article->setTitle($title);
        $article->setBody($body);
        $article->save();
    }
    header('Location: backend.php');
    exit;
} else if (!empty($_POST)){
    //POSTメソッドで送信されたが、titleかbodyが足りないとき
    if(!empty($_POST["id"])){
        $id = $_POST['id'];
    } else {
        // 編集する記事IDがセットされていなければ、backend.phpへ戻る
        header('Location: backend.php');
        exit;
    }

    // 存在するほうは変数へ、ない場合空文字にしてフォームのvalueに設定する
    if(!empty($_POST['title'])){
        $title = $_POST['title'];
    } else {
        $title_alert = "タイトルを入力してください。";
    }

    if(!empty($_POST['body'])){
        $body = $_POST['body'];
    } else {
        $body_alert = "本文を入力してください。";
    }
}

?>

<!doctype html>
<html lang="ja">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Blog Backend</title>

    <!-- Bootstrap core CSS -->
    <link href="./css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            padding-top: 5rem;
        }

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

        .bg-red {
            background-color: #008080 !important;
        }
    </style>

    <!-- Custom styles for this template -->
    <link href="./css/blog.css" rel="stylesheet">
</head>

<body>

    <!-- ナビの呼び出し -->
    <?php include('lib/nav.php'); ?>

    <main class="container">
        <div class="row">
            <div class="col-md-12">

                <h1>記事の編集</h1>

                <form action="edit.php" method="post">
                    <input type="hidden" name="id" value="<?php echo $id ?>">
                    <div class="mb-3">
                        <label class="form-label">タイトル</label>
                        <?php echo !empty($title_alert) ? '<div class="alert alert-danger">' . $title_alert . '</div>' : '' ?>
                        <input type="text" name="title" value="<?php echo $title; ?>" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">本文</label>
                        <?php echo !empty($body_alert) ? '<div class="alert alert-danger">' . $body_alert . '</div>' : '' ?>
                        <textarea name="body" class="form-control" rows="10"><?php echo $body; ?></textarea>
                    </div>
                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary">投稿する</button>
                    </div>
                </form>

            </div>

        </div><!-- /.row -->

    </main><!-- /.container -->

</body>

</html>