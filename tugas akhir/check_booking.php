<?php
// PASTIKAN SEMUA ERROR DITAMPILKAN SELAMA PENGEMBANGAN
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 1. KONFIGURASI DATABASE
$host = "localhost";
$user = "root"; 
$pass = "mysql";    // Password yang berhasil Anda gunakan
$db = "hotel";    // Nama Database Anda

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi Database Gagal: " . mysqli_connect_error());
}

$bookings = []; // Array untuk menyimpan hasil pemesanan
$message = ""; // Pesan status

try {
// SQL: Mengambil SEMUA data customer dan booking
$sql = "SELECT
    b.id AS booking_id, c.full_name, c.email, c.phone, b.room_type, b.total_rooms, b.check_in, b.check_out, b.guest_name, b.status
    FROM
        bookings b
    JOIN 
        customers c ON b.customer_id = c.id
    ORDER BY 
        b.check_in DESC";

// Tidak perlu Prepared Statement karena tidak ada input user (?)
    $result = mysqli_query($conn, $sql);

// Mengambil semua hasil dan menyimpannya di array $bookings
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $bookings[] = $row;
        }
        $message = "<p class='text-green-600'>Menampilkan " . count($bookings) . " total pemesanan.</p>";
    } else {
        $message = "<p class='text-yellow-600'>Belum ada data pemesanan yang tercatat.</p>";
    }

} catch (Exception $e) {
    $message = "<p class='text-red-600'>Terjadi Error: " . $e->getMessage() . "</p>";
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Booking List</title>
    <link rel="icon" href="serenity logo.png">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100 min-h-screen p-8">
    <div class="max-w-7xl mx-auto bg-white p-6 rounded-lg shadow-lg">
        <h1 class="text-3xl font-bold mb-4 text-gray-800 border-b pb-2">Daftar Semua Pemesanan</h1>

    <a href="landing.html" class="inline-block bg-gray-500 hover:bg-gray-700 text-white font-semibold px-4 py-2 rounded-md mb-4 transition duration-300">
      ← Kembali ke Landing Page
    </a>
            <?php echo $message; ?>

        <?php if (!empty($bookings)): ?>
        <div class="overflow-x-auto mt-6">
            <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Booking</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Telp</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kamar & Jmlh</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check In</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check Out</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
            <?php foreach ($bookings as $booking): ?>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($booking['booking_id']); ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($booking['full_name']); ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($booking['email']); ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($booking['phone']); ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($booking['room_type']) . " (" . htmlspecialchars($booking['total_rooms']) . " rooms)"; ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($booking['check_in']); ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($booking['check_out']); ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
            <?php 
// Logika untuk mewarnai status
                    $status = $booking['status'];
                    if ($status == 'Confirmed') echo 'bg-green-100 text-green-800';
                    elseif ($status == 'Pending') echo 'bg-yellow-100 text-yellow-800';
                    else echo 'bg-red-100 text-red-800';
                    ?>">
                    <?php echo htmlspecialchars($status); ?>
                </span>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            </table>
        </div>
    <?php else: ?>
    <p class="text-center text-gray-500 mt-4">Tidak ada data pemesanan yang ditemukan.</p>
    <?php endif; ?>
    </div>
</body>
</html>