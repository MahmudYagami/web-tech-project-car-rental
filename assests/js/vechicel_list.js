document.addEventListener("DOMContentLoaded", () => {
    fetch('fetch_vehicle.php') // assumes you're fetching from this PHP script
        .then(res => res.json())
        .then(data => {
            const container = document.getElementById("vehicle-container");
            container.innerHTML = '';

            data.forEach(vehicle => {
                const card = document.createElement("div");
                card.classList.add("vehicle-card");

                card.innerHTML = `
                    <img src="${vehicle.image || 'images/default_car.jpg'}" alt="${vehicle.model}">
                    <div class="vehicle-card-content">
                        <h3>${vehicle.model}</h3>
                        <p><strong>Price/Day:</strong> $${vehicle.price_per_day}</p>
                        <p><strong>Mileage:</strong> ${vehicle.mileage ?? 'N/A'}</p>
                        <p><strong>Fuel:</strong> ${vehicle.fuel_type ?? 'N/A'}</p>
                        <p><strong>Transmission:</strong> ${vehicle.transmission ?? 'N/A'}</p>
                        <div style="margin-top: 10px; display: flex; gap: 10px;">
                            <a href="../view/booking.php?id=${vehicle.vehicle_id}" class="btn" style="background-color: #27ae60;">Rent Now</a>
                        </div>
                    </div>
                `;

                container.appendChild(card);
            });
        })
        .catch(err => {
            console.error("Error fetching vehicles:", err);
            document.getElementById("vehicle-container").innerHTML = "<p>Failed to load vehicles.</p>";
        });
});
