function deco() {
    fetch('logout.php', { method: 'GET' })
        .then(() => {
            window.location.href = 'index.php';
        })
        .catch(() => {
            window.location.href = 'index.php';
        });
}