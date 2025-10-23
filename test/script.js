function Affiche() {
    var identifiant = document.getElementById("identifiant").value.trim();
    var mdp = document.getElementById("mdp").value;

    if (!identifiant || !mdp) {
        alert("Veuillez remplir les deux champs.");
        return;
    }

    document.getElementById("id").innerHTML = "Identifiant : " + identifiant;
    document.getElementById("md_p").innerHTML = "Mot de passe : " + mdp;
    document.cookie = "id=" + encodeURIComponent(identifiant);
    document.cookie = "mdp=" + encodeURIComponent(mdp);

    document.forms["identification"].submit();
}