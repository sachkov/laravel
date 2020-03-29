importScripts('https://www.gstatic.com/firebasejs/7.13.1/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/7.13.1/firebase-messaging.js');

// Your web app's Firebase configuration
var firebaseConfig = {
    apiKey: "AIzaSyDVSwUzmTVJCqvVYts0Ur_GG4AR9WNAS7Q",
    authDomain: "probuzhdenie-956a9.firebaseapp.com",
    databaseURL: "https://probuzhdenie-956a9.firebaseio.com",
    projectId: "probuzhdenie-956a9",
    storageBucket: "probuzhdenie-956a9.appspot.com",
    messagingSenderId: "152744733596",
    appId: "1:152744733596:web:370e56b77f7bcff9a13659"
};
// Initialize Firebase
firebase.initializeApp(firebaseConfig);

// Retrieve an instance of Firebase Messaging so that it can handle background
// messages.
const messaging = firebase.messaging();