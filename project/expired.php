<?php 
// menu.phpとデータベースを読み込む
require 'menu.php'; 
require 'config/database.php';

// 【解説】期限切れの貸出情報を取得するSQL
// DATEDIFF(現在日, 返却予定日) で超過した日数を計算します
$sql = 'SELECT l.yotei, u.name, b.title, DATEDIFF(CURDATE(), l.yotei) AS overdue_days 
        FROM lending l 
        JOIN users u ON l.user_id = u.user_id 
        JOIN book b ON l.book_id = b.book_id 
        WHERE l.yotei < CURDATE() 
        ORDER BY l.yotei ASC';

// 上記のSQL情報をデータベースに送信して実行する準備
$stmt = $pdo->prepare($sql);
$stmt->execute();

// データベースのデータを連想配列にして $lending_list に入れる
$lending_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<link rel="stylesheet" href="css/expired.css">

<!-- ここから下が画面の見た目の部分（HTML） -->
<h1>返却期限切れ一覧</h1>

<table>
    <tr>
        <th>名前</th>
        <th>タイトル</th>
        <th>返却予定日</th>
    </tr>

    <?php if (empty($lending_list)): ?>
        <!-- 期限切れのデータがなかった時に表示する -->
        <tr>
            <td class="no-border" colspan="3">現在、返却期限切れの不届き者はいません。</td>
        </tr>
    <?php else: ?>
        <?php foreach ($lending_list as $row): ?>
        <tr>
            <!-- htmlspecialcharsで安全な形に変換して表示 -->
            <td><?php echo htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td>
                <?php echo htmlspecialchars($row['yotei'], ENT_QUOTES, 'UTF-8'); ?>
    
                <span class="overdue-text">
                    （+<?php echo htmlspecialchars($row['overdue_days'], ENT_QUOTES, 'UTF-8'); ?>日）
                </span>
            </td>
        </tr>
        <?php endforeach; ?>
    <?php endif; ?>
</table>