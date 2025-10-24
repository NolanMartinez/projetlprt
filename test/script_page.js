function deco() {
    fetch('logout.php')
        .then(() => {
            window.location.href = 'index.php';
        });
}