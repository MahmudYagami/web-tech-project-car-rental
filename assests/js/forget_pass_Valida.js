document.getElementById("submit-btn").addEventListener("click", function (e) {
  e.preventDefault();

  const email = document.getElementById("email").value.trim();
  const currentPassword = document.getElementById("cur_password").value;
  const newPassword = document.getElementById("new_password").value;

  // Email validation
  if (!email || !email.includes("@") || !email.includes(".")) {
    alert("Please enter a valid email address.");
    return;
  }

  // Current password check
  if (!currentPassword) {
    alert("Please enter your current password.");
    return;
  }

  // New password strength check
  let hasUpper = /[A-Z]/.test(newPassword);
  let hasLower = /[a-z]/.test(newPassword);
  let hasDigit = /[0-9]/.test(newPassword);
  let hasSpecial = /[!@#$%^&*(),.?":{}|<>]/.test(newPassword);

  if (newPassword.length < 8 || !hasUpper || !hasLower || !hasDigit || !hasSpecial) {
    alert("New password must be at least 8 characters long and include uppercase, lowercase, digit, and special character.");
    return;
  }

  // Check if new password is different from current
  if (currentPassword === newPassword) {
    alert("New password must be different from current password.");
    return;
  }

  alert("Password reset request validated. (Form would submit here)");
  
});
