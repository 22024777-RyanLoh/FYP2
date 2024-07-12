// JavaScript
const cards = document.querySelectorAll('.card');
const modalContainer = document.getElementById('modalContainer');

cards.forEach(card => {
    card.addEventListener('click', () => {
        const title = card.getAttribute('data-title');
        const description = card.getAttribute('data-description');
        const image = card.getAttribute('data-image');

        document.getElementById('modalTitle').textContent = title;
        document.getElementById('modalDescription').innerHTML = `<p>${description}</p>`;
        document.getElementById('modalImage').src = image;

        modalContainer.style.display = 'block';
    });
});

modalContainer.addEventListener('click', (event) => {
    if (event.target === modalContainer) {
        closeModal();
    }
});

function closeModal() {
    modalContainer.style.display = 'none';
}
