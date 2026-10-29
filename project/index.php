<?php require 'config/database.php'; ?>
<?php session_start(); ?>

<?php
$er="";
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    unset($_SESSION['use']);
    $id=$_POST['id'];
    $pas=$_POST['pas'];
    $sql = $pdo->prepare('select * from users where user_id = ?');
    $sql->execute([$id]);
    $row = $sql->fetch();
    if ($row && password_verify($pas, $row['password'])){
        session_regenerate_id(true);
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
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ログイン</title>
    <link rel="stylesheet" href="css/index.css">
</head>
<body>

<h1>ログイン</h1>

<?PHP
if ($er !== "") {
    echo '<p style="color: red; text-align: center; font-weight: bold; margin-bottom: 20px;">' . htmlspecialchars($er, ENT_QUOTES, 'UTF-8') . '</p>';
}
?>

<form action="" method="post">
    <p>学籍番号・教員番号(ID)<br><input type="text" name="id"></p>
    <p>パスワード<br><input type="text" name="pas" onfocus="this.type='password'"></p>
    <input type="submit" value="ログイン">
</form>

<p><a href="new_user.php">新規登録はこちら</a></p>

</body>
</html>