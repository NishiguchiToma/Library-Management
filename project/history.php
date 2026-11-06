<?php
session_start();
require 'menu.php'; 
require 'config/database.php';
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
    <link rel="stylesheet" href="css/history.css">
</head>
<body>
<?php
// 管理者権限チェック（roleが1以外の場合）
if (!isset($_SESSION['use']['role']) || $_SESSION['use']['role'] != 1) {
    
    // ① 拒否された回数をカウントする（セッションを使用）
    if (!isset($_SESSION['history_deny_count'])) {
        $_SESSION['history_deny_count'] = 1;
    } else {
        $_SESSION['history_deny_count']++;
    }
    $history_deny_count = $_SESSION['history_deny_count'];

    // ② 回数に応じてエラーメッセージを変化させる
    if ($history_deny_count < 4) {
        $message = "あなたは管理者じゃないので閲覧できません。";
        $css_class = "access-denied";
    } elseif ($history_deny_count < 5) {
        $message = "お前は管理者じゃない！";
        $css_class = "access-denied";
    } elseif ($history_deny_count > 0 && $history_deny_count % 100 === 0) {
        $message = "祝！{$history_deny_count}回";
        $css_class = "access-denied-special";
    }else {
        $message = "開いた回数:{$history_deny_count}";
        $css_class = "access-denied";
    }

    echo '<p class="' . $css_class . '">' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</p>';
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
                    <th>返却日/返却予定日</th>
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
                                    <span class="status-red">(未) </span><?= htmlspecialchars($row['rental_day'], ENT_QUOTES, 'UTF-8') ?>~<?= htmlspecialchars($row['yotei'], ENT_QUOTES, 'UTF-8') ?>
                                <?php else: ?>
                                    (済) <?= htmlspecialchars($row['rental_day'], ENT_QUOTES, 'UTF-8') ?>~<?= htmlspecialchars($row['return_day'], ENT_QUOTES, 'UTF-8') ?>
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