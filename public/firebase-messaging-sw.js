importScripts(
    "https://www.gstatic.com/firebasejs/11.0.0/firebase-app-compat.js"
);
importScripts(
    "https://www.gstatic.com/firebasejs/11.0.0/firebase-messaging-compat.js"
);

const firebaseConfig = {
    apiKey: "AIzaSyApIzl1uy6clfvPKs7W9e54Io1IP6b5JRY",
    authDomain: "imst-b2007.firebaseapp.com",
    projectId: "imst-b2007",
    storageBucket: "imst-b2007.firebasestorage.app",
    messagingSenderId: "613940030216",
    appId: "1:613940030216:web:7363bd0b71117efc1fdabd",
    measurementId: "G-1RTB5Q83MK",
};

firebase.initializeApp(firebaseConfig);
const messaging = firebase.messaging();
