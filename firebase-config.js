// Konfigurasi Firebase
const firebaseConfig = {
    apiKey: "AIzaSyDYDrN3BqTIhPQMg5aiIyZKEHliTxt6Tjk",
  authDomain: "tesds-led-f9a40.firebaseapp.com",
  databaseURL: "https://tesds-led-f9a40-default-rtdb.asia-southeast1.firebasedatabase.app",
  projectId: "tesds-led-f9a40",
  storageBucket: "tesds-led-f9a40.firebasestorage.app",
  messagingSenderId: "536759320796",
  appId: "1:536759320796:web:4571e771f38b092c6cb419"
  };
  
  // Inisialisasi Firebase
  const app = firebase.initializeApp(firebaseConfig);
  const database = firebase.database();
  