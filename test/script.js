function Affiche() {
    var identifiant = document.getElementById("identifiant").value.trim();
    var mdp = document.getElementById("mdp").value;

    if (!identifiant || !mdp) {
        alert("Veuillez remplir les deux champs.");
        return;
    }


    document.cookie = "id=" + encodeURIComponent(identifiant);
    document.cookie = "mdp=" + encodeURIComponent(mdp);

    document.forms["identification"].submit();
}