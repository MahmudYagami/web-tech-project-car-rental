function searchUsers() {
    const searchTerm = document.getElementById('searchInput').value;
    fetch(`../controller/user_management_controller.php?search=${encodeURIComponent(searchTerm)}`)
        .then(response => response.json())
        .then(data => {
            updateUserTable(data.users);
        })
        .catch(error => console.error('Error:', error));
}

function updateUserTable(users) {
    const tbody = document.getElementById('userTableBody');
    tbody.innerHTML = users.map(user => `
        <tr>
            <td>${user.user_id}</td>
            <td>${user.first_name} ${user.last_name}</td>
            <td>${user.email}</td>
            <td>
                <select onchange="updateRole(${user.user_id}, this.value)">
                    <option value="user" ${user.role === 'user' ? 'selected' : ''}>User</option>
                    <option value="admin" ${user.role === 'admin' ? 'selected' : ''}>Admin</option>
                </select>
            </td>
            <td>
                <button onclick="deleteUser(${user.user_id})" class="btn btn-danger">Delete</button>
            </td>
        </tr>
    `).join('');
}

function updateRole(userId, role) {
    const formData = new FormData();
    formData.append('action', 'update_role');
    formData.append('user_id', userId);
    formData.append('role', role);

    fetch('../controller/user_management_controller.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Role updated successfully');
        } else {
            alert('Failed to update role');
        }
    })
    .catch(error => console.error('Error:', error));
}

function deleteUser(userId) {
    if (!confirm('Are you sure you want to delete this user?')) {
        return;
    }

    const formData = new FormData();
    formData.append('action', 'delete_user');
    formData.append('user_id', userId);

    fetch('../controller/user_management_controller.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('User deleted successfully');
            searchUsers(); // Refresh the table
        } else {
            alert('Failed to delete user');
        }
    })
    .catch(error => console.error('Error:', error));
}

function handleAddUser(event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);
    formData.append('action', 'add_user');

    fetch('../controller/user_management_controller.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('User added successfully');
            form.reset();
            searchUsers(); // Refresh the table
        } else {
            alert('Failed to add user');
        }
    })
    .catch(error => console.error('Error:', error));

    return false;
}