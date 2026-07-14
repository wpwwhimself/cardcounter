<span id="timer">0:00:00</span>

<script>
const timer = document.getElementById("timer");
let seconds_elapsed = 0;
let timer_interval = null;

function startTimer() {
    timer_interval = setInterval(updateTimer, 1000);
}

function stopTimer() {
    clearInterval(timer_interval);
    return getTimer();
}

function updateTimer() {
    seconds_elapsed += 1;
    let seconds = seconds_elapsed;
    let minutes = Math.floor(seconds / 60);
    seconds = seconds % 60;
    let hours = Math.floor(minutes / 60);
    minutes = minutes % 60;
    timer.innerText = `${hours}:${minutes.toString().padStart(2, "0")}:${seconds.toString().padStart(2, "0")}`;
}

function getTimer() {
    return timer.innerText;
}
</script>