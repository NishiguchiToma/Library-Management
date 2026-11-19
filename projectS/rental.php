<?php session_start(); ?>
<?php require 'config/database.php'; ?>
<?php
$b_id=$_POST['i'];
$sql=$pdo->prepare('update book set count = count - 1 WHERE book_id = ?');
$sql->execute([$b_id]);
$u_id=$_SESSION['use']['id'];
$r_day = date('Y-m-d'); 
$yotei = date('Y-m-d', strtotime('+14 days')); 

$sql2 = $pdo->prepare('INSERT INTO lending (book_id, user_id, rental_day, return_day, memo, yotei) VALUES (?, ?, ?, NULL, NULL, ?)');
$sql2->execute([$b_id, $u_id, $r_day,$yotei]);
echo "<script>
        alert('貸し出しが完了しました');
        window.location.href = 'home.php';
      </script>";
exit;
?>