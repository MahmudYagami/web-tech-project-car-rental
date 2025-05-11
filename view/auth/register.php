<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="..\..\assests\css\registration_style.css">
    <title>Registration Form</title>
  </head>
  <body>
    <div class="form-container">
      <h1>Registration</h1>
      <form id="registration-form">
        <label for="firstname">First Name:</label>
        <input type="text" id="firstname" name="first_name" required />

        <label for="lastname">Last Name:</label>
        <input type="text" id="lastname" name="last_name" required />

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required />
        <div id="email-message" class="message"></div>

        <label for="mobile">Mobile:</label>
        <input
          type="tel"
          id="mobile"
          name="mobile"
          pattern="\d{11}"
          title="Enter a valid 11-digit mobile number"
          required
        />

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required />
        <div id="password-message" class="message"></div>

        <label for="repassword">Retype Password:</label>
        <input
          type="password"
          id="repassword"
          name="retype_password"
          required
        />
        <div id="repassword-message" class="message"></div>

        <label for="country">Country:</label>
        <select id="country" name="country" required>
          <option value="Bangladesh">Bangladesh</option>
          <option value="Nepal">Nepal</option>
          <option value="UK">United Kingdom</option>
        </select>

        <label for="address">Address:</label>
        <input type="text" id="address" name="address" required />

        <label for="dob">Date of Birth:</label>
        <input type="date" id="dob" name="dob" required />

        <button type="button" id="submit-btn">Sign Up</button>
      </form>

      <!-- Login Link -->
      <div class="login-link">
        <span
          >Already have an account?
          <a href="login.php">Login here</a></span
        >
      </div>
    </div>

    <script src="..\..\assests\js\validation.js">
    </script>
  </body>
</html>