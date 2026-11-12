<?php
session_start();
require 'config/database.php';
require 'menu.php';

// ログイン中のユーザーID
$user_id = $_SESSION['user_id'] ?? '';

// お気に入りの本を取得
$sql = 'SELECT f.favorite_id, b.title
        FROM favorite f
        JOIN book b ON f.book_id = b.book_id
        WHERE f.user_id = :user_id
        ORDER BY f.favorite_id ASC';

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR);
$stmt->execute();

$favorite_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>お気に入り | 図書管理システム</title>

    <!-- CSSの読み込み -->
    <link rel="stylesheet" href="css/menu.css">
    <link rel="stylesheet" href="css/favorite.css">
</head>
<body>

<!-- お気に入り画面 -->
<main class="main">

    <h2>お気に入り</h2>

    <p class="description">本のタイトル</p>

    <div class="favorite-list" id="favoriteList">

        <?php if (empty($favorite_list)): ?>

           <p class="empty-message">お気に入りの本はありません。</p>

        <?php else: ?>

            <?php foreach ($favorite_list as $row): ?>

                <label class="book">
                    <input type="checkbox"
                           name="favorite[]"
                           value="<?php echo htmlspecialchars($row['favorite_id'], ENT_QUOTES, 'UTF-8'); ?>">

                    <span>
                        <?php echo htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8'); ?>
                    </span>
                </label>

            <?php endforeach; ?>

        <?php endif; ?>

    </div>

    <div class="button-area">
        <button type="button" class="delete-button" id="deleteButton">
            削除
        </button>
    </div>

    <p id="message"></p>

</main>

<script src="js/favorite.js"></script>

</body>
</html>