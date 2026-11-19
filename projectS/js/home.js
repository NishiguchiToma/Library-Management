document.addEventListener('DOMContentLoaded', function() {
    const filterBtn = document.getElementById('filterBtn');
    const genreDropdown = document.getElementById('genreDropdown');
    const genreInput = document.getElementById('genreInput');
    const searchForm = document.getElementById('searchForm');

    if (filterBtn && genreDropdown) {
        // ▼ボタンを押したときの開閉処理
        filterBtn.addEventListener('click', function(e){
            e.stopPropagation();
            if(genreDropdown.style.display === 'none' || genreDropdown.style.display === ''){
                genreDropdown.style.display = 'block';
            } else {
                genreDropdown.style.display = 'none';
            }
        });

        // 各選択肢をクリックしたとき
        const options = genreDropdown.querySelectorAll('.genre-option');
        options.forEach(function(option) {
            option.addEventListener('click', function() {
                const val = this.getAttribute('data-value');
                genreInput.value = val;
                searchForm.submit(); // 選択した瞬間に送信され、メニューが消える
            });
        });

        // メニュー以外の場所をクリックしたら閉じる
        document.addEventListener('click', function(e){
            if(!genreDropdown.contains(e.target) && e.target !== filterBtn){
                genreDropdown.style.display = 'none';
            }
        });
    }
});