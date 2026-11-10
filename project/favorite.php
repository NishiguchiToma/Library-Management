<?php require 'menu.php'; ?>
<!DOCTYPE html>

<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>お気に入り | 図書管理システム</title>
    <link rel="stylesheet" href="css/menu.css">
    <!-- <link rel="stylesheet" href="css/favorite.css"> -->
</head>
<body>
    
<!-- 共通ヘッダー -->


<!-- お気に入り画面 -->
<main class="favorite-main">

    <h2>お気に入り</h2>

    <p class="favorite-title">本のタイトル</p>

    <div class="favorite-list" id="favoriteList">

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

    </div>

    <div class="delete-area">
        <button type="button" id="deleteButton">削除</button>
    </div>

    <p id="message"></p>

</main>

<script src="js/favorite.js"></script>

</body>
</html>
