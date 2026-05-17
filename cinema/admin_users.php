<?php require_once 'config.php'; ?>

<div style="background: #800020; color: white; padding: 20px; margin: 10px 0; border-radius: 8px;">
    <h2 style="margin: 0;">User Management</h2>
</div>

<div style="background: white; padding: 20px; margin: 10px 0; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background: #800020; color: white;">
                <th style="padding: 10px; text-align: left;">User ID</th>
                <th style="padding: 10px; text-align: left;">Name</th>
                <th style="padding: 10px; text-align: left;">Email</th>
                <th style="padding: 10px; text-align: left;">Role</th>
                <th style="padding: 10px; text-align: left;">Status</th>
                <th style="padding: 10px; text-align: left;">Date Created</th>
                <th style="padding: 10px; text-align: left;">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $result = $conn->query("SELECT * FROM users WHERE role = 'user' ORDER BY user_id DESC");
            
            if ($result && $result->num_rows > 0) {
                while ($user = $result->fetch_assoc()) {
                    echo '<tr style="border-bottom: 1px solid #0b0b0b;">
                        <td style="padding: 10px; color: black;">' . $user['user_id'] . '</td>
                        <td style="padding: 10px; color: black;">' . htmlspecialchars($user['name']) . '</td>
                        <td style="padding: 10px; color: black;">' . htmlspecialchars($user['email']) . '</td>
                        <td style="padding: 10px; color: black;">' . htmlspecialchars($user['role']) . '</td>
                        <td style="padding: 10px;"><span style="background: #d4edda; color: #155724; padding: 4px 8px; border-radius: 12px; font-size: 11px;">' . (!empty($user['status']) ? htmlspecialchars($user['status']) : 'active') . '</span></td>
                        <td style="padding: 10px; color: black;">' . (!empty($user['created_at']) ? date('M d, Y', strtotime($user['created_at'])) : 'N/A') . '</td>
                        <td style="padding: 10px;">
                            <button onclick="editUser(' . $user['user_id'] . ', \'' . htmlspecialchars(addslashes($user['name']), ENT_QUOTES) . '\', \'' . htmlspecialchars(addslashes($user['email']), ENT_QUOTES) . '\')" style="background: #007bff; color: white; border: none; padding: 4px 8px; margin-right: 4px; border-radius: 4px; cursor: pointer;">Edit</button>
                            <button onclick="deleteUser(' . $user['user_id'] . ')" style="background: #dc3545; color: white; border: none; padding: 4px 8px; border-radius: 4px; cursor: pointer;">Delete</button>
                        </td>
                    </tr>';
                }
            } else {
                echo '<tr><td colspan="7" style="padding: 20px; text-align: center; color: #666;">No users yet</td></tr>';
            }
            ?>
        </tbody>
    </table>
</div>

<!-- EDIT USER MODAL -->
<div id="editModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; justify-content:center; align-items:center;">
    <div style="background:white; padding:30px; border-radius:10px; width:400px; max-width:90%;">
        <h3 style="color:#800020; margin-bottom:20px;">Edit User</h3>
        <input type="hidden" id="editUserId">
        
        <div style="margin-bottom:15px;">
            <label style="display:block; margin-bottom:5px; font-weight:bold; color:black;">Name:</label>
            <input type="text" id="editName" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:4px; color:black;">
        </div>
        
        <div style="margin-bottom:15px;">
            <label style="display:block; margin-bottom:5px; font-weight:bold; color:black;">Email:</label>
            <input type="email" id="editEmail" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:4px; color:black;">
        </div>
        
        <div style="margin-bottom:15px;">
            <label style="display:block; margin-bottom:5px; font-weight:bold; color:black;">New Password:</label>
            <input type="text" id="editPassword" placeholder="Leave blank to keep current" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:4px; color:black;">
            <small style="color:#999;">Only fill this if the user forgot their password.</small>
        </div>
        
        <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:20px;">
            <button onclick="saveUser()" style="background:#28a745; color:white; border:none; padding:8px 16px; border-radius:4px; cursor:pointer; font-weight:bold;">Save</button>
            <button onclick="closeEditModal()" style="background:#6c757d; color:white; border:none; padding:8px 16px; border-radius:4px; cursor:pointer; font-weight:bold;">Cancel</button>
        </div>
    </div>
</div>

<script>
function editUser(id, name, email) {
    document.getElementById('editUserId').value = id;
    document.getElementById('editName').value = name;
    document.getElementById('editEmail').value = email;
    document.getElementById('editPassword').value = '';
    document.getElementById('editModal').style.display = 'flex';
}

function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}

function saveUser() {
    const id = document.getElementById('editUserId').value;
    const name = document.getElementById('editName').value;
    const email = document.getElementById('editEmail').value;
    const password = document.getElementById('editPassword').value;

    if (!name || !email) {
        alert('Name and Email are required');
        return;
    }

    fetch('update_user.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({
            user_id: id,
            name: name,
            email: email,
            password: password
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('User updated successfully');
            location.reload();
        } else {
            alert('Error: ' + data.error);
        }
    })
    .catch(err => {
        console.error('Error:', err);
        alert('Error updating user');
    });
}

function deleteUser(id) {
    if (confirm('Delete this user?')) {
        fetch('delete_users.php?id=' + id)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.error || 'Failed to delete user');
            }
        })
        .catch(err => {
            console.error('Error:', err);
            alert('Error deleting user');
        });
    }
}
</script>
