<?php
session_start();
require 'config/database.php';

// ログイン中のユーザーID（※必要に応じて 'use' を 'user' に直してください）
$user_id = $_SESSION['use']['id'] ?? '';

// ▼ ここから追加：JSからの削除リクエスト（POST）を受け取ったときの処理 ▼
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json; charset=UTF-8');

    $input = json_decode(file_get_contents('php://input'), true);
    $favorite_ids = $input['favorite_ids'] ?? [];

    if ($user_id === '') {
        echo json_encode(['success' => false, 'error' => 'ログイン情報が確認できません。']);
        exit;
    }

    if (empty($favorite_ids) || !is_array($favorite_ids)) {
        echo json_encode(['success' => false, 'error' => '削除対象が選択されていません。']);
        exit;
    }

    try {
        $placeholders = implode(',', array_fill(0, count($favorite_ids), '?'));
        $sql = "DELETE FROM favorite WHERE user_id = ? AND favorite_id IN ($placeholders)";
        $stmt = $pdo->prepare($sql);

        $params = array_merge([$user_id], $favorite_ids);
        $stmt->execute($params);

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => 'データベースエラー: ' . $e->getMessage()]);
    }
    exit; // ここで終了させ、下のHTMLは出力しない
}
// ▲ ここまで追加 ▲

require 'menu.php';

// お気に入りの本を取得（通常の画面表示処理）
$sql = 'SELECT f.favorite_id, b.title FROM favorite f JOIN book b ON f.book_id = b.book_id WHERE f.user_id = :user_id ORDER BY f.favorite_id ASC';
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
                <p class="empty-message" style="text-align: center; width: 100%;">
                    お気に入りの本はありません。
                </p>
            <?php else: ?>
                <?php foreach ($favorite_list as $row): ?>
                    <label class="book">
                        <input type="checkbox" name="favorite[]" value="<?php echo htmlspecialchars($row['favorite_id'], ENT_QUOTES, 'UTF-8'); ?>">
                        <span>
                            <?php echo htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8'); ?>
                        </span>
                    </label>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div class="button-area">
            <p>選択したものをまとめてお気に入り解除</p>
            <button type="button" class="delete-button" id="deleteButton">
                解除
            </button>
        </div>
        <p id="message"></p>
    </main>
    <script src="js/favorite.js"></script>
</body>
</html>
