// #region card selectors
function getAllCards() {
    return document.querySelectorAll('#playmat .playing-card');
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
