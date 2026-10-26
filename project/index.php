<h1>ログイン</h1>
<form action="index.php" method="post">
    <p>学籍番号・教員番号(ID)<input type="text" name="id"></p>
    <p>パスワード<input type="text" name="pas"></p>
    <input type="submit" value="ログイン">
</form>
<a href="new_user.php">新規登録はこちら</a>
<?php
    $id=$_POST['id'];
    $pas=$_POST['pas'];
    $sql=$pdo->prepare('select*from users where id=? a')

?>