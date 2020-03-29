
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
// Retrieve Firebase Messaging object.
const messaging = firebase.messaging();
// Add the public key generated from the console here.
messaging.usePublicVapidKey("BLD-hYtKj86Z6nH4Zifo0_yBqnNYLmrvHoyTp8oJ83MMzliMD3tbBlKX7IBHI4t3c-AUOUE7yQ2H_Gs9Y7NILbU");


$( document ).ready(function(){
    //Проверка дал ли пользователь разрешение на уведомления
    console.log(Notification.permission);
    if (Notification.permission === "granted") {
        $("#notification").prop("checked", true);
        $("#notification").prop("disabled", false);
    }else if(Notification.permission !== "denied"){
        $("#notification").prop("disabled", false);
    }

    $("#notification").on("change", function(){
        if($("#notification").prop("checked")){
            Notification.requestPermission(function (permission){
                if(permission === "granted") {
                    getToken();
                }else if(permission === "denied"){
                    $("#notification")
                        .prop("checked", false)
                        .prop("disabled", true);
                }else{
                    $("#notification").prop("checked", false);
                }
            });
        }else{
            sendTokenToServer("");
        }
    });
});

function getToken(){
// Get Instance ID token. Initially this makes a network call, once retrieved
// subsequent calls to getToken will return from cache.
    messaging.getToken().then((currentToken) => {
        if (currentToken) {
            sendTokenToServer(currentToken);
            console.log("TOKEN");
            console.log(currentToken);
            //updateUIForPushEnabled(currentToken);
        } else {
            // Show permission request.
            console.log('No Instance ID token available. Request permission to generate one.');
            sendTokenToServer("");
        }
    }).catch((err) => {
        console.log('An error occurred while retrieving token. ', err);
        sendTokenToServer("");
    });
}

function sendTokenToServer(token = ""){
    console.log(token);
    if(token == "") console.log("empty token");
}

function detectMob() {
    return ((window.innerWidth <= 800) && (window.innerHeight <= 600));
}

function sendTokenToServer(token){
    let site = detectMob?"mobile":"desctop";
    globalAjax(
        "/personal/saveToken",
        {
            token: token,
            site: site
        },
        function(){
            console.log("request was sent");
        },
        ()=>{}
    );
}