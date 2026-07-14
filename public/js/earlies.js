function finish(game) {
    const time_elapsed = stopTimer();

    toggleBigLoader();
    fetch(`/api/game-stats/finish`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
        },
        body: JSON.stringify({
            game: game,
            time: time_elapsed,
        }),
    })
        .then(res => res.json())
        .then(res => {
            openModalManuall("Gratulacje!", res.modal);
        });
}

function log(card, target) {
    window.gameHistory.push({
        card: card,
        from: card.closest(".card-tray"),
        to: target,
    });
}

// #region card selectors
function getAllCards() {
    return document.querySelectorAll('#playmat .playing-card');
}

function getTopCardFromStack(stack) {
    if (!stack) return null;
    if (stack.children.length === 0) return null;
    return stack.children[stack.children.length - 1];
}

function getCardValue(card, mode = "standard") {
    if (!card) return null;
    const data = card.dataset.value.split("-");

    return {
        color: data[0].charCodeAt(0) - 64,
        rank: parseInt(data[1])
            - (mode === "standard" && data[1] > 10), // standardowa talia korzysta z N (#12) jako J, więc face'y mają za duży indeks
    };
}
// #endregion

// #region dragging
function grabCard(ev) {
    const card = ev.target.closest(".playing-card");
    ev.dataTransfer.setData("card", card.id);
}
// #endregion

// #region visual cleanup
function cleanUpStack(stack) {
    if (!stack) return;

    Array.from(stack.children).forEach((card, i) => {
        card.style.transform = (stack.classList.contains("compact"))
            ? null
            : `translateY(${i * 35}px)`;
    });
}
// #endregion
