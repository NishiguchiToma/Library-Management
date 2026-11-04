<?php session_start(); ?>
<?php require 'config/database.php'; ?>
<?php require 'menu.php'; ?>
<?php
$word = $_POST['word'] ?? '';
$if=0;
$s_genre= $_POST['genre'] ?? ''; 
enum Genre: string
{
    case Php = 'php';
    case Java = 'java';
    case Javascript = 'javascript';
    case Python = 'python';
    case HtmlCss = 'html css';
    case FundamentalInfo = '基本情報';
    case Other = 'その他';
    public function label(): string
    {
        return match($this) {
            self::Php => 'PHP',
            self::Java => 'Java',
            self::Javascript => 'JavaScript',
            self::Python => 'Python',
            self::HtmlCss => 'HTML/CSS',
            self::FundamentalInfo => '基本情報',
            self::Other => 'その他',
        };
    }
}
$genre = Genre::cases();
?>
<style>
    #text{
        display: none;
    }
</style>
<form action="" method="post">
    <input type="submit" value="🔍">
    <input type="text" placeholder="検索" name="word">
    <button type="button" id="btn">▼</button>

    <div id="text" style="display: <?php if ($s_genre !== '') { echo 'block'; } else { echo 'none'; } ?>;">
        <label>
            <select name="genre">
                <option value="">指定なし</option>
                <?php
                    foreach($genre as $g){
                        $selected = ($s_genre === $g->value) ? 'selected' : '';
                        echo "
                        <option value='{$g->value}' {$selected}>
                             {$g->label()}
                        </option>
                        ";
                    }
                ?>
            </select>
        </label>
    </div>

</form>

<script>
    const button = document.getElementById('btn');
    const text = document.getElementById('text');

    button.addEventListener('click', function(){
        if(text.style.display === 'none' || text.style.display === ''){
            text.style.display = 'block'; 
        }else{
             text.style.display = 'none';
        }
    })
</script>




<tr>
<?php
if($word==''){
    if($s_genre == ''){
        $sql = $pdo->query('select * from book');
    }else{
        $sql = $pdo->prepare('select * from book where genre = ?');
        $sql->execute([$s_genre]);
    }

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
}else{
    if($s_genre == ''){
        $sql = $pdo->prepare('select * from book where title like ?');
        $sql->execute(['%'.$word.'%']);
    }else{
        $sql = $pdo->prepare('select * from book where title like ? and genre = ?');
        $sql->execute(['%'.$word.'%', $s_genre]);
    }

    foreach($sql as $row){
        $if = 1;
        $img = base64_encode($row['book_image']);
        echo "<td> 
        <p><img src='data:image/png;base64,{$img}' alt='book image' style='height: 200px; width: auto;'></p>
        <p> {$row['title']} </p>
        </td>";
    }
    
    if($if == 0){
        echo "<td><h1>該当なし</h1></td>";
    }
}
?>
</tr>