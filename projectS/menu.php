<link rel="stylesheet" href="css/menu.css">
<header class="header">
        <h1>
            <a href="home.php">図書管理システム</a>
            <button id="openBtn" style="font-size: 28px; background: none; border: none; cursor: pointer;">
                ☰
            </button>
        </h1>
        <dialog id="myDialog">
                <a href="home.php">menu</a><button id="closeBtn">×</button>
                <div><a href="confirm.php">貸出確認</a></div>
                <div><a href="favorite.php">お気に入り</a></div>
                <div><a href="expired.php">期限切れ</a></div>
                <div><a href="new-book.php">本登録</a></div>
                <div><a href="history.php">履歴</a></div>
                <div><a href="logout.php">ログアウト</a></div>
        </dialog>
</header>

<script>
  const dialog = document.getElementById('myDialog');
  const openBtn = document.getElementById('openBtn');
  const closeBtn = document.getElementById('closeBtn');

  // ボタンを押したらポップアップを表示
  openBtn.addEventListener('click', () => {
    dialog.showModal();
  });

  // 閉じるボタンを押したら非表示
  closeBtn.addEventListener('click', () => {
    dialog.close();
  });
</script>