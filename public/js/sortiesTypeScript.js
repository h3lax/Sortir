const url = document.currentScript.getAttribute('data-url');
function updateLieux(selectedVilleId) {
    return new Promise(function (resolve, reject) {
        fetch(url+'lieux/' + selectedVilleId)
            .then(response => response.json())
            .then(data => {
                // Injection des données dans le menu déroulant
                var lieuxSelect = document.getElementById('sortie_lieu');
                lieuxSelect.innerHTML = '';

                data.forEach(function (lieu) {
                    var option = document.createElement('option');
                    option.value = lieu.id;
                    option.text = lieu.nom;
                    lieuxSelect.appendChild(option);
                });
                resolve();
            })
            .catch(function (error){
                console.error('Erreur lors de la récupération des lieux :', error);
                reject(error);
            });
    });
}

function updateCoord(selectedLieuId){
    fetch(url+'coordonnees/'+ selectedLieuId)
        .then(response => response.json())
        .then(data => {

            var lattitudeSelect = document.getElementById('sortie_latitude');
            var longitudeSelect = document.getElementById('sortie_longitude');
            lattitudeSelect.value = data.latitude;
            longitudeSelect.value = data.longitude;
        })
        .catch(error => {
            console.error('Erreur lors de la récupération des coordonées :', error);
        });
    document.querySelectorAll('.hidden-coord').forEach(function (show){
        show.style.display = 'inline-block';
    });
}

addEventListener('DOMContentLoaded', function() {
    var selectedVilleId = document.getElementById('sortie_ville').value;
    updateLieux(selectedVilleId)
        .then(function(){
            updateCoord(document.getElementById('sortie_lieu').value);
        })
        .catch(function (error){
            console.error('Erreur lors de la récupération des lieux :', error);
        })
});

document.getElementById('sortie_ville').addEventListener('change', function() {
    var selectedVilleId = this.value;
    updateLieux(selectedVilleId)
        .then(function(){
            updateCoord(document.getElementById('sortie_lieu').value);
        })
        .catch(function (error){
            console.error('Erreur lors de la récupération des lieux :', error);
        })
});

document.getElementById('sortie_lieu').addEventListener('change', function (){
    var selectedLieuId = this.value;
    updateCoord(selectedLieuId);
});