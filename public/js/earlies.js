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
    ev.dataTransfer.setData("card", card.dataset.value);
}
// #endregion

// #region visual cleanup
function spreadStack(stack) {
    if (!stack) return;

    Array.from(stack.children).forEach((card, i) => {
        card.style.transform = `translateY(${i * 22}px)`
    });
}
// #endregion
