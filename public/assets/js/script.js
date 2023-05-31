// Sélectionner les options de couleur
var colorOptions = document.querySelectorAll('.color-option');

// Sélectionner les zones
var zones = document.querySelectorAll('path');

// Variable pour stocker la zone actuellement sélectionnée
var selectedZone = null;

// Ajouter un événement de clic à chaque option de couleur
colorOptions.forEach(function(option) {
  option.addEventListener('click', function() {
    // Récupérer la couleur de l'option sélectionnée
    var color = this.style.backgroundColor;    
    // Stocker la zone actuellement sélectionnée
    selectedZone = null;
    // Appliquer la couleur à la zone cliquée
    zones.forEach(function(zone) {
      zone.addEventListener('click', function() {
        // Stocker la zone actuellement sélectionnée
        selectedZone = zone;
        // Appliquer la couleur à la zone
        zone.style.fill = color;
      });
    });
  });
});
