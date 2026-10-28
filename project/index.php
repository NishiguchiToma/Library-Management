<?php require 'config/database.php'; ?>
<?php session_start(); ?>

<?php
$er="";
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    unset($_SESSION['use']);
    $id=$_POST['id'];
    $pas=$_POST['pas'];
    $sql=$pdo->prepare('select*from users where user_id=? and password=?');
    $sql->execute([$id,$pas]);
    foreach($sql as $row){
        $_SESSION['use']=[
            'id'=>$row['user_id'],'pas'=>$row['password'],'name'=>$row['name'],'adr'=>$row['address'],'role'=>$row['role']
        ];
    }
    if(isset($_SESSION['use'])){
        header('Location: home.php');
        exit;
    }else{
        $er="IDまたはパスワードが違います";
    }
}
?>

<h1>ログイン</h1>
<form action="" method="post">
    <p>学籍番号・教員番号(ID)<input type="text" name="id"></p>
    <p>パスワード<input type="text" name="pas" onfocus="this.type='password'"></p>
    <input type="submit" value="ログイン">
</form>
<?PHP
if ($er!=="") {
    echo $er;
}
?>
<p><a href="new_user.php">新規登録はこちら</a></p>
