const deleteButton = document.getElementById("deleteButton");
const favoriteList = document.getElementById("favoriteList");
const message = document.getElementById("message");

deleteButton.addEventListener("click", function () {

const checkedBooks = favoriteList.querySelectorAll(
    'input[name="favorite"]:checked'
);

if (checkedBooks.length === 0) {
    message.textContent = "削除する本を選択してください。";
    return;
}

if (!confirm("選択した本を削除しますか？")) {
    return;
}

checkedBooks.forEach(function (checkbox) {
    checkbox.closest(".book").remove();
});

if (favoriteList.querySelectorAll(".book").length === 0) {
    message.textContent = "お気に入りの本はありません。";
} else {
    message.textContent = "削除しました。";
}

});
