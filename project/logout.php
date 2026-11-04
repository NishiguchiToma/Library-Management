<?php
// セッションを開始する
session_start();

// 1. セッションのデータをすべて空にする（これで拒否カウントも消えます）
$_SESSION = array();

// 2. セッションのクッキーを削除する
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 3. セッションを完全に破壊する
session_destroy();

// 4. ログイン画面（index.php）へリダイレクトする
header("Location: index.php");
exit;