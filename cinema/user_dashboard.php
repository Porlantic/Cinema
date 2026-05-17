<?php
session_start();
require_once 'config.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>User Dashboard</title>

<link rel="stylesheet" href="index.css">
<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    background: #141414;
    color: #fff;
    -webkit-font-smoothing: antialiased;
    overflow-y: auto;
}

/* ================= NAVBAR ================= */
.navbar {
    background: rgba(20, 20, 20, 0.95);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    padding: 0 40px;
    height: 64px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: sticky;
    top: 0;
    z-index: 1000;
    border-bottom: 1px solid rgba(255,255,255,0.05);
}

.logo {
    font-size: 22px;
    font-weight: 800;
    color: #e50914;
    letter-spacing: 3px;
    text-transform: uppercase;
}

.nav-links {
    display: flex;
    gap: 8px;
}

.nav-links a {
    color: rgba(255,255,255,0.7);
    text-decoration: none;
    margin: 0;
    padding: 8px 16px;
    border-radius: 4px;
    transition: all 0.2s ease;
    font-size: 14px;
    font-weight: 500;
}

.nav-links a:hover {
    color: #fff;
    background: rgba(255,255,255,0.08);
}

/* PROFILE DROPDOWN */
.profile-dropdown {
    position: relative;
}

.profile-btn {
    background: none;
    border: 1px solid rgba(255,255,255,0.15);
    color: rgba(255,255,255,0.85);
    cursor: pointer;
    padding: 6px 16px;
    border-radius: 4px;
    font-size: 13px;
    font-family: 'Inter', sans-serif;
    font-weight: 500;
    transition: all 0.2s ease;
}

.profile-btn:hover {
    border-color: rgba(255,255,255,0.3);
    color: #fff;
}

.dropdown-content {
    display: none;
    position: absolute;
    right: 0;
    top: 100%;
    margin-top: 8px;
    background: #1a1a1a;
    min-width: 180px;
    border-radius: 6px;
    overflow: hidden;
    border: 1px solid rgba(255,255,255,0.08);
    box-shadow: 0 10px 40px rgba(0,0,0,0.6);
}

.dropdown-content a {
    display: block;
    padding: 12px 16px;
    color: rgba(255,255,255,0.8);
    text-decoration: none;
    font-size: 13px;
    transition: all 0.15s ease;
}

.dropdown-content a:hover {
    background: rgba(229,9,20,0.15);
    color: #fff;
}

.dropdown-content.show {
    display: block;
}

/* ================= SECTIONS ================= */
.section {
    display: none;
    padding: 20px 40px 40px;
}

.section.active {
    display: block;
}

.section > h2 {
    color: #fff;
    font-size: 24px;
    font-weight: 700;
    margin-bottom: 20px;
    padding-left: 16px;
    position: relative;
}

.section > h2::before {
    content: '';
    position: absolute;
    left: 0;
    top: 50%;
    transform: translateY(-50%);
    width: 4px;
    height: 60%;
    background: #e50914;
    border-radius: 2px;
}

/* PROFILE SECTION */
.profile-info {
    display: flex;
    gap: 30px;
    flex-wrap: wrap;
    margin-bottom: 30px;
}

.profile-card {
    background: #1a1a1a;
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 8px;
    padding: 20px 28px;
    min-width: 200px;
}

.profile-card .label {
    font-size: 11px;
    color: rgba(255,255,255,0.4);
    text-transform: uppercase;
    letter-spacing: 1.5px;
    font-weight: 500;
    margin-bottom: 6px;
}

.profile-card .value {
    font-size: 16px;
    color: #fff;
    font-weight: 600;
}

.history-title {
    color: #fff;
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 16px;
    padding-left: 14px;
    position: relative;
}

.history-title::before {
    content: '';
    position: absolute;
    left: 0;
    top: 50%;
    transform: translateY(-50%);
    width: 3px;
    height: 60%;
    background: #e50914;
    border-radius: 2px;
}

.history-container {
    background: #1a1a1a;
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 8px;
    max-height: 400px;
    overflow-y: auto;
}

.history-container::-webkit-scrollbar {
    width: 6px;
}

.history-container::-webkit-scrollbar-track {
    background: transparent;
}

.history-container::-webkit-scrollbar-thumb {
    background: rgba(255,255,255,0.15);
    border-radius: 3px;
}

.history-container::-webkit-scrollbar-thumb:hover {
    background: rgba(255,255,255,0.25);
}

.history-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 14px 20px;
    border-bottom: 1px solid rgba(255,255,255,0.04);
    transition: background 0.15s ease;
}

.history-item:last-child {
    border-bottom: none;
}

.history-item:hover {
    background: rgba(255,255,255,0.03);
}

.history-left {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.history-movie {
    font-size: 14px;
    font-weight: 600;
    color: #fff;
}

.history-details {
    font-size: 12px;
    color: rgba(255,255,255,0.4);
}

.history-right {
    text-align: right;
    display: flex;
    flex-direction: column;
    gap: 4px;
    align-items: flex-end;
}

.history-price {
    font-size: 14px;
    font-weight: 700;
    color: #e50914;
}

.history-status {
    font-size: 10px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 3px 8px;
    border-radius: 3px;
    background: rgba(76,175,80,0.15);
    color: #4caf50;
}

.history-receipt {
    display: inline-block;
    padding: 4px 10px;
    background: rgba(229,9,20,0.12);
    color: #e50914;
    border-radius: 3px;
    text-decoration: none;
    font-size: 10px;
    font-weight: 600;
    margin-top: 4px;
    transition: all 0.2s ease;
}

.history-receipt:hover {
    background: #e50914;
    color: #fff;
}

/* ===== MODAL OVERLAY ===== */
.dash-modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.85);
    z-index: 2000;
    justify-content: center;
    align-items: flex-start;
    padding: 40px 20px;
    overflow-y: auto;
}

.dash-modal-overlay.show {
    display: flex;
}

.dash-modal {
    background: #141414;
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 12px;
    width: 100%;
    max-width: 440px;
    overflow: hidden;
    position: relative;
    animation: modalSlideIn 0.25s ease;
}

@keyframes modalSlideIn {
    from { opacity: 0; transform: translateY(-20px); }
    to { opacity: 1; transform: translateY(0); }
}

.dash-modal-close {
    position: absolute;
    top: 14px;
    right: 16px;
    background: rgba(0,0,0,0.5);
    border: none;
    color: #fff;
    font-size: 18px;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    cursor: pointer;
    z-index: 10;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s;
}

.dash-modal-close:hover {
    background: rgba(255,255,255,0.15);
}

/* Receipt modal inner styles */
.rm-header {
    background: #e50914;
    padding: 20px 24px;
    text-align: center;
}

.rm-header .rm-logo { font-size: 16px; font-weight: 800; letter-spacing: 3px; text-transform: uppercase; }
.rm-header .rm-sub { font-size: 10px; opacity: 0.85; letter-spacing: 1px; text-transform: uppercase; margin-top: 2px; }

.rm-body { padding: 24px; }

.rm-row {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px solid rgba(255,255,255,0.04);
}

.rm-row:last-child { border-bottom: none; }
.rm-label { font-size: 11px; color: rgba(255,255,255,0.4); text-transform: uppercase; letter-spacing: 1px; font-weight: 500; }
.rm-val { font-size: 13px; font-weight: 600; color: #fff; text-align: right; max-width: 60%; }

.rm-total {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 14px 0 0;
    margin-top: 8px;
    border-top: 1px dashed rgba(255,255,255,0.1);
}

.rm-total .rm-label { font-size: 13px; font-weight: 600; }
.rm-total .rm-amount { font-size: 20px; font-weight: 800; color: #e50914; }

.rm-ref {
    text-align: center;
    margin-top: 16px;
    padding-top: 12px;
    border-top: 1px solid rgba(255,255,255,0.06);
    font-size: 10px;
    color: rgba(255,255,255,0.3);
}

.rm-ref span { display: block; font-family: monospace; color: rgba(255,255,255,0.5); margin-top: 2px; font-size: 11px; }

.rm-actions {
    padding: 0 24px 24px;
    display: flex;
    gap: 10px;
}

.rm-actions button {
    flex: 1;
    padding: 11px;
    border: none;
    border-radius: 6px;
    font-family: 'Inter', sans-serif;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.rm-btn-dl { background: #e50914; color: #fff; }
.rm-btn-dl:hover { background: #f40612; }
.rm-btn-close { background: rgba(255,255,255,0.08); color: rgba(255,255,255,0.7); }
.rm-btn-close:hover { background: rgba(255,255,255,0.12); color: #fff; }

/* ===== TICKET MODAL ===== */
.ticket-modal-body {
    padding: 24px;
    max-height: 70vh;
    overflow-y: auto;
}

.ticket-modal-body::-webkit-scrollbar { width: 5px; }
.ticket-modal-body::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); border-radius: 3px; }

.tm-header {
    background: #e50914;
    padding: 20px 24px;
    text-align: center;
}

.tm-header .tm-logo { font-size: 16px; font-weight: 800; letter-spacing: 3px; text-transform: uppercase; }
.tm-header .tm-sub { font-size: 10px; opacity: 0.85; letter-spacing: 1px; text-transform: uppercase; margin-top: 2px; }

.tm-info {
    text-align: center;
    margin-bottom: 20px;
    padding-bottom: 16px;
    border-bottom: 1px dashed rgba(255,255,255,0.1);
}

.tm-info h3 { font-size: 18px; font-weight: 700; margin-bottom: 4px; }
.tm-info .tm-schedule { font-size: 12px; color: #e50914; font-weight: 500; }

.ticket-card {
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 8px;
    padding: 16px;
    margin-bottom: 10px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.tc-left { display: flex; flex-direction: column; gap: 2px; }
.tc-seat { font-size: 15px; font-weight: 700; color: #fff; }
.tc-movie { font-size: 11px; color: rgba(255,255,255,0.4); }
.tc-date { font-size: 10px; color: rgba(255,255,255,0.3); }

.tc-right { display: flex; flex-direction: column; align-items: flex-end; gap: 6px; }
.tc-price { font-size: 14px; font-weight: 700; color: #e50914; }

.tc-dl {
    padding: 4px 10px;
    background: rgba(229,9,20,0.12);
    color: #e50914;
    border: none;
    border-radius: 3px;
    font-size: 10px;
    font-weight: 600;
    font-family: 'Inter', sans-serif;
    cursor: pointer;
    transition: all 0.2s;
}

.tc-dl:hover { background: #e50914; color: #fff; }

/* ===== REFUND MODAL ===== */
.rf-header {
    background: #ffc107;
    color: #000;
    padding: 20px 24px;
    text-align: center;
}

.rf-header .rf-logo { font-size: 16px; font-weight: 800; letter-spacing: 3px; text-transform: uppercase; }
.rf-header .rf-sub { font-size: 10px; letter-spacing: 1px; text-transform: uppercase; margin-top: 2px; opacity: 0.7; }

.rf-body { padding: 24px; }

.rf-info {
    margin-bottom: 20px;
    padding-bottom: 16px;
    border-bottom: 1px dashed rgba(255,255,255,0.1);
}

.rf-info h3 { font-size: 16px; font-weight: 700; margin-bottom: 10px; }

.rf-detail {
    display: flex;
    justify-content: space-between;
    padding: 6px 0;
    font-size: 12px;
}

.rf-detail .rf-lbl { color: rgba(255,255,255,0.4); }
.rf-detail .rf-val { color: #fff; font-weight: 600; }

.rf-warn {
    background: rgba(255,193,7,0.08);
    border: 1px solid rgba(255,193,7,0.2);
    border-radius: 6px;
    padding: 12px;
    margin-bottom: 16px;
    font-size: 11px;
    color: rgba(255,255,255,0.6);
    line-height: 1.5;
}

.rf-input-group {
    margin-bottom: 16px;
}

.rf-input-group label {
    display: block;
    font-size: 11px;
    color: rgba(255,255,255,0.4);
    text-transform: uppercase;
    letter-spacing: 1px;
    font-weight: 500;
    margin-bottom: 8px;
}

.rf-input-group input {
    width: 100%;
    padding: 10px 14px;
    background: rgba(255,255,255,0.06);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 6px;
    color: #fff;
    font-family: monospace;
    font-size: 13px;
    outline: none;
    transition: border-color 0.2s;
}

.rf-input-group input:focus {
    border-color: #ffc107;
}

.rf-input-group input::placeholder {
    color: rgba(255,255,255,0.2);
}

.rf-error {
    color: #ef5350;
    font-size: 11px;
    margin-top: 6px;
    display: none;
}

.rf-actions {
    display: flex;
    gap: 10px;
}

.rf-actions button {
    flex: 1;
    padding: 11px;
    border: none;
    border-radius: 6px;
    font-family: 'Inter', sans-serif;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.rf-btn-confirm { background: #ffc107; color: #000; }
.rf-btn-confirm:hover { background: #ffca28; }
.rf-btn-confirm:disabled { opacity: 0.5; cursor: not-allowed; }
.rf-btn-cancel { background: rgba(255,255,255,0.08); color: rgba(255,255,255,0.7); }
.rf-btn-cancel:hover { background: rgba(255,255,255,0.12); color: #fff; }
</style>

</head>

<script>
window.showSection = function(section) {
    document.querySelectorAll('.section').forEach(s => {
        s.classList.remove('active');
    });

    const target = document.getElementById(section);
    if (target) {
        target.classList.add('active');
    }
};
</script>

<body>

<!-- NAVBAR -->
<header class="navbar">

    <div class="logo">Cinema</div>

    <nav class="nav-links">
        <a href="#" onclick="window.showSection('movies')">Movies</a>
        <a href="#" onclick="window.showSection('bookings')">My Bookings</a>
    </nav>

    <div class="profile-dropdown">

        <button class="profile-btn" onclick="toggleDropdown(event)">
            <?= htmlspecialchars($_SESSION['user_name']) ?> ▼
        </button>

        <div class="dropdown-content" id="profileDropdown">
            <a href="#" onclick="showSection('profile')">Profile</a>
            <a href="logout.php">Logout</a>
        </div>

    </div>

</header>

<?php $activeTab = $_GET['tab'] ?? 'movies'; ?>

<!-- MOVIES -->
<div id="movies" class="section <?= $activeTab === 'movies' ? 'active' : '' ?>">
    <?php include 'user_movies.php'; ?>
</div>

<!-- BOOKINGS -->
<div id="bookings" class="section <?= $activeTab === 'bookings' ? 'active' : '' ?>">
    <h2>My Bookings</h2>
    <?php include 'user_booking.php'; ?>
</div>

<!-- PROFILE -->
<div id="profile" class="section">
    <h2>My Profile</h2>

    <div class="profile-info">
        <div class="profile-card">
            <div class="label">Name</div>
            <div class="value"><?= htmlspecialchars($_SESSION['user_name']) ?></div>
        </div>
        <div class="profile-card">
            <div class="label">Email</div>
            <div class="value"><?= htmlspecialchars($_SESSION['user_email']) ?></div>
        </div>
    </div>

    <h3 class="history-title">Booking History</h3>
    <div class="history-container">
        <?php
        $uid = $_SESSION['user_id'];
        $hist = $conn->query("
            SELECT b.booking_id, b.total_price, b.payment_status, b.created_at, b.payment_method,
                   m.title,
                   GROUP_CONCAT(s.seat_label ORDER BY s.seat_label SEPARATOR ', ') as seats
            FROM bookings b
            LEFT JOIN movies m ON b.movie_id = m.movie_id
            LEFT JOIN booked_seats bs ON b.booking_id = bs.booking_id
            LEFT JOIN seats s ON bs.seat_id = s.seat_id
            WHERE b.user_id = $uid
            GROUP BY b.booking_id
            ORDER BY b.created_at DESC
        ");

        if ($hist && $hist->num_rows > 0) {
            while ($h = $hist->fetch_assoc()) {
        ?>
        <div class="history-item">
            <div class="history-left">
                <span class="history-movie"><?= htmlspecialchars($h['title']) ?></span>
                <span class="history-details">Seats: <?= htmlspecialchars($h['seats']) ?> &bull; <?= date('M d, Y h:i A', strtotime($h['created_at'])) ?></span>
            </div>
            <div class="history-right">
                <span class="history-price">&#8369;<?= number_format($h['total_price'], 2) ?></span>
                <span class="history-status"><?= ucfirst($h['payment_status']) ?></span>
                <a href="#" class="history-receipt" onclick="openReceiptModal(<?= $h['booking_id'] ?>); return false;">View Receipt</a>
            </div>
        </div>
        <?php
            }
        } else {
            echo '<p style="padding:20px; color:rgba(255,255,255,0.4); text-align:center;">No booking history yet.</p>';
        }
        ?>
    </div>
</div>

<script>
window.showSection = function(section) {
    document.querySelectorAll('.section').forEach(s => {
        s.classList.remove('active');
    });

    const target = document.getElementById(section);
    if (target) {
        target.classList.add('active');
    }
};

function toggleDropdown(e) {
    e.stopPropagation();
    document.getElementById('profileDropdown').classList.toggle('show');
}

document.addEventListener('click', function(e) {
    const dropdown = document.getElementById('profileDropdown');
    if (dropdown && !e.target.closest('.profile-dropdown')) {
        dropdown.classList.remove('show');
    }
});

// ===== RECEIPT MODAL =====
function openReceiptModal(bookingId) {
    fetch('get_receipt.php?id=' + bookingId)
    .then(r => r.json())
    .then(res => {
        if (res.error) { alert(res.error); return; }
        const d = res.data;

        const schedule = d.show_date
            ? new Date(d.show_date).toLocaleDateString('en-US', {month:'short',day:'numeric',year:'numeric'}) + ' • ' + (d.show_time || '')
            : 'N/A';

        let payment = d.payment_method === 'card'
            ? d.card_type + ' ****' + d.card_last4
            : d.payment_method === 'gcash'
                ? 'GCash ***' + d.gcash_last3
                : (d.payment_method || 'N/A');

        const seats = d.seats || '';
        const seatArr = seats.split(', ');

        let breakdown = '';
        seatArr.forEach(s => {
            breakdown += `<div style="display:flex;justify-content:space-between;padding:4px 0;font-size:12px;color:rgba(255,255,255,0.6);">
                <span>Seat ${s}</span><span>₱${parseFloat(d.seat_price).toFixed(2)}</span></div>`;
        });

        document.getElementById('receiptModalBody').innerHTML = `
            <div class="rm-header">
                <div class="rm-logo">Cinema</div>
                <div class="rm-sub">Payment Receipt</div>
            </div>
            <div class="rm-body">
                <div class="rm-row"><span class="rm-label">Customer</span><span class="rm-val">${d.user_name}</span></div>
                <div class="rm-row"><span class="rm-label">Movie</span><span class="rm-val">${d.title}</span></div>
                <div class="rm-row"><span class="rm-label">Schedule</span><span class="rm-val">${schedule}</span></div>
                <div class="rm-row"><span class="rm-label">Seats</span><span class="rm-val">${seats}</span></div>
                <div class="rm-row"><span class="rm-label">Payment</span><span class="rm-val">${payment}</span></div>
                <div style="margin:12px 0;padding:12px;background:rgba(255,255,255,0.03);border-radius:6px;">
                    <div style="font-size:10px;color:rgba(255,255,255,0.4);text-transform:uppercase;letter-spacing:1px;margin-bottom:8px;font-weight:600;">Seat Breakdown</div>
                    ${breakdown}
                </div>
                <div class="rm-total">
                    <span class="rm-label">Total Paid</span>
                    <span class="rm-amount">₱${parseFloat(d.total_price).toFixed(2)}</span>
                </div>
                <div class="rm-ref">REFERENCE NUMBER<span>${d.payment_reference || 'N/A'}</span></div>
            </div>
            <div class="rm-actions">
                <button class="rm-btn-dl" onclick="downloadReceipt(${d.booking_id})">Download Receipt</button>
                <button class="rm-btn-close" onclick="closeReceiptModal()">Close</button>
            </div>`;

        document.getElementById('receiptModalOverlay').classList.add('show');
    })
    .catch(err => { alert('Failed to load receipt'); console.error(err); });
}

function closeReceiptModal() {
    document.getElementById('receiptModalOverlay').classList.remove('show');
}

function downloadReceipt(bookingId) {
    fetch('get_receipt.php?id=' + bookingId)
    .then(r => r.json())
    .then(res => {
        if (res.error) { alert(res.error); return; }
        const d = res.data;
        const schedule = d.show_date
            ? new Date(d.show_date).toLocaleDateString('en-US', {month:'short',day:'numeric',year:'numeric'}) + ' • ' + (d.show_time || '')
            : 'N/A';
        let payment = d.payment_method === 'card'
            ? d.card_type + ' ****' + d.card_last4
            : d.payment_method === 'gcash' ? 'GCash ***' + d.gcash_last3 : (d.payment_method || 'N/A');
        const seats = d.seats ? d.seats.split(', ') : [];
        let seatRows = '';
        seats.forEach(s => {
            seatRows += `<div class="r-row"><span>Seat ${s}</span><span class="v">₱${parseFloat(d.seat_price).toFixed(2)}</span></div>`;
        });

        const w = window.open('', '_blank', 'width=460,height=700');
        w.document.write(`<!DOCTYPE html><html><head><title>Receipt #${d.booking_id}</title>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        <style>
            *{margin:0;padding:0;box-sizing:border-box}
            body{font-family:'Inter',sans-serif;background:#fff;display:flex;justify-content:center;padding:30px}
            .receipt{width:380px;border:2px solid #141414;border-radius:12px;overflow:hidden}
            .r-head{background:#e50914;color:#fff;padding:18px;text-align:center}
            .r-head h2{font-size:15px;font-weight:800;letter-spacing:3px;text-transform:uppercase}
            .r-head p{font-size:9px;letter-spacing:1px;text-transform:uppercase;opacity:0.85;margin-top:2px}
            .r-body{padding:22px}
            .r-success{text-align:center;margin-bottom:16px}
            .r-success h3{font-size:16px;font-weight:700;color:#141414}
            .r-success small{font-size:11px;color:#999}
            .r-info{display:flex;justify-content:space-between;padding:7px 0;font-size:11px;border-bottom:1px solid #f0f0f0;color:#666}
            .r-info:last-child{border-bottom:none}
            .r-info .v{font-weight:600;color:#141414;text-align:right;max-width:55%}
            .r-breakdown{margin:14px 0;padding:12px;background:#f8f8f8;border-radius:6px}
            .r-breakdown .title{font-size:9px;color:#999;text-transform:uppercase;letter-spacing:1.5px;margin-bottom:8px;font-weight:600}
            .r-row{display:flex;justify-content:space-between;padding:4px 0;font-size:11px;color:#555}
            .r-row .v{font-weight:600;color:#141414}
            .r-total{display:flex;justify-content:space-between;align-items:center;margin-top:12px;padding-top:12px;border-top:2px dashed #ddd}
            .r-total .lbl{font-size:12px;font-weight:600;color:#333}
            .r-total .amount{font-size:20px;font-weight:800;color:#e50914}
            .r-ref{text-align:center;margin-top:14px;padding-top:10px;border-top:1px solid #f0f0f0;font-size:8px;color:#bbb;text-transform:uppercase;letter-spacing:1.5px}
            .r-ref span{display:block;font-family:monospace;font-size:10px;color:#666;margin-top:2px}
            @media print{body{padding:0}.receipt{border:2px solid #000}}
        </style></head><body>
        <div class="receipt">
            <div class="r-head"><h2>Cinema</h2><p>Payment Receipt</p></div>
            <div class="r-body">
                <div class="r-success"><h3>Payment Successful</h3><small>Booking #${d.booking_id}</small></div>
                <div class="r-info"><span>Customer</span><span class="v">${d.user_name}</span></div>
                <div class="r-info"><span>Movie</span><span class="v">${d.title}</span></div>
                <div class="r-info"><span>Schedule</span><span class="v">${schedule}</span></div>
                <div class="r-info"><span>Seats</span><span class="v">${d.seats}</span></div>
                <div class="r-info"><span>Payment</span><span class="v">${payment}</span></div>
                <div class="r-breakdown"><div class="title">Seat Breakdown</div>${seatRows}</div>
                <div class="r-total"><span class="lbl">Total Paid</span><span class="amount">₱${parseFloat(d.total_price).toFixed(2)}</span></div>
                <div class="r-ref">Reference Number<span>${d.payment_reference || 'N/A'}</span></div>
            </div>
        </div>
        <script>setTimeout(()=>{window.print()},300)<\/script>
        </body></html>`);
        w.document.close();
    });
}

// ===== TICKET MODAL =====
function openTicketModal(data) {
    const seatArr = data.seats.split(', ');
    const schedule = data.show_date
        ? new Date(data.show_date).toLocaleDateString('en-US', {month:'short',day:'numeric',year:'numeric'}) + ' • ' + (data.show_time || '')
        : 'N/A';
    const bookedOn = new Date(data.created_at).toLocaleDateString('en-US', {month:'short',day:'numeric',year:'numeric'});

    let cards = '';
    seatArr.forEach(seat => {
        cards += `
        <div class="ticket-card">
            <div class="tc-left">
                <span class="tc-seat">Seat ${seat.trim()}</span>
                <span class="tc-movie">${data.title}</span>
                <span class="tc-date">${schedule} • Booked: ${bookedOn}</span>
            </div>
            <div class="tc-right">
                <span class="tc-price">₱${parseFloat(data.seat_price).toFixed(2)}</span>
                <button class="tc-dl" onclick="downloadTicket(${data.booking_id}, '${seat.trim()}', '${data.title.replace(/'/g,"\\'")}', '${schedule}', ${data.seat_price})">Download</button>
            </div>
        </div>`;
    });

    document.getElementById('ticketModalBody').innerHTML = `
        <div class="tm-header">
            <div class="tm-logo">Cinema</div>
            <div class="tm-sub">Tickets</div>
        </div>
        <div class="ticket-modal-body">
            <div class="tm-info">
                <h3>${data.title}</h3>
                <div class="tm-schedule">${schedule}</div>
            </div>
            ${cards}
        </div>`;

    document.getElementById('ticketModalOverlay').classList.add('show');
}

function closeTicketModal() {
    document.getElementById('ticketModalOverlay').classList.remove('show');
}

function downloadTicket(bookingId, seat, movie, schedule, price) {
    const w = window.open('', '_blank', 'width=400,height=600');
    w.document.write(`<!DOCTYPE html><html><head><title>Ticket - ${seat}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Inter',sans-serif;background:#fff;display:flex;justify-content:center;padding:30px}
        .ticket{width:320px;border:2px solid #141414;border-radius:12px;overflow:hidden}
        .t-header{background:#e50914;color:#fff;padding:16px;text-align:center}
        .t-header h2{font-size:14px;font-weight:800;letter-spacing:3px;text-transform:uppercase}
        .t-header p{font-size:9px;letter-spacing:1px;text-transform:uppercase;opacity:0.85;margin-top:2px}
        .t-body{padding:20px}
        .t-movie{font-size:18px;font-weight:700;text-align:center;margin-bottom:4px;color:#141414}
        .t-schedule{font-size:11px;text-align:center;color:#e50914;font-weight:600;margin-bottom:16px}
        .t-seat-big{text-align:center;margin:16px 0;padding:16px;background:#f5f5f5;border-radius:8px}
        .t-seat-big .lbl{font-size:9px;color:#999;text-transform:uppercase;letter-spacing:2px}
        .t-seat-big .num{font-size:28px;font-weight:800;color:#141414;margin-top:4px}
        .t-row{display:flex;justify-content:space-between;padding:6px 0;font-size:11px;color:#666;border-bottom:1px solid #f0f0f0}
        .t-row:last-child{border-bottom:none}
        .t-row .v{font-weight:600;color:#141414}
        .t-price{text-align:center;margin-top:16px;padding-top:12px;border-top:2px dashed #ddd}
        .t-price .amount{font-size:22px;font-weight:800;color:#e50914}
        .t-price .lbl{font-size:9px;color:#999;text-transform:uppercase;letter-spacing:1px}
        .t-footer{text-align:center;padding:12px;font-size:8px;color:#ccc;letter-spacing:1px;text-transform:uppercase;border-top:1px solid #f0f0f0}
        @media print{body{padding:0}.ticket{border:2px solid #000}}
    </style></head><body>
    <div class="ticket">
        <div class="t-header"><h2>Cinema</h2><p>Admission Ticket</p></div>
        <div class="t-body">
            <div class="t-movie">${movie}</div>
            <div class="t-schedule">${schedule}</div>
            <div class="t-seat-big"><div class="lbl">Seat</div><div class="num">${seat}</div></div>
            <div class="t-row"><span>Booking ID</span><span class="v">#${bookingId}</span></div>
            <div class="t-row"><span>Price</span><span class="v">₱${parseFloat(price).toFixed(2)}</span></div>
            <div class="t-price"><div class="lbl">Admit One</div><div class="amount">₱${parseFloat(price).toFixed(2)}</div></div>
        </div>
        <div class="t-footer">Present this ticket at the entrance</div>
    </div>
    <script>setTimeout(()=>{window.print()},300)<\/script>
    </body></html>`);
    w.document.close();
}

// ===== REFUND MODAL =====
let refundBookingId = null;

function openRefundModal(bookingId, title, seats, total) {
    refundBookingId = bookingId;
    document.getElementById('refundModalBody').innerHTML = `
        <div class="rf-header">
            <div class="rf-logo">Cinema</div>
            <div class="rf-sub">Refund Request</div>
        </div>
        <div class="rf-body">
            <div class="rf-info">
                <h3>Booking #${bookingId}</h3>
                <div class="rf-detail"><span class="rf-lbl">Movie</span><span class="rf-val">${title}</span></div>
                <div class="rf-detail"><span class="rf-lbl">Seats</span><span class="rf-val">${seats}</span></div>
                <div class="rf-detail"><span class="rf-lbl">Amount</span><span class="rf-val">₱${parseFloat(total).toFixed(2)}</span></div>
            </div>
            <div class="rf-warn">
                To process your refund, please enter the reference number from your receipt. 
                This action will cancel your booking and release the seats. This cannot be undone.
            </div>
            <div class="rf-input-group">
                <label>Reference Number</label>
                <input type="text" id="refundRefInput" placeholder="TXN-XXXXXXXXX-XXXXXXXXX" oninput="clearRefundError()">
                <div class="rf-error" id="refundError"></div>
            </div>
            <div class="rf-actions">
                <button class="rf-btn-confirm" id="refundConfirmBtn" onclick="submitRefund()">Confirm Refund</button>
                <button class="rf-btn-cancel" onclick="closeRefundModal()">Cancel</button>
            </div>
        </div>`;
    document.getElementById('refundModalOverlay').classList.add('show');
}

function closeRefundModal() {
    document.getElementById('refundModalOverlay').classList.remove('show');
    refundBookingId = null;
}

function clearRefundError() {
    const err = document.getElementById('refundError');
    if (err) { err.style.display = 'none'; err.textContent = ''; }
}

function submitRefund() {
    const ref = document.getElementById('refundRefInput').value.trim();
    const errEl = document.getElementById('refundError');
    const btn = document.getElementById('refundConfirmBtn');

    if (!ref) {
        errEl.textContent = 'Please enter the reference number';
        errEl.style.display = 'block';
        return;
    }

    btn.disabled = true;
    btn.textContent = 'Processing...';

    fetch('process_refund.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'booking_id=' + refundBookingId + '&reference=' + encodeURIComponent(ref)
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            closeRefundModal();
            alert(res.message);
            location.reload();
        } else {
            errEl.textContent = res.error;
            errEl.style.display = 'block';
            btn.disabled = false;
            btn.textContent = 'Confirm Refund';
        }
    })
    .catch(() => {
        errEl.textContent = 'Something went wrong. Please try again.';
        errEl.style.display = 'block';
        btn.disabled = false;
        btn.textContent = 'Confirm Refund';
    });
}

// Close modals on overlay click
document.addEventListener('click', function(e) {
    if (e.target.id === 'receiptModalOverlay') closeReceiptModal();
    if (e.target.id === 'ticketModalOverlay') closeTicketModal();
    if (e.target.id === 'refundModalOverlay') closeRefundModal();
});
</script>

<!-- RECEIPT MODAL -->
<div class="dash-modal-overlay" id="receiptModalOverlay">
    <div class="dash-modal" id="receiptModalBody">
        <button class="dash-modal-close" onclick="closeReceiptModal()">&times;</button>
    </div>
</div>

<!-- TICKET MODAL -->
<div class="dash-modal-overlay" id="ticketModalOverlay">
    <div class="dash-modal" id="ticketModalBody">
        <button class="dash-modal-close" onclick="closeTicketModal()">&times;</button>
    </div>
</div>

<!-- REFUND MODAL -->
<div class="dash-modal-overlay" id="refundModalOverlay">
    <div class="dash-modal" id="refundModalBody">
        <button class="dash-modal-close" onclick="closeRefundModal()">&times;</button>
    </div>
</div>

</body>
</html>