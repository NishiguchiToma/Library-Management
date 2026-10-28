<?php require 'menu.php'; ?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>お気に入り | 図書館管理システム</title>

    <link rel="stylesheet" href="css/style.css">
</head>

<body>

    <!-- ヘッダー -->
    <header class="header">
        <h1>図書館管理システム</h1>

        <!-- スマホ用メニューボタン -->
        <button class="menu-button" id="menuButton"
                aria-label="メニューを開く"
                aria-expanded="false">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <!-- ナビゲーションメニュー -->
        <nav class="navigation" id="navigation">
            <a href="#">貸出確認</a>
            <a href="favorites.html" class="active">お気に入り</a>
            <a href="#">期限切れ</a>
            <a href="#">本登録</a>
            <a href="#">履歴</a>
        </nav>
    </header>

    <!-- メインコンテンツ -->
    <main class="main">

        <h2>お気に入り</h2>

        <p class="description">
            お気に入りに登録した本の一覧です。
        </p>

        <!-- お気に入り一覧 -->
        <section class="favorite-list" id="favoriteList">

            <label class="book">
                <input type="checkbox" name="favorite" value="1">
                <span>トマト農家</span>
            </label>

            <label class="book">
                <input type="checkbox" name="favorite" value="2">
                <span>野鳥図鑑</span>
            </label>

            <label class="book">
                <input type="checkbox" name="favorite" value="3">
                <span>酒ができるまで</span>
            </label>

        </section>

        <!-- 削除ボタン -->
        <div class="button-area">
            <button type="button" id="deleteButton" class="delete-button">
                削除
            </button>
        </div>

        <!-- メッセージ表示 -->
        <p id="message" role="status" aria-live="polite"></p>

    </main>

    <script src="js/script.js"></script>

</body>
</html>