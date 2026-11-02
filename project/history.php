<?php
require 'menu.php'; 
require 'config/database.php';
session_start();
try {
    $sql = "SELECT 
                l.rental_day, 
                l.return_day, 
                l.yotei, 
                u.name, 
                b.title 
            FROM lending l
            LEFT JOIN users u ON l.user_id = u.user_id
            LEFT JOIN book b ON l.book_id = b.book_id
            ORDER BY l.rental_day DESC";

    $stmt = $pdo->query($sql);
    $histories = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error_message = "データベース接続エラー: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>新規本登録 - 図書館管理システム</title>
    <link rel="stylesheet" href="css/new-book.css">
</head>
<body>
<?php
// 管理者権限チェック（roleが1以外の場合）
if (!isset($_SESSION['role']) || $_SESSION['role'] != 1) {
    
    // ① 拒否された回数をカウントする（セッションを使用）
    if (!isset($_SESSION['history_deny_count'])) {
        $_SESSION['history_deny_count'] = 1;
    } else {
        $_SESSION['history_deny_count']++;
    }
    $history_deny_count = $_SESSION['history_deny_count'];

    // ② 回数に応じてエラーメッセージを変化させる
    if ($history_deny_count === 1) {
        $message = "お前は管理者じゃないので閲覧できません。";
    } elseif ($history_deny_count === 2) {
        $message = "だから見れないって";
    } else {
        $message = "{$history_deny_count}";
    }

    // ③ メッセージを出力して停止
    echo '<p class="access-denied">' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</p>';
    exit;
}
?>

    <h1>本の貸し出し履歴</h1>

    <?php if (isset($error_message)): ?>
        <p style="color: red;"><?= $error_message ?></p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>日付</th>
                    <th>名前</th>
                    <th>タイトル</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($histories)): ?>
                    <tr>
                        <td colspan="3" style="text-align: center;">需要無いから誰も借りてない</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($histories as $row): ?>
                        <tr>
                            <td>
                                <?php if (empty($row['return_day'])): ?>
                                    <?= htmlspecialchars($row['rental_day'], ENT_QUOTES, 'UTF-8') ?>~(未)<span class="yotei-red"><?= htmlspecialchars($row['yotei'], ENT_QUOTES, 'UTF-8') ?></span>
                                <?php else: ?>
                                    <?= htmlspecialchars($row['rental_day'], ENT_QUOTES, 'UTF-8') ?>~(済)<?= htmlspecialchars($row['return_day'], ENT_QUOTES, 'UTF-8') ?>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($row['name'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($row['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    <?php endif; ?>

</body>
</html>