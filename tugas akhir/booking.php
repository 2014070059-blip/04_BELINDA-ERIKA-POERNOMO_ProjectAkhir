<?php
// Konfigurasi Database
$host = "localhost";
$user = "root";
$pass = "mysql";
$db = "hotel"; 

// Koreksi Koneksi
$conn = mysqli_connect($host, $user, $pass, $db); // <-- Gunakan 4 parameter

if (!$conn) {
    die("Koneksi Database Gagal: " . mysqli_connect_error());
}


// Mulai transaksi untuk memastikan kedua INSERT berhasil atau gagal bersama
mysqli_begin_transaction($conn);

try {
    // 1. Ambil dan sanitasi data form
    $full_name   = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email       = mysqli_real_escape_string($conn, $_POST['email']);
    $phone       = mysqli_real_escape_string($conn, $_POST['phone']);
    $room_type   = mysqli_real_escape_string($conn, $_POST['room_type']);
    $total_rooms = mysqli_real_escape_string($conn, $_POST['total_rooms']);
    $check_in    = mysqli_real_escape_string($conn, $_POST['check_in']);
    $check_out   = mysqli_real_escape_string($conn, $_POST['check_out']);
    $guest_name  = mysqli_real_escape_string($conn, $_POST['guest_name']);
    $note        = mysqli_real_escape_string($conn, $_POST['note']);

    // 2. Insert customer
    $sql1 = "INSERT INTO customers (full_name, email, phone) 
             VALUES ('$full_name', '$email', '$phone')";
    
    if (!mysqli_query($conn, $sql1)) {
        // Jika INSERT customer gagal
        throw new Exception("Error saat Insert Customer: " . mysqli_error($conn));
    }
    
    $customer_id = mysqli_insert_id($conn);
    
    // 3. Insert booking menggunakan customer_id yang baru dibuat
    $sql2 = "INSERT INTO bookings (customer_id, room_type, total_rooms, check_in, check_out, guest_name, note) 
             VALUES ('$customer_id', '$room_type', '$total_rooms', '$check_in', '$check_out', '$guest_name', '$note')";

    if (!mysqli_query($conn, $sql2)) {
        // Jika INSERT booking gagal
        throw new Exception("Error saat Insert Booking: " . mysqli_error($conn));
    }

    // Jika semua berhasil, COMMIT transaksi dan redirect
    mysqli_commit($conn);
    mysqli_close($conn);
    header("Location: success.html");
    exit();

} catch (Exception $e) {
    // Jika terjadi error, ROLLBACK transaksi (membatalkan semua perubahan)
    mysqli_rollback($conn);
    mysqli_close($conn);
    echo "<h2>Proses Booking GAGAL</h2>";
    echo "<p>Pesan Error: " . $e->getMessage() . "</p>";
    echo "<p>Silakan periksa apakah tabel 'customers' dan 'bookings' sudah ada dan memiliki kolom yang benar di database 'hotel_serenity'.</p>";
}

?>