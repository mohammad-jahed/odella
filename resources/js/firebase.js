// Import the functions you need from the SDKs you need
import { initializeApp } from "firebase/app";
import { getAnalytics } from "firebase/analytics";
import { getMessaging , getToken} from "firebase/messaging";

// TODO: Add SDKs for Firebase products that you want to use
// https://firebase.google.com/docs/web/setup#available-libraries

// Your web app's Firebase configuration
// For Firebase JS SDK v7.20.0 and later, measurementId is optional
const firebaseConfig = {
    apiKey: "AIzaSyC0FfjuV0wjS1W__EzJ2MBQ0IGZONGvruE",
    authDomain: "odella-f86e7.firebaseapp.com",
    projectId: "odella-f86e7",
    storageBucket: "odella-f86e7.appspot.com",
    messagingSenderId: "488432009966",
    appId: "1:488432009966:web:99f7cfa8317ef8c29fe9ff",
    measurementId: "G-MH2VZWSRDD"
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);
const analytics = getAnalytics(app);
const messaging = getMessaging();
getToken(messaging, { vapidKey: 'BADr7YzusqYC1b26yOkBypW0SmC91ck1XHdUr9eF__m5cCZKsrejGTUJ6aU1t8jS7PfgFQouhOtuzIUwX7e-RY4' }).then((currentToken) => {
    if (currentToken) {
        // Send the token to your server and update the UI if necessary
        // ...
        console.log(currentToken);
    } else {
        // Show permission request UI
        console.log('No registration token available. Request permission to generate one.');
        // ...
    }
}).catch((err) => {
    console.log('An error occurred while retrieving token. ', err);
    // ...
});
