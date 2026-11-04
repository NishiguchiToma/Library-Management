<?php session_start(); ?>
<?php require 'config/database.php'; ?>
<?php require 'menu.php'; ?>
<?php
$word=$_POST['word'];
$if=0;
?>
<form action="" method="post">
    <input type="submit" value="🔍">
    <input type="text" placeholder="検索" name="word">
</form>

<tr>
<?php
if($word==''){
    foreach($pdo->query('select*from book') as $row){
        $img=base64_encode($row['book_image']);
        echo "<td> 
        <p><img src='data:image/png;base64,{$img}' alt='book image' style='height: 200px; width: auto;'></p>
        <p> {$row['title']} </p>
        </td>
        ";
    }
    echo "</tr>";
}else{
    $sql=$pdo->prepare('select*from book where title like ?');
    $sql->execute(['%'.$word.'%']);
    foreach($sql as $row){
        $if=1;
        $img=base64_encode($row['book_image']);
        echo "<td> 
        <p><img src='data:image/png;base64,{$img}' alt='book image' style='height: 200px; width: auto;'></p>
        <p> {$row['title']} </p>
        </td>
        ";
    }
    if($if==0){
        echo "<h1>該当なし</h1>";
    }
}
?>
</tr>