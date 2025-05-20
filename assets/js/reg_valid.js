document.getElementById("submit-btn").addEventListener("click", function (e) {
  e.preventDefault();

  // Get input values
  const fname = document.getElementById("firstname").value.trim();
  const lname = document.getElementById("lastname").value.trim();
  const email = document.getElementById("email").value.trim();
  const password = document.getElementById("password").value;
  const repassword = document.getElementById("repassword").value;
  const mobile = document.getElementById("mobile").value.trim();
  const country = document.getElementById("country").value;
  const address = document.getElementById("address").value;
  const dob = document.getElementById("dob").value;

  // Get message elements
  const emailMsg = document.getElementById("email-message");
  const passMsg = document.getElementById("password-message");
  const repassMsg = document.getElementById("repassword-message");
  const mobileMsg = document.getElementById("mobile-message");
  const generalMsg = document.getElementById("general-message");

  // Clear all messages first
  emailMsg.innerText = "";
  passMsg.innerText = "";
  repassMsg.innerText = "";
  mobileMsg.innerText = "";
  generalMsg.innerText = "";

  // Check for empty fields
  if (
    !fname ||
    !lname ||
    !email ||
    !password ||
    !mobile ||
    !repassword ||
    !country ||
    !address ||
    !dob
  ) {
    generalMsg.innerText = "All fields are required.";
    generalMsg.style.color = "red";
    return;
  }

  // Email check
  if (
    !email.includes("@") ||
    !email.includes(".") ||
    email.indexOf("@") > email.lastIndexOf(".")
  ) {
    emailMsg.innerText = "Invalid email format.";
    emailMsg.style.color = "red";
    return;
  }

  // Password strength check
  let hasUpper = false,
    hasLower = false,
    hasDigit = false,
    hasSpecial = false;
  let specials = "!@#$%^&*()_+-=[]{};:'\",.<>/?";

  for (let i = 0; i < password.length; i++) {
    const ch = password[i];
    if (ch >= "A" && ch <= "Z") hasUpper = true;
    else if (ch >= "a" && ch <= "z") hasLower = true;
    else if (ch >= "0" && ch <= "9") hasDigit = true;
    else if (specials.includes(ch)) hasSpecial = true;
  }

  if (
    password.length < 8 ||
    !hasUpper ||
    !hasLower ||
    !hasDigit ||
    !hasSpecial
  ) {
    passMsg.innerText =
      "Password must be at least 8 characters and include uppercase, lowercase, digit, and special character.";
    passMsg.style.color = "red";
    return;
  }

  // Confirm password
  if (password !== repassword) {
    repassMsg.innerText = "Passwords do not match.";
    repassMsg.style.color = "red";
    return;
  }

  // Mobile check
  if (mobile.length !== 11 || isNaN(Number(mobile))) {
    mobileMsg.innerText = "Mobile number must be 11 digits and numeric.";
    mobileMsg.style.color = "red";
    return;
  }

  // All good
  generalMsg.innerText = "Registration successful! Redirecting...";
  generalMsg.style.color = "green";
setTimeout(() => {
  document.getElementById("registration-form").submit();
}, 1000);
});
