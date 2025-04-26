// Referensi ke data records
const recordsRef = database.ref("records");

// Query untuk mengambil data terakhir
const latestRecordQuery = recordsRef.limitToLast(1);

// Listener untuk update realtime
latestRecordQuery.on("value", (snapshot) => {
  if (snapshot.exists()) {
    snapshot.forEach((childSnapshot) => {
      const data = childSnapshot.val();
      console.log("Data received:", data); // Untuk debugging

      // Update nilai suhu
      document.getElementById("temperatureValue").innerHTML = `${data.temperature} <span style="font-size: 1.5rem">°C</span>`;

      // Update status LED
      const statusClass = data.led_status === "ON" ? "status-on" : "status-off";
      document.getElementById("ledValue").innerHTML = `<span class="status-badge ${statusClass}">${data.led_status}</span>`;
    });
  } else {
    console.log("No data available");
    document.getElementById("temperatureValue").innerHTML = "No data";
    document.getElementById("ledValue").innerHTML = "No data";
  }
}, (error) => {
  console.error("Error:", error);
  document.getElementById("temperatureValue").innerHTML = "Error loading data";
  document.getElementById("ledValue").innerHTML = "Error loading data";
});
