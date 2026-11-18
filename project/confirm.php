<?php
// エラー表示設定（必要に応じて0にしてください）
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/../vendor/autoload.php'; // Composerのオートロード

// ログインしているかどうかのチェック
if (!isset($_SESSION['use']) || !isset($_SESSION['use']['id'])) {
    header('Location: index.php');
    exit;
}

$login_user_id = $_SESSION['use']['id'];

/* ==========================================
    返却処理 ＆ 在庫数更新 ＆ メール通知処理
========================================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['l_id'])) {
    $b_id = $_POST['b_id'];
    $lend_id = $_POST['lend_id'] ?? $_POST['l_id']; // フォームのインプット名に合わせて安全に取得

    try {
        // 1. 書籍の在庫数（count）を +1 する
        $sq = $pdo->prepare('UPDATE book SET count = count + 1 WHERE book_id = ?');
        $sq->execute([$b_id]);

        // 2. 貸出データの return_day を今日の日付に更新（まだ返却されていないものに限定）
        $sql_update = "UPDATE lending SET return_day = CURDATE() WHERE lending_id = ? AND user_id = ? AND return_day IS NULL";
        $stmt_update = $pdo->prepare($sql_update);
        $stmt_update->execute([$lend_id, $login_user_id]);

        // 3. 実際に返却更新が成功した場合のみ、お気に入り登録者へのメール送信処理を行う
        if ($stmt_update->rowCount() === 1) {

            // 書籍タイトルを取得（メール本文用）
            $sql_book = "SELECT title FROM book WHERE book_id = ?";
            $stmt_book = $pdo->prepare($sql_book);
            $stmt_book->execute([$b_id]);
            $book_row = $stmt_book->fetch(PDO::FETCH_ASSOC);
            $book_title = $book_row['title'] ?? '書籍';

            // 4. この本をお気に入り登録しているユーザーを取得（アドレスが存在するもの）
            $sql_favorite = "
                SELECT DISTINCT u.user_id, u.name, u.address AS email 
                FROM favorite AS f 
                INNER JOIN `users` AS u ON f.user_id = u.user_id 
                WHERE f.book_id = ? AND u.address IS NOT NULL AND u.address <> ''
            ";
            $stmt_favorite = $pdo->prepare($sql_favorite);
            $stmt_favorite->execute([$b_id]);
            $favorite_users = $stmt_favorite->fetchAll(PDO::FETCH_ASSOC);

            // 5. Gmailの認証情報（★テストで成功したご自身の情報に書き換えてください）
            $smtp_user = 'oharatosyo@gmail.com'; // 送信元Gmailアドレス
            $smtp_pass = 'liqrgvhfpstxzjii';    // 16桁のアプリパスワード

            // 6. お気に入り登録者へメールを送信
            foreach ($favorite_users as $user) {
                if (!filter_var($user['email'], FILTER_VALIDATE_EMAIL)) {
                    continue;
                }

                try {
                    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);

                    // SMTP設定（テストで動作確認済みの設定）
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = $smtp_user;
                    $mail->Password = $smtp_pass;
                    $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;
                    $mail->CharSet = 'UTF-8';
                    $mail->Timeout = 10;

                    // 送信元・送信先
                    $mail->setFrom($smtp_user, '図書館管理システム');
                    $mail->addAddress($user['email'], $user['name'] ?? '');

                    // メールの内容
                    $mail->isHTML(false);
                    $mail->Subject = 'お気に入り登録した本が返却されました';
                    $mail->Body = $user['name'] . " 様\n\n" .
                        "お気に入り登録している本が返却されました！\n\n" .
                        "書籍名：" . $book_title . "\n\n" .
                        "ぜひ図書管理システムからご確認ください。\n\n" .
                        "図書館管理システム";

                    $mail->send();

                } catch (\Throwable $e) {
                    // 個別のメール送信エラーは処理を止めずに続行、または必要に応じてデバッグ
                }
            }
        }

        // 処理が完了したら元のページにリフレッシュ
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;

    } catch (PDOException $e) {
        echo "<h1>データベースエラー</h1>";
        echo "<p style='color:red;'>" . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . "</p>";
        exit;
    }
}

require 'menu.php'; 

try {
    $sql = "SELECT 
                l.rental_day, 
                l.return_day, 
                l.yotei, 
                l.lending_id,
                l.book_id,
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
                            <td><span class="<?= $status_class ?>"><?= $status_text ?></span>
                            <?php if (empty($row['return_day'])): ?>
                                <button type="button"
                                data-id="<?= htmlspecialchars($row['lending_id'], ENT_QUOTES, 'UTF-8') ?>" 
                                data-book-id="<?= htmlspecialchars($row['book_id'], ENT_QUOTES, 'UTF-8') ?>"
                                data-title="<?= htmlspecialchars($row['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                onclick="openReturnDialog(this.dataset.id, this.dataset.bookId, this.dataset.title)">
                                返却</button>
                            <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <dialog id="return-dg">
        <form action="" method="post">
            <p class="dialog-message" id="dialog-book-title"></p>
            <input type="hidden" name="l_id" id="dialog-lending-id">
            <input type="hidden" name="b_id" id="dialog-book-id">
            <div class="detail-dialog-buttons">
                <button type="submit" class="dialog-btn-yes">はい</button>
                <button type="button" class="dialog-btn-no" onclick="document.getElementById('return-dg')->close()">いいえ</button>
            </div>
        </form>
    </dialog>

    <script>
        function openReturnDialog(lendingId, bookId, bookTitle) {
            document.getElementById('dialog-lending-id').value = lendingId;
            document.getElementById('dialog-book-id').value = bookId; 
            document.getElementById('dialog-book-title').textContent = '「 ' + bookTitle + ' 」を返却しますか？';
            document.getElementById('return-dg').showModal();
        }
    </script>
</body>
</html>