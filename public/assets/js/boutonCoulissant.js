// Sélectionne l'élément avec la classe "switch-slider"
const switchSlider = document.querySelector(".switch-slider");
// Sélectionne le formulaire avec l'ID "visibilityForm"
const form = document.getElementById("visibilityForm");

// Fonction pour générer l'action du formulaire mise à jour avec la nouvelle visibilité
function generateUpdatedAction(visibility) {
  const currentAction = form.getAttribute("action");
  const updatedAction = currentAction.replace(
    /visibility=[^&]*/,
    `visibility=${visibility}`
  );
  return updatedAction;
}

// Ajoute un écouteur d'événement "change" au bouton de profil
document
  .getElementById("profileSwitch")
  .addEventListener("change", function () {
    // Détermine la visibilité en fonction de l'état du bouton
    const visibility = document.getElementById("profileSwitch").checked
      ? "visible"
      : "invisible";

    // Met à jour la valeur du paramètre "visibility" dans l'action du formulaire
    const updatedAction = generateUpdatedAction(visibility);
    form.setAttribute("action", updatedAction);

    // Envoie la donnée mise à jour au serveur via une requête Ajax
    const xhr = new XMLHttpRequest();
    xhr.open("POST", updatedAction);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
      if (xhr.readyState === XMLHttpRequest.DONE) {
        if (xhr.status === 200) {
          // Succès de la requête
          console.log("Donnée mise à jour et enregistrée avec succès.");
          const response = JSON.parse(xhr.responseText);

          if (response.result === "success") {
            // Affichez ici un message de succès ou effectuez toute autre action nécessaire
            console.log(
              "La visibilité du profil a été mise à jour avec succès."
            );
            // Redirigez l'utilisateur vers la page appropriée
            window.location.href = "/utilisateur/visibilite?result=success";
          } else {
            // Affichez ici un message d'erreur ou effectuez toute autre action nécessaire
            console.log(
              "Erreur lors de la mise à jour de la visibilité du profil."
            );
            // Redirigez l'utilisateur vers la page appropriée
            window.location.href = "/utilisateur/visibilite?result=error";
          }
        }
      }
    };
    console.log("Envoi de la donnée mise à jour au serveur...");
    xhr.send("visibility=" + visibility);
  });
