<?php require 'menu.php'; ?>
<?php require 'config/database.php'; ?>
<?php session_start(); ?>
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
    if (!isset($_SESSION['new_book_deny_count'])) {
        $_SESSION['new_book_deny_count'] = 1;
    } else {
        $_SESSION['new_book_deny_count']++;
    }
    $new_book_deny_count = $_SESSION['new_book_deny_count'];

    // ② 回数に応じてエラーメッセージを変化させる
    if ($new_book_deny_count === 1) {
        $message = "お前は管理者じゃないので閲覧できません。";
    } elseif ($new_book_deny_count === 2) {
        $message = "お前は管理者じゃない！";
    } else {
        $message = "開いた回数:{$new_book_deny_count}";
    }
    echo '<p class="access-denied">' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</p>';
    exit;
}
?>
<?php
enum Genre: string
{
    case Php = 'php';
    case Java = 'java';
    case Javascript = 'javascript';
    case Python = 'python';
    case Html = 'html';
    public function label(): string
    {
        return match($this) {
            self::Php => 'PHP',
            self::Java => 'Java',
            self::Javascript => 'JavaScript',
            self::Python => 'Python',
            self::Html => 'HTML',
        };
    }
}

$er = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $title = $_POST['title'] ?? '';
    $genre = $_POST['genre'] ?? '';
    $count = $_POST['count'] ?? '';
    $hosoku = $_POST['hosoku'] ?? '';

    if (!empty($_FILES['file']['tmp_name']) && !empty($title) && !empty($genre) && !empty($count)){
        $book_image = file_get_contents($_FILES['file']['tmp_name']);
        $hyouji = 1;
        $rental = 0;
        try{
            $ins = $pdo->prepare('INSERT INTO book (title, genre, hosoku, book_image, hyouji, rental, count) VALUES (?, ?, ?, ?, ?, ?, ?)');
            $ins->bindValue(1, $title, PDO::PARAM_STR);
            $ins->bindValue(2, $genre, PDO::PARAM_STR);
            $ins->bindValue(3, $hosoku, $hosoku === '' ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $ins->bindValue(4, $book_image, PDO::PARAM_LOB); // 画像バイナリ
            $ins->bindValue(5, $hyouji, PDO::PARAM_INT);
            $ins->bindValue(6, $rental, PDO::PARAM_INT);
            $ins->bindValue(7, $count, PDO::PARAM_INT);

            if($ins->execute()){
                echo "<script> alert('書籍を登録しました'); window.location.href = 'home.php'; </script>";
                exit;
            }else{
                $er = "登録処理に失敗しました。";
            }
        }catch(PDOException $e){
            $er = "エラーが発生しました: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
        }
    }else{
        $er="画像、タイトル、ジャンル、在庫数を入力してください";
    }
}
?>


    <h1>新規本登録</h1>

    <?php if(!empty($er)): ?>
        <p class="error-msg"><?= $er ?></p>
    <?php endif; ?>

    <form action="" method="post" enctype="multipart/form-data" class="book-form">
        <div class="form-row">
            <label>本の画像</label>
            <div class="form-input">
                <input type="file" name="file" class="file-input">
            </div>
        </div>

        <div class="form-row">
            <label>タイトル</label>
            <div class="form-input">
                <input type="text" name="title" class="text-input">
            </div>
        </div>

        <div class="form-row">
            <label>ジャンル</label>
            <div class="form-input">
                <select name="genre" class="select-input">
                    <option value=""></option>
                    <?php
                        foreach(Genre::cases() as $row){
                            $value = htmlspecialchars($row->value, ENT_QUOTES, 'UTF-8');
                            $label = htmlspecialchars($row->label(), ENT_QUOTES, 'UTF-8');
                            echo "<option value='{$value}'>{$label}</option>";
                        }
                    ?>
                </select>
            </div>
        </div>

        <div class="form-row">
            <label>在庫数</label>
            <div class="form-input">
                <input type="number" name="count" min="1" value="1" class="number-input">
            </div>
        </div>

        <div class="form-group-block">
            <label>補足</label>
            <textarea name="hosoku" class="textarea-input"></textarea>
        </div>

        <div class="form-buttons">
            <input type="submit" value="登録" class="btn-submit">
        </div>
    </form>

</body>
</html>