function Affiche(){
			var identifiant = document.getElementById("identifiant").value;
			var mdp = document.getElementById("mdp").value;
			document.getElementById("id").innerHTML = "Identifiant : " + identifiant;
			document.getElementById("md_p").innerHTML = "Mot de passe : " + mdp;
			document.cookie = "id = " + identifiant;
			document.cookie = "mdp = " + mdp;
			document.forms["identification"].submit();
			window.location.reload();
		}