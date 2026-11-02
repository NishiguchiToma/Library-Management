<?php session_start(); ?>
<?php require 'config/database.php'; ?>
<?php require 'menu.php'; ?>

<tr>
<?php
foreach($pdo->query('select*from book') as $row){
    $img=base64_encode($row['book_image']);
    echo "<td> 
    <p><img src='data:image/png;base64,{$img}' alt='book image' style='height: 200px; width: auto;'></p>
    <p> {$row['title']} </p>
    </td>
    ";
}
?>
</tr>