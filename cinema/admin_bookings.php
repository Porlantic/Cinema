<?php require_once 'config.php'; ?>


<div style="background: #800020; color: white; padding: 20px; margin: 10px 0; border-radius: 8px;">
    <h2 style="margin: 0;">Booking Management</h2>
</div>

<div style="background: white; padding: 20px; margin: 10px 0; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
    <h3 style="color: #800020; margin-bottom: 15px;">All Bookings</h3>
    
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background: #800020; color: white;">
                <th style="padding: 10px; text-align: left;">Booking ID</th>
                <th style="padding: 10px; text-align: left;">Movie Title</th>
                <th style="padding: 10px; text-align: left;">Customer</th>
                <th style="padding: 10px; text-align: left;">Seats</th>
                <th style="padding: 10px; text-align: left;">Total</th>
                <th style="padding: 10px; text-align: left;">Payment</th>
                <th style="padding: 10px; text-align: left;">Status</th>
                <th style="padding: 10px; text-align: left;">Date</th>
                <th style="padding: 10px; text-align: left;">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Simple test query first
            $result = $conn->query("SELECT COUNT(*) as total FROM bookings");
            $row = $result->fetch_assoc();
            $totalBookings = $row['total'];
            
            echo '<tr><td colspan="8" style="padding: 20px; text-align: center; color: #666;">Total bookings in database: ' . $totalBookings . '</td></tr>';
            
            // Now try the full query with normalized structure
            $sql = "SELECT b.booking_id, b.user_id, b.total_price, b.payment_status, b.created_at, b.payment_method, b.payment_reference, b.card_last4, b.card_type, b.gcash_last3, m.title AS movie_title, u.name as customer_name,
                    GROUP_CONCAT(s.seat_label ORDER BY s.seat_label SEPARATOR ', ') as seats
                    FROM bookings b 
                    LEFT JOIN movies m ON b.movie_id = m.movie_id
                    LEFT JOIN users u ON b.user_id = u.user_id
                    LEFT JOIN booked_seats bs ON b.booking_id = bs.booking_id
                    LEFT JOIN seats s ON bs.seat_id = s.seat_id
                    GROUP BY b.booking_id
                    ORDER BY b.booking_id DESC";
            $result = $conn->query($sql);
            
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $statusColor = '#d4edda; color: #155724';
                    $showAction = true;
                    
                    echo '<tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 10px; color: black;">' . $row['booking_id'] . '</td>
                        <td style="padding: 10px; color: black;">' . htmlspecialchars($row['movie_title']) . '</td>
                        <td style="padding: 10px; color: black;">' . htmlspecialchars($row['customer_name']) . '</td>
                        <td style="padding: 10px; color: black;">' . htmlspecialchars($row['seats']) . '</td>
                        <td style="padding: 10px; color: black;">₱' . number_format($row['total_price'], 2) . '</td>
                        <td style="padding: 10px; color: black;">';
                        if ($row['payment_method'] == 'card') {
                            echo '💳 ' . $row['card_type'] . ' (****' . $row['card_last4'] . ')';
                        } elseif ($row['payment_method'] == 'gcash') {
                            echo '📱 Gcash (***' . $row['gcash_last3'] . ')';
                        } else {
                            echo ucfirst($row['payment_method'] ?? 'N/A');
                        }
                        echo '</td>
                        <td style="padding: 10px;"><span style="background: ' . $statusColor . '; padding: 4px 8px; border-radius: 12px; font-size: 11px;">' . ucfirst($row['payment_status']) . '</span></td>
                        <td style="padding: 10px; color: black;">' . date('M d, Y h:i A', strtotime($row['created_at'])) . '</td>
                        <td style="padding: 10px;">';
                    if ($showAction) {
                        echo '<button onclick="cancelBooking(' . $row['booking_id'] . ')" style="background: #dc3545; color: white; border: none; padding: 4px 8px; margin-right: 4px; border-radius: 4px; cursor: pointer;">Cancel</button>';
                    }
                    echo '</td>
                    </tr>';
                }
            } else {
                echo '<tr><td colspan="8" style="padding: 20px; text-align: center; color: #666;">No bookings found</td></tr>';
            }
            ?>
        </tbody>
    </table>
</div>

<!-- ADD NEW BOOKING -->
<div style="background: white; padding: 20px; margin: 10px 0; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
    <h3 style="color: #800020; margin-bottom: 15px;">Add New Booking</h3>
    
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
        <div>
            <label style="display: block; margin-bottom: 5px; font-weight: bold; color: black;">Movie:</label>
            <input type="text" id="movieSearch" list="movieList" placeholder="Search movie..." style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; color: black;">
            <datalist id="movieList">
                <?php
                $movies = $conn->query("SELECT movie_id, title FROM movies ORDER BY title ASC");
                while ($movie = $movies->fetch_assoc()) {
                    echo '<option data-id="' . $movie['movie_id'] . '" value="' . htmlspecialchars($movie['title']) . '"></option>';
                }
                ?>
            </datalist>
        </div>
        
        <div>
            <label style="display: block; margin-bottom: 5px; font-weight: bold; color: black;">User:</label>
            <input type="text" id="userSearch" list="userList" placeholder="Search user by name..." style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; color: black;">
            <datalist id="userList">
                <?php
                $users = $conn->query("SELECT user_id, name FROM users WHERE role = 'user' ORDER BY name ASC");
                while ($user = $users->fetch_assoc()) {
                    echo '<option data-id="' . $user['user_id'] . '" value="' . htmlspecialchars($user['name']) . '"></option>';
                }
                ?>
            </datalist>
        </div>
    </div>
    
    <div style="text-align: center; margin-top: 20px;">
        <button type="button" id="selectSeatsBtn" onclick="goToSeats()" style="background: #800020; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-size: 16px;">Select Seats</button>
    </div>
</div>

<script>
function goToSeats() {
    const movieTitle = document.getElementById('movieSearch').value;
    const userName = document.getElementById('userSearch').value;
    
    if (!movieTitle || !userName) {
        alert('Please select both a movie and a user');
        return;
    }
    
    // Find movie_id from datalist
    const movieOption = Array.from(document.querySelectorAll('#movieList option')).find(o => o.value === movieTitle);
    const userOption = Array.from(document.querySelectorAll('#userList option')).find(o => o.value === userName);
    
    if (!movieOption) {
        alert('Please select a valid movie from the list');
        return;
    }
    if (!userOption) {
        alert('Please select a valid user from the list');
        return;
    }
    
    const movieId = movieOption.getAttribute('data-id');
    const userId = userOption.getAttribute('data-id');
    
    window.location.href = 'admin_seat.php?movie_id=' + movieId + '&user_id=' + userId;
}

function cancelBooking(id) {
    if (confirm('Cancel and delete this booking? This cannot be undone.')) {
        fetch('delete_booking.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
                booking_id: id
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.error);
            }
        })
        .catch(err => {
            console.error('Error:', err);
            alert('Error cancelling booking');
        });
    }
}
</script>