<?php require 'config/database.php'; ?>
<?php session_start(); ?>
<?php
$pattern = '/\A(?=.*?[a-zA-Z])(?=.*?\d)[a-zA-Z\d]{8,}\z/';
?>
<head>
    <link rel="stylesheet" href="css/new_user.css">
</head>
<h1>新規登録</h1>
<form action="new_user.php" method="post">
    <p>学籍番号・教員番号(ID)<input type="text" name="id"></p>
    <p>パスワード<input type="text" name="pas" onfocus="this.type='password'">　（8桁以上・アルファベットと数字を含む）</p>
    <p>パスワード(再確認)<input type="text" name="pas2" onfocus="this.type='password'"></p>
    <p>名前<input type="text" name="name"></p>
    <p>メールアドレス<input type="text" name="address"></p>
    <input type="submit" value="新規登録">
</form>
<?php
if($_SERVER['REQUEST_METHOD'] === 'POST'){

$id=$_POST['id'];
$pas=$_POST['pas'];
$name=$_POST['name'];
$adr=$_POST['address'];
$pas2=$_POST['pas2'];
$role='';
$er="";
$check = $pdo->prepare('SELECT COUNT(*) FROM users WHERE user_id = ?');
$check->execute([$id]);
    if(!empty($id) && !empty($pas) && !empty($pas2)&& !empty($name)&& !empty($adr)){
        if(preg_match($pattern, $pas)){
        if($pas==$pas2){
            if($check->fetchColumn()==0){
                $ins=$pdo->prepare('insert into users (user_id, password,name,address,role) values (?, ?,?,?,?)');
                if(true){
                    $role=true;
                }else{
                    $role=false;
                }
                $h_pas = password_hash($pas, PASSWORD_DEFAULT);
                $ins->execute([$id,$h_pas,$name,$adr,$role]);
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
            echo "パスワードの条件を満たしていません";
        }
    }else{
        $er="空欄を入力してください";
    }
    echo $er;
}
?>

<p><a href="index.php">ログインはこちら</a></p>
