<?php
// エラーを画面に隠さず表示
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Composerのオートロード（パスが違う場合は適宜調整してください）
require_once __DIR__ . '/../vendor/autoload.php';

// ★ご自身の情報に書き換えてください
$smtp_user = 'oharatosyo@gmail.com'; // 送信元（ご自身のGmail）
$smtp_pass = 'liqrgvhfpstxzjii';    // Googleアカウントで発行した16桁のアプリパスワード
$test_to   = 'M2520046@ngo.ohara.ac.jp';             // テスト送信先（自分宛てでOK）

echo "<h2>PHPMailer 単体メール送信テスト</h2>";

try {
    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);

    // 詳細なデバッグ出力を画面にリアルタイム表示
    $mail->SMTPDebug = 2;
    $mail->Debugoutput = function($str, $level) {
        echo "<div style='font-family:monospace; font-size:12px; color:#333;'>[SMTP] " . htmlspecialchars($str, ENT_QUOTES, 'UTF-8') . "</div>";
        flush(); ob_flush();
    };

    // SMTP設定
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = $smtp_user;
    $mail->Password = $smtp_pass;
    $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    $mail->CharSet = 'UTF-8';
    $mail->Timeout = 15; // 15秒でタイムアウト

    // 送信元・送信先
    $mail->setFrom($smtp_user, 'テスト送信者');
    $mail->addAddress($test_to, '自分');

    // メールの内容
    $mail->isHTML(false);
    $mail->Subject = 'PHPMailer 接続テストメール';
    $mail->Body = 'これはローカル環境からのメール送信テストです。このメールが届けば、PHPMailerの通信自体は成功しています。';

    echo "<p>-> メール送信を実行します...</p>";
    $mail->send();
    echo "<h3 style='color:green;'>【成功】メールの送信が正常に完了しました！受信トレイを確認してください。</h3>";

} catch (\Throwable $e) {
    echo "<h3 style='color:red;'>【失敗】エラーが発生しました</h3>";
    echo "<p style='color:red;'>" . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . "</p>";
}