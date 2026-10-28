// ハンバーガーメニュー
const menuButton = document.getElementById("menuButton");
const navigation = document.getElementById("navigation");

menuButton.addEventListener("click", function () {

    // メニューの表示・非表示を切り替える
    navigation.classList.toggle("open");

    // メニューが開いているか確認する
    const isOpen = navigation.classList.contains("open");

    // アクセシビリティ用の状態を更新する
    menuButton.setAttribute("aria-expanded", isOpen);

});


// 削除ボタン
const deleteButton = document.getElementById("deleteButton");
const favoriteList = document.getElementById("favoriteList");
const message = document.getElementById("message");

deleteButton.addEventListener("click", function () {

    // チェックされている本を取得する
    const checkedBooks = favoriteList.querySelectorAll(
        'input[name="favorite"]:checked'
    );

    // 何も選択されていない場合
    if (checkedBooks.length === 0) {
        message.textContent = "削除する本を選択してください。";
        return;
    }

    // 削除する本を確認する
    const result = confirm(
        "選択した本をお気に入りから削除しますか？"
    );

    // キャンセルされた場合
    if (!result) {
        return;
    }

    // 選択した本を削除する
    checkedBooks.forEach(function (checkbox) {
        checkbox.closest(".book").remove();
    });

    // 削除完了メッセージ
    message.textContent = "選択した本を削除しました。";

    // 本が0冊になった場合
    if (favoriteList.querySelectorAll(".book").length === 0) {
        message.textContent = "お気に入りの本はありません。";
    }

});