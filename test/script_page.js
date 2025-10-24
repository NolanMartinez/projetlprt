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
>>>>>>> parent of 67b6c05 (a supprimer)
}