function commit() {
    document.forms["identification"].submit();
    window.location.reload();
}
function cookie_session(id_compte) {
    document.cookie = "cookie_session=" + encodeURIComponent(id_compte);
    window.location.href="page.php";
}