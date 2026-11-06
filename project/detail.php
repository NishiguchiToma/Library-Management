<?php session_start(); ?>
<?php require 'config/database.php'; ?>
<?php require 'menu.php'; ?>
<?php
$i=$_POST['id']?? '';
    $i=1;
$sql=$pdo->prepare('select*from book where book_id=?');
$sql->execute([$i]);
$row = $sql->fetch();
$img=base64_encode($row['book_image']);
$c=$row['count'];
echo "
<div>
    <p>
        <img src='data:image/png;base64,{$img}' alt='book image' style='height: 200px; width: auto;'>
    </p>
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
        <input type="hidden" name="i" value="<?= htmlspecialchars($i, ENT_QUOTES, 'UTF-8') ?>">
        <button type="submit">はい</button>
        <button type="button" onclick="document.getElementById('dg').close()">いいえ</button>
    </form>
     <?php endif; ?>
 </dialog>