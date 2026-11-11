<?php
session_start();
require 'config/database.php';
require 'menu.php';

$b_id = $_GET['id'] ?? '';
$u_id = $_SESSION['use']['id'] ?? '';

$f_user = $_POST['f_u'] ?? '';
$f_book = $_POST['f_b'] ?? '';

// お気に入りボタンが押されたときの処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($f_user) && !empty($f_book)) {
    $check = $pdo->prepare('SELECT * FROM favorite WHERE book_id = ? AND user_id = ?');
    $check->execute([$f_book, $f_user]);

    if ($check->fetch()) {
        $sql2 = 'DELETE FROM favorite WHERE user_id = ? AND book_id = ?';
    } else {
        $sql2 = 'INSERT INTO favorite (user_id, book_id) VALUES (?, ?)';
    }
    $stmt = $pdo->prepare($sql2);
    $stmt->execute([$f_user, $f_book]);
    header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . urlencode($b_id));
    exit;
}

// 本情報の取得
$sql = $pdo->prepare('SELECT * FROM book WHERE book_id = ?');
$sql->execute([$b_id]);
$row = $sql->fetch();

if (!$row) {
    echo "<p style='text-align:center; margin-top:50px; font-weight:bold;'>指定された本は見つかりませんでした。</p>";
    exit;
}

// お気に入り状態のチェック
$is_favorite = false;
$sq = $pdo->prepare('SELECT * FROM favorite WHERE book_id = ? AND user_id = ?');
$sq->execute([$b_id, $u_id]);
$ro = $sq->fetch(PDO::FETCH_ASSOC);
if ($ro) {
    $is_favorite = true; // 登録済みのフラグ
}

$img = base64_encode($row['book_image']);
$c = $row['count'];
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>本の詳細 - 図書館管理システム</title>
    <link rel="stylesheet" href="css/detail.css">
</head>
<body>

    <!-- 全体を囲むグリッドコンテナ -->
    <div class="detail-container">
        
        <!-- 左側：本画像 ＆ 右上に重ねるお気に入りボタン -->
        <div class="detail-left">
            <div class="detail-image-box">
                <img src="data:image/png;base64,<?= $img ?>" alt="book image">
                
                <!-- 画像の右上に重ねるお気に入りフォーム -->
                <form action="" method="post" class="favorite-form">
                    <input type="hidden" name="f_u" value="<?= htmlspecialchars($u_id, ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="f_b" value="<?= htmlspecialchars($b_id, ENT_QUOTES, 'UTF-8') ?>">
                    <!-- 登録状態によってクラスを切り替え (is-favorite) -->
                    <button type="submit" class="favorite-btn <?= $is_favorite ? 'is-favorite' : '' ?>" title="お気に入り登録/解除">
                        ★
                    </button>
                </form>
            </div>
        </div>

        <!-- 右側：詳細情報エリア（テキストテーブルのみ） -->
        <div class="detail-right">
            <div class="detail-table">
                <div class="detail-label">タイトル</div>
                <div class="detail-value"><?= htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8') ?></div>

                <div class="detail-label">ジャンル</div>
                <div class="detail-value"><?= htmlspecialchars($row['genre'], ENT_QUOTES, 'UTF-8') ?></div>

                <div class="detail-label">在庫数</div>
                <div class="detail-value"><?= htmlspecialchars($row['count'], ENT_QUOTES, 'UTF-8') ?></div>

                <div class="detail-label">補足</div>
                <div class="detail-value"><?= htmlspecialchars($row['hosoku'] ?? '', ENT_QUOTES, 'UTF-8') ?></div>
            </div>
        </div>

        <!-- ★完全に独立した「借りる」ボタンエリア（左右の列をまたいで中央に配置） -->
        <div class="rental-area">
            <button type="button" class="rental-btn" onclick="document.getElementById('dg').showModal()">借りる</button>
        </div>

    </div>

    <!-- 借りる確認用ダイアログ -->
    <dialog id="dg">
        <?php if ($c == 0): ?>
            <p class="dialog-message" style="color: red;">この本は在庫がありません</p>
            <button type="button" class="dialog-btn-no" onclick="document.getElementById('dg').close()">閉じる</button>
        <?php else: ?>
            <form method="POST" action="rental.php">
                <p class="dialog-message">この本を借りますか？</p>
                <input type="hidden" name="i" value="<?= htmlspecialchars($b_id, ENT_QUOTES, 'UTF-8') ?>">
                <div class="detail-dialog-buttons">
                    <button type="submit" class="dialog-btn-yes">はい</button>
                    <button type="button" class="dialog-btn-no" onclick="document.getElementById('dg').close()">いいえ</button>
                </div>
            </form>
        <?php endif; ?>
    </dialog>

</body>
</html>