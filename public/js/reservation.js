function updateSeanceDisplay() {
    const numPersons = parseInt(document.getElementById('reservation_numPersons').value) || 0;
    const filmFilter = document.getElementById('filmFilter').value.toLowerCase();
    const dateFilter = document.getElementById('dateFilter').value;
    const seances = document.querySelectorAll('.seance-card');

    seances.forEach(card => {
        const filmTitle = card.getAttribute('data-film').toLowerCase();
        const seanceDate = card.getAttribute('data-date');
        const places = parseInt(card.getAttribute('data-places')) || 0;

        const matchesFilm = filmFilter === "" || filmTitle.includes(filmFilter);
        const matchesDate = dateFilter === "" || seanceDate === dateFilter;
        const hasEnoughSeats = numPersons <= 0 || places >= numPersons;

        const shouldShow = matchesFilm && matchesDate && hasEnoughSeats;

        if (shouldShow) {
            card.style.display = 'block';
            card.classList.remove('disabled');
            card.style.pointerEvents = 'auto';
            card.style.opacity = '1';
        } else {
            card.style.display = 'none';
            card.classList.add('disabled');
            card.style.pointerEvents = 'none';
            card.style.opacity = '0.5';
        }
    });

    document.querySelectorAll('.seance-card .card').forEach(card => card.classList.remove('selected'));
    document.getElementById('reservation_seance').value = '';
    document.getElementById('reservation_seats').value = '';
    document.getElementById('seatSelection').innerHTML = '';
    document.getElementById('reserve_button').style.display = 'none';
    document.getElementById('placesAvailableDisplay').textContent = '0';
}
	
	        function toggleSelection(card) {
	            const numPersons = document.getElementById('reservation_numPersons').value;
	            if (!numPersons || numPersons <= 0) {
	                alert("Veuillez d'abord saisir le nombre de personnes.");
	                return;
	            }
	
	            const placesAvailable = parseInt(card.getAttribute('data-places')) || 0;
	            if (placesAvailable <= 0) {
	                alert("Aucune place disponible pour cette séance.");
	                return;
	            }
	
	            // Vérifier si l'élément reservation_seance existe
	            const selectedSeancesInput = document.getElementById('reservation_seance');
	            if (!selectedSeancesInput) {
	                console.error("Champ 'reservation_seance' introuvable.");
	                alert("Erreur : impossible de sélectionner une séance. Veuillez recharger la page.");
	                return;
	            }
	
	            // Désélectionner toute autre séance
	            document.querySelectorAll('.seance-card .card').forEach(c => c.classList.remove('selected'));
	            card.querySelector('.card').classList.add('selected');
	
	            const seanceId = card.getAttribute('data-id');
	            const reservedSeats = JSON.parse(card.getAttribute('data-reserved-seats') || '[]');
	
	            // Définir la valeur pour l'input hidden
	            selectedSeancesInput.value = seanceId;
	
	            updatePlacesAvailable(placesAvailable, reservedSeats);
	
	            document.getElementById('reserve_button').style.display = 'block';
	        }
	
	        function updatePlacesAvailable(places, reservedSeats) {
	            const display = document.getElementById('placesAvailableDisplay');
	            const seatSelection = document.getElementById('seatSelection');
	            const numPersons = parseInt(document.getElementById('reservation_numPersons').value) || 0;
	
	            display.textContent = places;
	            seatSelection.innerHTML = '';
	
	            if (places > 0 && places >= numPersons) {
	                const seatsPerRow = 8;
	                const alphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	                const totalSeats = places + reservedSeats.length;
	
	                for (let i = 0; i < totalSeats; i++) {
	                    const row = Math.floor(i / seatsPerRow);
	                    const col = (i % seatsPerRow) + 1;
	                    const seatLabel = alphabet[row] + col;
	
	                    const seatWrapper = document.createElement('div');
	                    seatWrapper.style.display = 'inline-block';
	                    seatWrapper.style.margin = '5px';
	                    seatWrapper.style.textAlign = 'center';
	
	                    const seat = document.createElement('input');
	                    seat.type = 'checkbox';
	                    seat.id = `seat${seatLabel}`;
	                    seat.name = 'seats[]';
	                    seat.value = seatLabel;
	                    seat.style.width = '20px';
	                    seat.style.height = '20px';
	
	                    // Si la place est réservée, la désactiver et la mettre en rouge
	                    if (reservedSeats.includes(seatLabel)) {
	                        seat.disabled = true;
	                        seat.style.backgroundColor = 'red';
	                        seat.style.cursor = 'not-allowed';
	                    }
	
	                    const label = document.createElement('label');
	                    label.setAttribute('for', `seat${seatLabel}`);
	                    label.textContent = seatLabel;
	                    label.style.display = 'block';
	                    label.style.fontSize = '12px';
	
	                    seatWrapper.appendChild(seat);
	                    seatWrapper.appendChild(label);
	                    seatSelection.appendChild(seatWrapper);
	
	                    if ((i + 1) % seatsPerRow === 0) {
	                        seatSelection.appendChild(document.createElement('br'));
	                    }
	                }
	                addSeatSelectionLimit();
	            } else {
	                seatSelection.innerHTML = '<p>Aucune place disponible ou nombre de personnes trop élevé.</p>';
	            }
	        }
	
	        function addSeatSelectionLimit() {
	            const numPersons = parseInt(document.getElementById('reservation_numPersons').value) || 0;
	            const seats = document.querySelectorAll('input[name="seats[]"]');
	
	            seats.forEach(seat => {
	                seat.addEventListener('change', function () {
	                    const selectedSeats = document.querySelectorAll('input[name="seats[]"]:checked');
	                    if (selectedSeats.length > numPersons) {
	                        this.checked = false;
	                        alert(`Vous ne pouvez choisir que ${numPersons} siège(s).`);
	                    }
	                });
	            });
	        }
	
	        document.addEventListener('DOMContentLoaded', function () {
	            const form = document.getElementById('reservationForm');
	            if (!form) {
	                console.error("Formulaire 'reservationForm' introuvable.");
	                return;
	            }
	
	            form.addEventListener('submit', function (e) {
	                const selectedSeats = document.querySelectorAll('input[name="seats[]"]:checked');
	                const numPersons = parseInt(document.getElementById('reservation_numPersons').value) || 0;
	
	                if (selectedSeats.length !== numPersons) {
	                    e.preventDefault();
	                    alert(`Veuillez sélectionner exactement ${numPersons} siège(s).`);
	                    return;
	                }
	
	                const seatsHiddenField = document.getElementById('reservation_seats');
	                const selectedSeatsArray = Array.from(selectedSeats).map(cb => cb.value);
	                seatsHiddenField.value = JSON.stringify(selectedSeatsArray);
	
	                console.log("Données envoyées:", {
	                    seance: document.getElementById('reservation_seance').value,
	                    seats: seatsHiddenField.value,
	                    numPersons: numPersons
	                });
	            });
	
	            // Appliquer les filtres au chargement
	            filterSeances();
	
	            
	        });
	
	        function filterSeances() {
	            const filmFilter = document.getElementById('filmFilter').value.toLowerCase();
	            const dateFilter = document.getElementById('dateFilter').value;
	            const seances = document.querySelectorAll('.seance-card');
	
	            seances.forEach(seance => {
	                const filmTitle = seance.getAttribute('data-film').toLowerCase();
	                const seanceDate = seance.getAttribute('data-date');
	
	                const matchesFilm = filmFilter === "" || filmTitle.includes(filmFilter);
	                const matchesDate = dateFilter === "" || seanceDate === dateFilter;
	
	                if (matchesFilm && matchesDate) {
	                    seance.style.display = 'block';
	                } else {
	                    seance.style.display = 'none';
	                }
	            });
	        }