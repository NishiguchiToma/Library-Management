const deleteButton = document.getElementById("deleteButton");
const favoriteList = document.getElementById("favoriteList");
const message = document.getElementById("message");

deleteButton.addEventListener("click", function () {
    const checkedBooks = favoriteList.querySelectorAll(
        'input[name="favorite[]"]:checked'
    );

    if (checkedBooks.length === 0) {
        message.textContent = "削除する本を選択してください。";
        return;
    }

    if (!confirm("選択した本を削除しますか？")) {
        return;
    }

    const favoriteIds = Array.from(checkedBooks).map(cb => cb.value);

    // 自分自身のPHPファイル（location.href）へ送信
    fetch(location.href, {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({ favorite_ids: favoriteIds })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            checkedBooks.forEach(function (checkbox) {
                checkbox.closest(".book").remove();
            });

            if (favoriteList.querySelectorAll(".book").length === 0) {
                message.textContent = "お気に入りの本はありません。";
            } else {
                message.textContent = "削除しました。";
            }
        } else {
            message.textContent = data.error || "削除に失敗しました。";
        }
    })
    .catch(error => {
        console.error("通信エラー:", error);
        message.textContent = "サーバーとの通信に失敗しました。";
    });
});
