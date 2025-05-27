        // Get the modal
        const modal = document.getElementById('editModal');
        const span = document.getElementsByClassName('close')[0];

        // When the user clicks on <span> (x), close the modal
        span.onclick = function() {
            modal.style.display = 'none';
        }

        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }

        function editCar(car) {
            // Populate the edit form with car data
            document.getElementById('edit_car_id').value = car.car_id;
            document.getElementById('edit_brand').value = car.brand;
            document.getElementById('edit_model').value = car.model;
            document.getElementById('edit_year').value = car.year;
            document.getElementById('edit_color').value = car.color;
            document.getElementById('edit_transmission').value = car.transmission;
            document.getElementById('edit_fuel_type').value = car.fuel_type;
            document.getElementById('edit_seats').value = car.seats;
            document.getElementById('edit_mileage').value = car.mileage;
            document.getElementById('edit_daily_rate').value = car.daily_rate;
            document.getElementById('edit_image_url').value = car.image_url;
            document.getElementById('edit_description').value = car.description;

            // Show the modal
            modal.style.display = 'block';
        }

        function deleteCar(carId) {
            if (confirm('Are you sure you want to delete this car?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '../controller/manage_cars_controller.php';

                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'delete_car';

                const carIdInput = document.createElement('input');
                carIdInput.type = 'hidden';
                carIdInput.name = 'car_id';
                carIdInput.value = carId;

                form.appendChild(actionInput);
                form.appendChild(carIdInput);
                document.body.appendChild(form);
                form.submit();
            }
        }

        function updateCarStatus(carId, status) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '../controller/manage_cars_controller.php';

            const actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            actionInput.value = 'update_car';

            const carIdInput = document.createElement('input');
            carIdInput.type = 'hidden';
            carIdInput.name = 'car_id';
            carIdInput.value = carId;

            const statusInput = document.createElement('input');
            statusInput.type = 'hidden';
            statusInput.name = 'status';
            statusInput.value = status;

            form.appendChild(actionInput);
            form.appendChild(carIdInput);
            form.appendChild(statusInput);
            document.body.appendChild(form);
            form.submit();
        }