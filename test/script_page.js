function deco() {
<<<<<<< HEAD
    fetch('logout.php')
        .then(() => {
            window.location.href = 'index.php';
        });
=======
    document.cookie = "id=";
    document.cookie = "mdp=;";
    window.location.reload();
>>>>>>> f673efc6a6483eb3044459f0302741a059368986
}