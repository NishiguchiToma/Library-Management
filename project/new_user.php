<?php require 'config/database.php'; ?>
<?php session_start(); ?>
<h1>新規登録</h1>
<form action="new_user.php" method="post">
    <p>学籍番号・教員番号(ID)<input type="text" name="id"></p>
    <p>パスワード<input type="text" name="pas"></p>
    <p>パスワード(再確認)<input type="text" name="pas2"></p>
    <p>名前<input type="text" name="name"></p>
    <p>メールアドレス<input type="text" name="address"></p>
    <input type="submit" value="新規登録">
</form>
<?php
$id=$_POST['id'];
$pas=$_POST['pas'];
$name=$_POST['name'];
$adr=$_POST['address'];
$pas2=$_POST['pas2'];
$role='';
$er=0;
$check = $pdo->prepare('SELECT COUNT(*) FROM users WHERE user_id = ?');
$check->execute([$id]);

if(!empty($id) && !empty($pas) && !empty($pas2)&& !empty($name)&& !empty($adr)){
    if($pas==$pas2){
        if($check->fetchColumn()==0){
            $ins=$pdo->prepare('insert into users (user_id, password,name,address,role) values (?, ?,?,?,?)');
            if(true){
                $role=true;
            }else{
                $role=false;
            }
            $ins->execute([$id,$pas,$name,$adr,$role]);
            echo "<script>
            alert('登録しました');
            window.location.href = 'index.php';
            </script>";
            exit;
        }else{
            $er="そのIDは既に登録されています";
        }
    }else{
        $er="確認用パスワードが一致しません";
    }
}else{
    $er="空欄を入力してください";
}

if($er!==0){
    echo $er;
}
?>

<p><a href="index.php">ログインはこちら</a></p>