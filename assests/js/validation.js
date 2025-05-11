document.getElementById("submit-btn").addEventListener("click",function(e){e.preventDefault();


  const fname = document.getElementById("firstname").value.trim();
  const lname = document.getElementById("lastname").value.trim();
  const email = document.getElementById("email").value.trim();
  const password = document.getElementById("password").value;
  const mobile = document.getElementById("mobile").value.trim();
  const repassword = document.getElementById("repassword").value;
  const country = document.getElementById("country").value;
  const address = document.getElementById("address").value;
  const dob = document.getElementById("dob").value;

    if(!fname||!lname||!email||!password||!mobile||!repassword||!country||!address||!dob){
        alert("All fields are required.");
        return;
    }else{
        alert("All fields are filled. You can now proceed.");
        window.location.replace("login.php");
    }



});
