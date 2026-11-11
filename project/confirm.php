<?php
session_start();
require 'menu.php'; 
require 'config/database.php';

// ログインしているかどうかのチェック
if (!isset($_SESSION['use']) || !isset($_SESSION['use']['id'])) {
    header('Location: index.php');
    exit;
}

$login_user_id = $_SESSION['use']['id'];

try {
    $sql = "SELECT 
                l.rental_day, 
                l.return_day, 
                l.yotei, 
                b.title 
            FROM lending l
            LEFT JOIN book b ON l.book_id = b.book_id
            WHERE l.user_id = ?
            ORDER BY l.rental_day DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$login_user_id]);
    $histories = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error_message = "データベース接続エラー: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>貸出確認 - 図書館管理システム</title>
    <link rel="stylesheet" href="css/history.css">
</head>
<body>

    <h1>あなたの貸出履歴</h1>

    <?php if (isset($error_message)): ?>
        <p style="color: red;"><?= $error_message ?></p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>日付</th>
                    <th>タイトル</th>
                    <th>ステータス</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($histories)): ?>
                    <tr>
                        <td colspan="3" class="no-border" style="text-align: center;">現在、借りている（または過去に借りた）本はありません。</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($histories as $row): ?>
                        <?php
                            // --- ステータスと残り日数の計算 ---
                            $today = strtotime(date('Y-m-d'));
                            $yotei = strtotime($row['yotei']);
                            $diff_days = (int)(($yotei - $today) / (60 * 60 * 24));

                            if (empty($row['return_day'])) {
                                // 未返却の場合
                                if ($diff_days >= 0) {
                                    $status_text = "未返却（あと{$diff_days}日）";
                                } else {
                                    $overdue_days = abs($diff_days);
                                    $status_text = "未返却（超過{$overdue_days}日）";
                                }
                                $status_class = "status-red"; // 未返却は赤字にするクラス
                            } else {
                                // 返却済の場合
                                $status_text = "返却済";
                                $status_class = "";
                            }

                            // 日付の表示形式（未返却なら予定日まで、返却済なら返却日まで）
                            $date_str = htmlspecialchars($row['rental_day'], ENT_QUOTES, 'UTF-8') . '~' . 
                                        htmlspecialchars(!empty($row['return_day']) ? $row['return_day'] : $row['yotei'], ENT_QUOTES, 'UTF-8');
                        ?>
                        <tr>
                            <td><?= $date_str ?></td>
                            <td><?= htmlspecialchars($row['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                            <td><span class="<?= $status_class ?>"><?= $status_text ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    <?php endif; ?>

</body>
</html>