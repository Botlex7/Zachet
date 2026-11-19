// Загрузка популярных лодок на главную
const boats = [
    { name: 'Моторная лодка 1', price: 2000, capacity: 4, image: 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=300' },
    { name: 'Моторная лодка 2', price: 2500, capacity: 5, image: 'https://images.unsplash.com/photo-1579033461380-adb47c3eb938?w=300' },
    { name: 'Катер', price: 3500, capacity: 8, image: 'https://images.unsplash.com/photo-1567899378494-47b22a2ae96a?w=300' }
];

function displayBoats(containerId, boatsList) {
    const container = document.getElementById(containerId);
    if (!container) return;
    
    container.innerHTML = boatsList.map(boat => `
        <div class="boat-card">
            <img src="${boat.image}" alt="${boat.name}">
            <div class="boat-card-content">
                <h3>${boat.name}</h3>
                <p>Вместимость: ${boat.capacity} человек</p>
                <div class="boat-price">от ${boat.price.toLocaleString()} ₽/час</div>
                <a href="rental-form.html" class="btn">Забронировать</a>
            </div>
        </div>
    `).join('');
}

// Показываем популярные лодки на главной
if (document.getElementById('popularBoats')) {
    displayBoats('popularBoats', boats);
}

// Показываем все лодки в каталоге
if (document.getElementById('allBoats')) {
    const allBoats = [
        ...boats,
        { name: 'Яхта', price: 5000, capacity: 12, image: 'https://images.unsplash.com/photo-1569263979104-865ab7cd8d13?w=300' },
        { name: 'Лодка для рыбалки', price: 1800, capacity: 3, image: 'https://images.unsplash.com/photo-1531860431897-37e4aef1e762?w=300' }
    ];
    displayBoats('allBoats', allBoats);
}

// Поиск лодок
const searchInput = document.getElementById('searchBoats');
if (searchInput) {
    searchInput.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const boatCards = document.querySelectorAll('.boat-card');
        
        boatCards.forEach(card => {
            const title = card.querySelector('h3').textContent.toLowerCase();
            if (title.includes(searchTerm)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    });
}