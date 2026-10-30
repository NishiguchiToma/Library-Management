<?php require 'menu.php'; ?>
<?php require 'config/database.php'; ?>
<?php session_start(); ?>
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
    $title = $_POST['title'];
    $genre = $_POST['genre'];
    $count = $_POST['count'];
    $hosoku = $_POST['hosoku'];

    if (!empty($_FILES['file']['tmp_name']) && !empty($title) && !empty($genre) && !empty($count)){
         $book_image = file_get_contents($_FILES['file']['tmp_name']);
         $hyouji = 1;
         $rental = 0;
         try{
             $ins = $pdo->prepare('INSERT INTO book (title, genre, hosoku, book_image, hyouji, rental, count) VALUES (?, ?, ?, ?, ?, ?, ?)');
            $ins->bindValue(1, $title, PDO::PARAM_STR);
            $ins->bindValue(2, $genre, PDO::PARAM_STR);
            $ins->bindValue(3, $hosoku, $hosoku === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
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
<?php
    if(!empty($er)){
        echo $er;
    }
?>

<form action="" method="post" enctype="multipart/form-data">
    <h2>本の画像<input type="file" name="file" value="画像をアップロード"></h2>
    <h2>タイトル<input type="text" name="title"></h2>
    <h2>ジャンル<select name="genre">
        <option value="">-- 選択してください --</option>
        <?php
            foreach(Genre::cases() as $row){
                $value = htmlspecialchars($row->value);
                $label = htmlspecialchars($row->label());
                echo "<option value='{$value}'>{$label}</option>";
            }
        ?>
    </select></h2>
    <h2>在庫数<input type="number" name="count" min="1" value="1"></h2>
    <h2>補足</h2>
    <p><textarea name="hosoku"></textarea></p>
    <input type="submit" value="登録">
    <button type="button"><a href="home.php">戻る</a></button>
</form>