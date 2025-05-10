function signinBtn() {
    window.location.href = "registration.html";
}

document.getElementById("loginForm").addEventListener("submit", function (event) {
    event.preventDefault(); // prevent actual form submission
    // Optional: Add validation logic or authentication here
    window.location.href = "home.html"; // redirect after 'login'
});
