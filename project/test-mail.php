
<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php';

// 環境変数からメール情報を取得
$smtpUser = getenv('SMTP_USER');
$smtpPass = getenv('SMTP_PASS');
$testTo = getenv('TEST_TO');

// 必須情報の確認
if (!$smtpUser || !$smtpPass || !$testTo) {
    exit("メール設定が不足しています。\n");
}

$mail = new PHPMailer(true);

try {

    // SMTP設定
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = $smtpUser;
    $mail->Password = $smtpPass;

    // 暗号化
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // 文字コード
    $mail->CharSet = 'UTF-8';

    // 送信元
    $mail->setFrom(
        $smtpUser,
        '図書管理システム'
    );

    // 送信先
    $mail->addAddress($testTo);

    // メール内容
    $mail->isHTML(false);
    $mail->Subject = 'メール送信テスト';

    $mail->Body =
        "メール送信テストです。\n\n" .
        "PHPMailerとGmailの接続に成功しました！\n\n" .
        "図書館管理システム";

    // 送信
    $mail->send();

    echo "メール送信成功！";

} catch (Exception $e) {

    echo "メール送信失敗。\n";
    error_log($mail->ErrorInfo);

}
