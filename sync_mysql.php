<?php
require_once 'database.php'; // Including the database connection configuration

// Set timezone to Asia/Jakarta
date_default_timezone_set('Asia/Jakarta');

// Firebase URL
$firebase_url = "https://tesds-led-f9a40-default-rtdb.asia-southeast1.firebasedatabase.app/.json";

try {
    // Create a MySQL connection
    $conn = connectDB();

    // Initialize cURL for Firebase data fetching
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $firebase_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Add debug information
    error_log("Attempting to access Firebase URL: " . $firebase_url);

    // Execute cURL request
    $response = curl_exec($ch);

    // Log response for debugging
    error_log("Firebase response: " . $response);

    if (curl_errno($ch)) {
        throw new Exception('Error retrieving data from Firebase: ' . curl_error($ch));
    }

    curl_close($ch);

    // Decode the JSON response
    $data = json_decode($response, true);

    // Debug: Check decoded data
    error_log("Decoded Firebase data: " . print_r($data, true));

    if ($data && isset($data['records'])) {
        // Prepare MySQL statement for insertion
        $stmt = $conn->prepare("INSERT INTO sensor_data (temperature, led_status, timestamp) VALUES (?, ?, ?)");

        $success_count = 0;
        $error_count = 0;

        // Loop through each record in Firebase data
        foreach ($data['records'] as $timestamp => $record) {
            // Ensure data is complete
            if (!isset($record['temperature']) || !isset($record['led_status'])) {
                error_log("Incomplete data for timestamp: " . $timestamp);
                continue;
            }

            // Convert timestamp from Firebase (milliseconds to seconds)
            $timestamp_seconds = floor($timestamp / 1000);
            $formatted_timestamp = date('Y-m-d H:i:s', $timestamp_seconds);

            // Debug: Check timestamp conversion
            error_log("Original timestamp: " . $timestamp);
            error_log("Converted timestamp (seconds): " . $timestamp_seconds);
            error_log("Formatted timestamp: " . $formatted_timestamp);

            // Debug: Check data before insertion
            error_log("Data to insert: " . print_r([
                'temperature' => $record['temperature'],
                'led_status' => $record['led_status'],
                'timestamp' => $formatted_timestamp
            ], true));

            // Bind parameters and execute the query
            $stmt->bind_param("dss", $record['temperature'], $record['led_status'], $formatted_timestamp);

            // Execute the query and handle success/failure
            if ($stmt->execute()) {
                $success_count++;
                error_log("Successfully inserted data into MySQL.");
            } else {
                $error_count++;
                error_log("Failed to insert data: " . $stmt->error);
            }
        }

        // Close the statement
        $stmt->close();

        // Send response with success details
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => "Synchronization completed",
            'details' => [
                'success_count' => $success_count,
                'error_count' => $error_count,
                'data' => $data['records']
            ]
        ]);
    } else {
        throw new Exception("No data available in Firebase or data format is incorrect.");
    }

    // Close the MySQL connection
    $conn->close();
} catch (Exception $e) {
    // Log the error
    error_log("Error in sync_mysql.php: " . $e->getMessage());

    // Send error response
    header('Content-Type: application/json');
    http_response_code(500); // Internal server error
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>