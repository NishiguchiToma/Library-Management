<?php session_start(); ?>
<?php require 'config/database.php'; ?>
<?php require 'menu.php'; ?>
<?php
$b_id=$_GET['id']?? '';
$u_id=$_SESSION['use']['id'];

$f_user=$_POST['f_u']?? '';
$f_book=$_POST['f_b']?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($f_user) && !empty($f_book)) {
    $check = $pdo->prepare('SELECT * FROM favorite WHERE book_id = ? AND user_id = ?');
    $check->execute([$f_book, $f_user]);

if ($check->fetch()) {
    $sql2 = 'DELETE FROM favorite WHERE user_id = ? AND book_id = ?';
} else {
    $sql2 = 'INSERT INTO favorite (user_id, book_id) VALUES (?, ?)';
}
$stmt = $pdo->prepare($sql2);
$stmt->execute([$f_user, $f_book]);
header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . urlencode($b_id));
exit;
}

$sql = $pdo->prepare('SELECT * FROM book WHERE book_id = ?');
$sql->execute([$b_id]);
$row = $sql->fetch();

$star="☆";
$sq=$pdo->prepare('select*from favorite where book_id=? and user_id=?');
$sq->execute([$b_id,$u_id]);
$ro = $sq->fetch(PDO::FETCH_ASSOC);
if($ro){
    $star="★";
}

$img=base64_encode($row['book_image']);
$c=$row['count'];
echo "
<div>
    <div>
        <form action='' method='post'>
            <input type='hidden' name='f_u' value='{$u_id}'>
            <input type='hidden' name='f_b' value='{$b_id}'>
            <input type='submit' value='{$star}'>
        </form>
        <img src='data:image/png;base64,{$img}' alt='book image' style='height: 200px; width: auto;'>
    </div>
    <h2>タイトル　{$row['title']}</h2>
    <h2>ジャンル　{$row['genre']}</h2>
    <h2>在庫数　{$row['count']}</h2>
    <h2>補足　{$row['hosoku']}</h2>
</div>
";
?>
 <button onclick="document.getElementById('dg').showModal()">借りる</button>
 <dialog id="dg">
    <?php if ($c == 0): ?>
        <p style="color: red; font-weight: bold;">この本は在庫がありません</p>
        <button type="button" onclick="document.getElementById('dg').close()">閉じる</button>
    <?php else: ?>
    <form method="POST" action="rental.php">
        <p>この本を借りますか</p>
        <input type="hidden" name="i" value="<?= htmlspecialchars($b_id, ENT_QUOTES, 'UTF-8') ?>">
        <button type="submit">はい</button>
        <button type="button" onclick="document.getElementById('dg').close()">いいえ</button>
    </form>
     <?php endif; ?>
 </dialog>