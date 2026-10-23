<?php
// 各項目のエラーメッセージを保持する連想配列
$errors = [
    'id' => '',
    'pas' => '',
    'pas2' => '',
    'name' => '',
    'address' => ''
];

// 送信された値を保持してフォームに残すための変数
$id = $name = $address = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST['id'] ?? '';
    $pas = $_POST['pas'] ?? '';
    $pas2 = $_POST['pas2'] ?? '';
    $name = $_POST['name'] ?? '';
    $address = $_POST['address'] ?? '';

    // 1. 各項目の必須入力（未入力）チェック【最優先】
    if (empty($id))      $errors['id'] = "この項目は必須項目です。";
    if (empty($pas))     $errors['pas'] = "この項目は必須項目です。";
    if (empty($pas2))    $errors['pas2'] = "この項目は必須項目です。";
    if (empty($name))    $errors['name'] = "この項目は必須項目です。";
    if (empty($address)) $errors['address'] = "この項目は必須項目です。";

    // 2. パスワードの条件チェック【優先度：中】
    if (empty($errors['pas'])) {
        $is_length_ok  = (strlen($pas) >= 30);
        $has_uppercase = preg_match('/[A-Z]/', $pas);
        $has_lowercase = preg_match('/[a-z]/', $pas);
        $has_number    = preg_match('/[0-9]/', $pas);
        $has_symbol    = preg_match('/[\x21-\x2F\x3A-\x40\x5B-\x60\x7B-\x7E]/', $pas);

        if (!$is_length_ok || !$has_uppercase || !$has_lowercase || !$has_number || !$has_symbol) {
            $errors['pas'] = "パスワードが条件を満たしていません。";
        }
    }

    // 3. パスワードの一致チェック【優先度：低】
    if (empty($errors['pas']) && empty($errors['pas2'])) {
        if ($pas !== $pas2) {
            $errors['pas2'] = "パスワードと再確認用パスワードが一致しません。";
        }
    }

    // ==========================================
    // ★ 4. データベースへの保存処理
    // ==========================================
    // エラーが1つもない場合のみ実行
    if (array_filter($errors) === []) {
        
        // データベース接続情報
        $dsn = 'mysql:host=localhost;dbname=tosho;charset=utf8mb4';
        $db_user = 'root';
        $db_pass = 'root';

        try {
            // PDOインスタンスの作成（データベース接続）
            $pdo = new PDO($dsn, $db_user, $db_pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // エラー時に例外を投げる設定
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);

            // パスワードを安全にハッシュ化（暗号化）
            $hashed_password = password_hash($pas, PASSWORD_DEFAULT);

            // SQL文の準備（テーブル名は一般的な「users」と仮定しています。違う場合は変更してください）
            // 指定されたカラム名（user_id, password, name, address）をセットしています
            $sql = "INSERT INTO users (user_id, password, name, address) VALUES (:user_id, :password, :name, :address)";
            $stmt = $pdo->prepare($sql);

            // データのバインド（SQLインジェクション対策）
            $stmt->bindValue(':user_id', $id, PDO::PARAM_STR);
            $stmt->bindValue(':password', $hashed_password, PDO::PARAM_STR);
            $stmt->bindValue(':name', $name, PDO::PARAM_STR);
            $stmt->bindValue(':address', $address, PDO::PARAM_STR);

            // 実行
            $stmt->execute();

            // 保存が成功したら loginO.php へ遷移
            header("Location: loginO.php");
            exit;

        } catch (PDOException $e) {
            // 万が一データベースエラーが発生した場合
            // すでに同じ学籍番号が登録されている場合（主キー重複）などのハンドリング
            if ($e->getCode() == 23000) {
                $errors['id'] = "この学籍番号・教員番号はすでに登録されています。";
            } else {
                // その他のデータベースエラー
                $errors['id'] = "データベースエラーが発生しました: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>新規登録</title>
    <style>
        /* 全体の基本設定 */
        body {
            font-family: "Helvetica Neue", Arial, "Hiragino Kaku Gothic ProN", "Hiragino Sans", Meiryo, sans-serif;
            background-color: #ffffff;
            color: #333333;
            margin: 0;
            padding: 40px 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* タイトル（新規登録） */
        h1 {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 30px;
            text-align: center;
            letter-spacing: 2px;
        }

        /* フォーム全体の囲み枠 */
        form {
            width: 100%;
            max-width: 450px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        /* 各入力項目のグループ */
        form p {
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 8px;
            font-size: 14px;
            color: #555555;
        }

        /* パスワード注意書き */
        form p .note {
            font-size: 11px;
            color: #888888;
            margin-top: 2px;
        }

        /* エラーメッセージのスタイル */
        .error-text {
            color: #cc0000;
            font-size: 13px;
            margin-top: 4px;
            font-weight: bold;
        }

        /* 入力欄（テキスト・パスワードボックス） */
        form input[type="text"],
        form input[type="password"] {
            width: 100%;
            height: 45px;
            padding: 0 12px;
            font-size: 16px;
            border: 2px solid #0091cb;
            border-radius: 4px;
            box-sizing: border-box;
            transition: border-color 0.2s;
        }

        /* エラーがある入力欄の枠線を赤くする */
        form input.input-error {
            border-color: #cc0000;
        }

        form input[type="text"]:focus,
        form input[type="password"]:focus {
            outline: none;
            border-color: #006f9c;
        }

        /* ログインはこちら へのリンク */
        .login-link {
            display: block;
            margin-top: 10px;
            color: #333333;
            font-size: 14px;
            text-decoration: none;
            text-align: center;
        }

        .login-link:hover {
            text-decoration: underline;
        }

        /* 新規登録ボタン */
        form input[type="submit"] {
            width: 100%;
            max-width: 180px;
            height: 45px;
            margin: 15px auto 5px auto;
            background-color: #0091cb;
            color: #ffffff;
            font-size: 18px;
            font-weight: bold;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            letter-spacing: 1px;
            transition: background-color 0.2s;
        }

        form input[type="submit"]:hover {
            background-color: #007bb0;
        }
    </style>
</head>
<body>

    <h1>新規登録</h1>
    
    <form action="" method="post" novalidate>
        
        <!-- 学籍番号・教員番号(ID) -->
        <p>
            学籍番号・教員番号(ID)
            <input type="text" name="id" value="<?php echo htmlspecialchars($id, ENT_QUOTES, 'UTF-8'); ?>" class="<?php echo !empty($errors['id']) ? 'input-error' : ''; ?>">
            <?php if (!empty($errors['id'])): ?>
                <span class="error-text"><?php echo $errors['id']; ?></span>
            <?php endif; ?>
        </p>
        
        <!-- パスワード -->
        <p>
            パスワード
            <input type="password" name="pas" class="<?php echo !empty($errors['pas']) ? 'input-error' : ''; ?>">
            <span class="note">※30文字以上、大文字・小文字・数字・記号を各1回以上</span>
            <?php if (!empty($errors['pas'])): ?>
                <span class="error-text"><?php echo $errors['pas']; ?></span>
            <?php endif; ?>
        </p>
        
        <!-- パスワード(再確認) -->
        <p>
            パスワード(再確認)
            <input type="password" name="pas2" class="<?php echo !empty($errors['pas2']) ? 'input-error' : ''; ?>">
            <?php if (!empty($errors['pas2'])): ?>
                <span class="error-text"><?php echo $errors['pas2']; ?></span>
            <?php endif; ?>
        </p>
        
        <!-- 名前 -->
        <p>
            名前
            <input type="text" name="name" value="<?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>" class="<?php echo !empty($errors['name']) ? 'input-error' : ''; ?>">
            <?php if (!empty($errors['name'])): ?>
                <span class="error-text"><?php echo $errors['name']; ?></span>
            <?php endif; ?>
        </p>
        
        <!-- メールアドレス -->
        <p>
            メールアドレス
            <input type="text" name="address" value="<?php echo htmlspecialchars($address, ENT_QUOTES, 'UTF-8'); ?>" class="<?php echo !empty($errors['address']) ? 'input-error' : ''; ?>">
            <?php if (!empty($errors['address'])): ?>
                <span class="error-text"><?php echo $errors['address']; ?></span>
            <?php endif; ?>
        </p>
        
        <a href="index.php" class="login-link">ログインはこちら</a>
        
        <input type="submit" value="新規登録">
    </form>
</body>
</html>
