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
        <form>
            <h1 class="h3 mb-3 fw-normal">ログインする</h1>
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