<?php require_once 'config.php'; ?>

<div class="section-header">
    <h2>User List</h2>
</div>

<div class="bookings-table-container">

    <table class="bookings-table">

        <thead>
            <tr>
                <th>User ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Date Created</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>

        <?php
        $result = $conn->query("
            SELECT * 
            FROM users 
            WHERE role = 'user'
            ORDER BY user_id DESC
        ");

        if ($result && $result->num_rows > 0) {

            while ($user = $result->fetch_assoc()) {
        ?>

            <tr id="user-row-<?= $user['user_id'] ?>">

                <td><?= $user['user_id'] ?></td>

                <td><?= htmlspecialchars($user['name']) ?></td>

                <td><?= htmlspecialchars($user['email']) ?></td>

                <td><?= htmlspecialchars($user['role']) ?></td>

                <td>
                    <?= !empty($user['status']) ? htmlspecialchars($user['status']) : 'active' ?>
                </td>

                <td>
                    <?= !empty($user['created_at'])
                        ? date('M d, Y', strtotime($user['created_at']))
                        : 'N/A' ?>
                </td>

                <td>
                    <button class="btn btn-edit btn-sm"
                        onclick="editUser(<?= $user['user_id'] ?>)">
                        Edit
                    </button>

                    <button class="btn btn-delete btn-sm"
                        onclick="deleteUser(<?= $user['user_id'] ?>)">
                        Delete
                    </button>
                </td>

            </tr>

        <?php
            }

        } else {
        ?>

            <tr>
                <td colspan="7" style="text-align:center; padding:20px; color:#aaa;">
                    No users yet
                </td>
            </tr>

        <?php } ?>

        </tbody>

    </table>

</div>

<script>

// EDIT (unchanged for now)
function editUser(id) {
    alert("Edit user ID: " + id);
}

// DELETE (NOW MATCHES MOVIE STYLE)
function deleteUser(id) {
    if (!confirm("Delete this user?")) return;

    fetch('delete_users.php?id=' + id)
    .then(res => res.text())   // 👈 change THIS
    .then(text => {
        console.log("RAW RESPONSE:", text); // 👈 show real output
        return JSON.parse(text);
    })
    .then(res => {
        document.querySelector('[data-id="' + id + '"]')?.remove();
        else alert(res.error);
    })
    .catch(err => {
        console.log("ERROR:", err);
    });
}

</script>