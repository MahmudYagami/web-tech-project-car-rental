document.addEventListener('DOMContentLoaded', function() {
    // Add click event listeners to all book buttons
    const bookButtons = document.querySelectorAll('.book-btn');
    
    bookButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const carId = this.getAttribute('data-car-id');
            
            if (!carId) {
                console.error('No car ID found');
                return;
            }

            // Redirect to booking controller with car ID
            window.location.href = `../controller/booking_controller.php?car_id=${carId}`;
        });
    });
});

// Add smooth scroll behavior
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        document.querySelector(this.getAttribute('href')).scrollIntoView({
            behavior: 'smooth'
        });
    });
});

// Add hover effect for car cards
document.querySelectorAll('.car-card').forEach(card => {
    card.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-5px)';
    });
    
    card.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0)';
    });
}); 

let searchTimeout;
const loadingElement = document.querySelector('.loading');
const noResultsElement = document.querySelector('.no-results');
const carGrid = document.getElementById('carGrid');
const searchInput = document.getElementById('searchInput');
const filterSelect = document.getElementById('filterSelect');

function debounceSearch() {
    clearTimeout(searchTimeout);
    loadingElement.style.display = 'block';
    noResultsElement.style.display = 'none';
    
    searchTimeout = setTimeout(() => {
        searchCars();
    }, 300);
}

function searchCars() {
    const searchTerm = searchInput.value;
    const filter = filterSelect.value;
    
    fetch(`../controller/inventory_controller.php?search=${encodeURIComponent(searchTerm)}&filter=${encodeURIComponent(filter)}`)
        .then(response => response.json())
        .then(data => {
            loadingElement.style.display = 'none';
            
            if (data.success) {
                if (data.cars.length === 0) {
                    noResultsElement.style.display = 'block';
                    carGrid.style.display = 'none';
                } else {
                    noResultsElement.style.display = 'none';
                    carGrid.style.display = 'grid';
                    updateCarGrid(data.cars);
                }
            } else {
                alert('Error searching cars: ' + data.message);
            }
        })
        .catch(error => {
            loadingElement.style.display = 'none';
            console.error('Error:', error);
            alert('An error occurred while searching cars');
        });
}

function updateCarGrid(cars) {
    carGrid.innerHTML = '';
    
    cars.forEach(car => {
        const carCard = document.createElement('div');
        carCard.className = 'car-card';
        carCard.innerHTML = `
            <img src="${car.image_url}" alt="${car.brand} ${car.model}">
            <div class="car-info">
                <h2>${car.brand} ${car.model}</h2>
                <p class="year">${car.year}</p>
                <div class="details">
                    <p><strong>Color:</strong> ${car.color}</p>
                    <p><strong>Transmission:</strong> ${car.transmission}</p>
                    <p><strong>Fuel Type:</strong> ${car.fuel_type}</p>
                    <p><strong>Seats:</strong> ${car.seats}</p>
                    <p><strong>Mileage:</strong> ${car.mileage} km</p>
                </div>
                <p class="price">$${car.daily_rate} per day</p>
                <p class="description">${car.description}</p>
                <button class="book-btn" data-car-id="${car.car_id}">Book Now</button>
            </div>
        `;
        carGrid.appendChild(carCard);
    });

    // Reattach event listeners to new book buttons
    attachBookButtonListeners();
}

function attachBookButtonListeners() {
    document.querySelectorAll('.book-btn').forEach(button => {
        button.addEventListener('click', function() {
            const carId = this.getAttribute('data-car-id');
            window.location.href = `booking.php?car_id=${carId}`;
        });
    });
}

// Event Listeners
searchInput.addEventListener('input', debounceSearch);
filterSelect.addEventListener('change', debounceSearch);

// Prevent form submission
document.getElementById('searchForm').addEventListener('submit', function(e) {
    e.preventDefault();
    searchCars();
});

function toggleDropdown() {
    const dropdown = document.getElementById('userDropdown');
    dropdown.classList.toggle('show');
}

// Close dropdown when clicking outside
window.onclick = function(event) {
    if (!event.target.matches('.user-icon') && !event.target.matches('.user-icon *')) {
        const dropdown = document.getElementById('userDropdown');
        if (dropdown.classList.contains('show')) {
            dropdown.classList.remove('show');
        }
    }
}