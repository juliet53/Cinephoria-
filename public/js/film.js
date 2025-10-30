document.addEventListener('DOMContentLoaded', () => {
    const genreFilter = document.getElementById('genreFilter');
    const dateFilter = document.getElementById('dateFilter');
    const cinemaFilter = document.getElementById('cinemaFilter');
    const filmsContainer = document.getElementById('films-container');

    if (!filmsContainer) {
        console.warn('films-container introuvable');
        return;
    }

    const filmCards = filmsContainer.getElementsByClassName('film-card');

    function applyFilters() {
        const selectedGenre = genreFilter.value;
        const selectedDate = dateFilter.value;
        const selectedCinema = cinemaFilter.value;

        Array.from(filmCards).forEach(card => {
            const genres = card.dataset.genres ? card.dataset.genres.split(',') : [];
            const seances = card.dataset.seances ? card.dataset.seances.split(',') : [];
            const cinemas = card.dataset.cinemas ? card.dataset.cinemas.split(',') : [];

            const matchGenre = !selectedGenre || genres.includes(selectedGenre);
            const matchDate = !selectedDate || seances.includes(selectedDate);
            const matchCinema = !selectedCinema || cinemas.includes(selectedCinema);

            if (matchGenre && matchDate && matchCinema) {
                card.classList.remove('d-none');
            } else {
                card.classList.add('d-none');
            }
        });
    }

    genreFilter.addEventListener('change', applyFilters);
    dateFilter.addEventListener('change', applyFilters);
    cinemaFilter.addEventListener('change', applyFilters);

    applyFilters();
});
