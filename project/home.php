<?php session_start(); ?>
<?php require 'config/database.php'; ?>
<?php require 'menu.php'; ?>
<link rel="stylesheet" href="css/home.css">
<!-- JavaScriptファイルを外部読み込み -->
<script src="js/home.js" defer></script>
<?php
$word = $_POST['word'] ?? '';
$if = 0;
$s_genre = $_POST['genre'] ?? ''; 

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

<!-- 検索・ジャンル絞り込みフォーム -->
<form action="" method="post" class="search-form-container" id="searchForm">
    <div class="search-bar">
        <!-- 検索入力欄 -->
        <input type="text" placeholder="検索" name="word" value="<?php echo htmlspecialchars($word, ENT_QUOTES, 'UTF-8'); ?>">
        <button type="submit" style="display:none;"></button>
        <!-- ↑透明なボタンにすることでエンターをクリックしたら検索が実行されるようにしてる -->
        
        <!-- 絞り込み用 ▼ ボタン -->
        <button type="button" id="filterBtn" class="filter-toggle-btn">▼</button>
        
        <!-- 選択されたジャンルを保持する隠しフィールド -->
        <input type="hidden" name="genre" id="genreInput" value="<?php echo htmlspecialchars($s_genre, ENT_QUOTES, 'UTF-8'); ?>">
        
        <!-- ▼を押したとき、一番上に「指定なし」を追加したカスタムメニュー -->
        <div id="genreDropdown" class="custom-genre-dropdown" style="display: none;">
            <?php
                // 一番上に「指定なし（絞り込み解除）」の項目を配置
                $active_all = ($s_genre === '') ? ' active' : '';
                echo '<div class="genre-option' . $active_all . '" data-value="">指定なし</div>';

                // 各ジャンルのループ
                foreach($genre as $g){
                    $active = ($s_genre === $g->value) ? ' active' : '';
                    echo '<div class="genre-option' . $active . '" data-value="' . htmlspecialchars($g->value, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($g->label(), ENT_QUOTES, 'UTF-8') . '</div>';
                }
            ?>
        </div>
    </div>
</form>

<!-- 本棚のグリッド表示エリア -->
<div class="b1">
<?php
$star = $star ?? '';
if($word == ''){
    if($s_genre == ''){
        $sql = $pdo->query('select * from book');
    }else{
        $sql = $pdo->prepare('select * from book where genre = ?');
        $sql->execute([$s_genre]);
    }

    foreach($sql as $row){
        $if = 1;
        $img = base64_encode($row['book_image']);
        echo "
        <div class='b2'>
            <a href='detail.php?id={$row['book_id']}'>
                <div class='image-container'>
                    <p class='star'>
                        {$star}
                    </p>
                    <img src='data:image/png;base64,{$img}' alt='book image'>
                </div>
                <p>{$row['title']}</p>
            </a>
        </div>";
    }

    if($if == 0){
        echo "<h1 class='no-result'>該当なし</h1>";
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
        echo "
        <div class='b2'>
            <a href='detail.php?id={$row['book_id']}'>
                <div class='image-container'>
                    <img src='data:image/png;base64,{$img}' alt='book image'>
                </div>
                <p>{$row['title']}</p>
            </a>
        </div>";
    }
    
    if($if == 0){
        echo "<h1 class='no-result'>該当なし</h1>";
    }
}
?>
</div>