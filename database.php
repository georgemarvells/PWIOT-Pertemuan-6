<?php 
// Konfigurasi Database MySQL
define('DB_HOST', 'localhost');     // Host database
define('DB_USERNAME', 'root');      // Username database
define('DB_PASSWORD', '');          // Password database
define('DB_NAME', 'sensor_db');     // Nama database

// Fungsi untuk membuat koneksi database
function connectDB() {
    $conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

    if ($conn->connect_error) {
        throw new Exception("Koneksi database gagal: " . $conn->connect_error);
    }

    $conn->set_charset("utf8");
    return $conn;
}

// Membuat koneksi
$conn = connectDB();

// Fungsi untuk menutup koneksi database
function closeConnection($conn) {
    if ($conn) {
        $conn->close();
    }
}

// Fungsi untuk escape string (mencegah SQL injection)
function escapeString($conn, $string) {
    return $conn->real_escape_string($string);
}

// Fungsi untuk mengetes koneksi dan tabel
function testDatabaseConnection() {
    global $conn;
    
    try {
        // Test 1: Cek koneksi database
        if ($conn->ping()) {
            echo "<div style='color: green;'>✓ Koneksi ke database berhasil</div>";
        } else {
            echo "<div style='color: red;'>✗ Koneksi ke database gagal</div>";
            return false;
        }

        // Test 2: Cek keberadaan tabel sensor_data
        $table_check = $conn->query("SHOW TABLES LIKE 'sensor_data'");
        if ($table_check->num_rows > 0) {
            echo "<div style='color: green;'>✓ Tabel sensor_data ditemukan</div>";
        } else {
            echo "<div style='color: red;'>✗ Tabel sensor_data tidak ditemukan</div>";

            // Buat tabel jika belum ada
            $create_table = "CREATE TABLE sensor_data (
                id INT AUTO_INCREMENT PRIMARY KEY,
                temperature FLOAT NOT NULL,
                led_status ENUM('ON', 'OFF') NOT NULL,
                timestamp DATETIME DEFAULT CURRENT_TIMESTAMP
            )";

            if ($conn->query($create_table)) {
                echo "<div style='color: green;'>✓ Tabel sensor_data berhasil dibuat</div>";
            } else {
                echo "<div style='color: red;'>✗ Gagal membuat tabel: " . $conn->error . "</div>";
                return false;
            }
        }

        // Test 3: Coba insert data test
        $test_insert = "INSERT INTO sensor_data (temperature, led_status) VALUES (25.5, 'OFF')";
        if ($conn->query($test_insert)) {
            echo "<div style='color: green;'>✓ Test insert data berhasil</div>";

            // Hapus data test
            $conn->query("DELETE FROM sensor_data WHERE temperature = 25.5 ORDER BY id DESC LIMIT 1");
        } else {
            echo "<div style='color: red;'>✗ Gagal insert data test: " . $conn->error . "</div>";
            return false;
        }

        return true;
    } catch (Exception $e) {
        echo "<div style='color: red;'>Error: " . $e->getMessage() . "</div>";
        return false;
    }
}

// Jika file diakses langsung, jalankan test
if (basename($_SERVER['PHP_SELF']) == 'database.php') {
    echo "<h2>Test Koneksi Database</h2>";
    echo "<div style='font-family: Arial, sans-serif; padding: 20px;'>";
    
    echo "<h3>Informasi Konfigurasi:</h3>";
    echo "<pre>";
    echo "Host: " . DB_HOST . "\n";
    echo "Database: " . DB_NAME . "\n";
    echo "Username: " . DB_USERNAME . "\n";
    echo "Password: " . (empty(DB_PASSWORD) ? "(kosong)" : "****") . "\n";
    echo "</pre>";

    echo "<h3>Hasil Test:</h3>";
    testDatabaseConnection();

    echo "<br><div style='margin-top: 20px;'>";
    echo "<a href='../index.html' style='text-decoration: none;'>";
    echo "<button style='padding: 10px 20px; background-color: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer;'>";
    echo "Kembali ke Halaman Utama";
    echo "</button>";
    echo "</a>";
    echo "</div>";
    echo "</div>";
}
?>
