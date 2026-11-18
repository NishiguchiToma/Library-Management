<?php
session_start();
require 'config/database.php';
require 'menu.php';

$b_id = $_POST['b_id'] ?? $_POST['book_id'] ?? '';
$u_id = $_SESSION['use']['id'] ?? '';
$edit=$_POST['book_id']??'';
$er="";
$t=$_POST["title"];
$g=$_POST["genre"];
$c=$_POST["count"];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($edit)){
        $sql2 =$pdo->prepare( 'UPDATE book SET title=?,genre=?,count=?,hosoku=? where book_id=?');
        $sql2->execute([$_POST["title"],$_POST["genre"],$_POST["count"],$_POST["hosoku"],$edit]);
       echo "<script>
               alert('登録しました');
             </script>";
    }

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['delete_id'])) {
    $del_sql = $pdo->prepare('DELETE FROM book WHERE book_id = ?');
    $del_sql->execute([$_POST['delete_id']]);
    echo "<script>alert('削除しました'); location.href='home.php';</script>";
    exit;
}

// 本情報の取得
$sql = $pdo->prepare('SELECT * FROM book WHERE book_id = ?');
$sql->execute([$b_id]);
$row = $sql->fetch();

if (!$row) {
    echo "<p style='text-align:center; margin-top:50px; font-weight:bold;'>指定された本は見つかりませんでした。</p>";
    exit;
}

$img = base64_encode($row['book_image']);


enum Genre: string
{
    case Php = 'php';
    case Java = 'java';
    case Javascript = 'javascript';
    case Python = 'python';
    case HtmlCss = 'html css';
    case FundamentalInfo = '基本情報';
    case Other = 'その他';
    public function label(): string
    {
        return match($this) {
            self::Php => 'PHP',
            self::Java => 'Java',
            self::Javascript => 'JavaScript',
            self::Python => 'Python',
            self::HtmlCss => 'HTML/CSS',
            self::FundamentalInfo => '基本情報',
            self::Other => 'その他',
        };
    }
}

?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>本の詳細 - 図書館管理システム</title>
    <link rel="stylesheet" href="css/detail.css">
</head>

<body>
    <!-- 全体を囲むグリッドコンテナ -->
    <div class="detail-container">
        <div class="detail-left">
            <div class="detail-image-box">
                <img src="data:image/png;base64,<?= $img ?>" alt="book image">
            </div>
        </div>
        <!-- 右側：詳細情報エリア（テキストテーブルのみ） -->
        <div class="detail-right">
            <form id="edit-form" action="edit.php" method="post">
                <input type="hidden" name="book_id" value="<?= htmlspecialchars($b_id, ENT_QUOTES, 'UTF-8') ?>">
                <div class="detail-table">
                    <div class="detail-label">タイトル</div>
                    <div class="detail-value">
                        <input type="text" name="title" value="<?= htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8') ?>">
                    </div>
                    <div class="detail-label">ジャンル</div>
                    <div class="detail-value">
                        <select name="genre" class="select-input">
                            <option value=""></option>
                            <?php
                            foreach (Genre::cases() as $genre) {
                                $value = htmlspecialchars($genre->value, ENT_QUOTES, 'UTF-8');
                                $label = htmlspecialchars($genre->label(), ENT_QUOTES, 'UTF-8');
                                $selected = (($row['genre'] ?? '') == $genre->value) ? 'selected' : '';
                                echo "<option value='{$value}' {$selected}>{$label}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="detail-label">在庫数</div>
                    <div class="detail-value">
                        <div class="form-input">
                            <input type="number" name="count" min="1" value="<?= htmlspecialchars($row['count'] ?? 1, ENT_QUOTES, 'UTF-8') ?>" class="number-input">
                        </div>
                    </div>

                    <div class="detail-label">補足</div>
                    <div class="detail-value">
                        <textarea name="hosoku"><?= htmlspecialchars($row['hosoku'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                    </div>
                </div>
            </form>
            <div class="rental-area">
                <button type="button" class="rental-btn" onclick="document.getElementById('dg').showModal()">編集を確定</button>
                <form action="" method="post">
                    <input type="hidden" name="b_id" value="<?= htmlspecialchars($b_id, ENT_QUOTES, 'UTF-8') ?>">
                    <input type="submit" value="リセット">
                </form>
                <button type="button" class="rental-btn" onclick="document.getElementById('dg2').showModal()">本を削除</button>
            </div>
        </div>

        <script>
             const values = <?php echo json_encode([$t, $g, $c], JSON_UNESCAPED_UNICODE); ?>;
               // 2. 一つでも空（null, undefined, 空文字, スペースのみ）があるか判定
             const hasEmpty = values.some(val => val === null || val === undefined || String(val).trim() === '');
             if (hasEmpty){
                <?php $er="1" ?>
             }
        </script>

        <dialog id="dg">
        <?php if(!empty($er)): ?>
            <p class="dialog-message" style="color: red;">補足 以外は必須項目です。</p>
            <button type="button" class="dialog-btn-no" onclick="document.getElementById('dg').close()">閉じる</button>
        <?php else: ?>
            <p class="dialog-message">編集を確定しますか？</p>
            <div class="detail-dialog-buttons">
                <button type="submit" form="edit-form" class="dialog-btn-yes">はい</button>
                <button type="button" class="dialog-btn-no" onclick="document.getElementById('dg').close()">いいえ</button>
            </div>
        <?php endif; ?>
        </dialog>

        <dialog id="dg2">
            <p class="dialog-message">この本を削除しますか？</p>
            <div class="detail-dialog-buttons">
                <form action="edit.php" method="post">
                    <input type="hidden" name="delete_id" value="<?= htmlspecialchars($b_id, ENT_QUOTES, 'UTF-8') ?>">
                    <button type="submit" class="dialog-btn-yes">はい</button>
                </form>
                <button type="button" class="dialog-btn-no" onclick="document.getElementById('dg2').close()">いいえ</button>
            </div>
        </dialog>
</body>

</html>