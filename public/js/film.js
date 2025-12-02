document.addEventListener('DOMContentLoaded', () => {
    // Rencup des elements
    const genreFilter = document.getElementById('genreFilter');
    const dateFilter = document.getElementById('dateFilter');
    const cinemaFilter = document.getElementById('cinemaFilter');
    const filmsContainer = document.getElementById('films-container');

    if (!filmsContainer) {
        console.warn('films-container introuvable');
        return;
    }

    // Recup mes card
    const filmCards = filmsContainer.getElementsByClassName('film-card');
    
    function applyFilters() {
    // Recup des valeur choisi par le user
        const selectedGenre = genreFilter.value;
        const selectedDate = dateFilter.value;
        const selectedCinema = cinemaFilter.value;

        Array.from(filmCards).forEach(card => {
            // On transforme les chaines de carractère en un tableau
            const genres = card.dataset.genres ? card.dataset.genres.split(',') : [];
            const seances = card.dataset.seances ? card.dataset.seances.split(',') : [];
            const cinemas = card.dataset.cinemas ? card.dataset.cinemas.split(',') : [];
            // Si il n'y a pas de filtre alors on affiche tout
            const matchGenre = !selectedGenre || genres.includes(selectedGenre);
            const matchDate = !selectedDate || seances.includes(selectedDate);
            const matchCinema = !selectedCinema || cinemas.includes(selectedCinema);

            if (matchGenre && matchDate && matchCinema) {
                // Si ca correspond alors on retire le display none sinon on le met 
                card.classList.remove('d-none');
            } else {
                card.classList.add('d-none');
            }
        });
    }
    // detecte les changements et on applique le filtre 
    genreFilter.addEventListener('change', applyFilters);
    dateFilter.addEventListener('change', applyFilters);
    cinemaFilter.addEventListener('change', applyFilters);

    applyFilters();
});
