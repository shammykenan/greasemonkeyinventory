// Session timeout script (15 minutes)
let timeoutDuration = 15 * 60 * 1000; // 15 minutes in ms
let inactivityTimer = setTimeout(autoLogout, timeoutDuration);

function resetTimer() {
    clearTimeout(inactivityTimer);
    inactivityTimer = setTimeout(autoLogout, timeoutDuration);
}

// Track user activity
window.onload = resetTimer;
document.onmousemove = resetTimer;
document.onkeypress = resetTimer;
document.onclick = resetTimer;
document.onscroll = resetTimer;
document.onwheel = resetTimer;
document.ontouchstart = resetTimer;

// Auto logout function
function autoLogout() {
    alert("Your session has expired due to inactivity.");
    window.location.href = "index.php?page=logout";
}