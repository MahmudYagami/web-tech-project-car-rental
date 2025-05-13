document.getElementById("submit-btn").addEventListener("click", function (e) {
  e.preventDefault();

  const email = document.getElementById("email");
  const password = document.getElementById("password");

  let isValid = true;

  clearMessages();
  if (!email.value.trim()) {
    showError(email, "Email is required.");
    isValid = false;
  } else if (!email.value.includes("@") || !email.value.includes(".") || email.value.indexOf("@") > email.value.lastIndexOf(".")) {
    showError(email, "Invalid email format.");
    isValid = false;
  } else {
    showSuccess(email);
  }

  // Password check
  if (!password.value.trim()) {
    showError(password, "Password is required.");
    isValid = false;
  } else {
    showSuccess(password);
  }

  if (isValid) {
  document.getElementById("login-form").submit();
}
});

function showError(input, message) {
  input.classList.add("error");
  input.classList.remove("success");

  let messageEl = input.nextElementSibling;
  if (!messageEl || !messageEl.classList.contains("message")) {
    messageEl = document.createElement("div");
    messageEl.className = "message";
    input.parentNode.appendChild(messageEl);
  }
  messageEl.innerText = message;
}

function showSuccess(input) {
  input.classList.remove("error");
  input.classList.add("success");

  let messageEl = input.nextElementSibling;
  if (messageEl && messageEl.classList.contains("message")) {
    messageEl.innerText = "";
  }
}

function clearMessages() {
  document.querySelectorAll(".message").forEach(el => el.innerText = "");
  document.querySelectorAll(".error").forEach(el => el.classList.remove("error"));
  document.querySelectorAll(".success").forEach(el => el.classList.remove("success"));
}
