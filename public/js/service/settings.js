// Initialize Firebase
firebase.initializeApp(GFB.firebaseConfig);
// Retrieve Firebase Messaging object.
const messaging = firebase.messaging();
// Add the public key generated from the console here.
messaging.usePublicVapidKey(GFB.PublicVapidKey);


$( document ).ready(function(){
    //Проверка дал ли пользователь разрешение на уведомления
    //console.log(Notification.permission);
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
            console.log("new token "+currentToken);
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
        function(data){
            console.log("token was "+data.status);
        },
        ()=>{}
    );
}