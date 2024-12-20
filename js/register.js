document.getElementById("signUpForm").addEventListener("submit", function (event) {
    event.preventDefault();

    // Retrieve form values
    const firstname = document.getElementById("firstname").value.trim();
    const lastname = document.getElementById("lastname").value.trim();
    const username = document.getElementById("username").value.trim();
    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value;
    const confirm_password = document.getElementById("confirm_password").value;

    // Regex validation
    const nameRegex = /^[a-zA-Z\s]{1,50}$/;
    const usernameRegex = /^[a-zA-Z0-9_]{3,20}$/;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const passwordRegex = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,20}$/;

    if (!nameRegex.test(firstname)) {
        document.getElementById("error-message").textContent = "Invalid first name.";
        return;
    }
    if (!nameRegex.test(lastname)) {
        document.getElementById("error-message").textContent = "Invalid last name.";
        return;
    }
    if (!usernameRegex.test(username)) {
        document.getElementById("error-message").textContent = "Invalid username.";
        return;
    }
    if (!emailRegex.test(email)) {
        document.getElementById("error-message").textContent = "Invalid email format.";
        return;
    }
    if (!passwordRegex.test(password)) {
        document.getElementById("error-message").textContent =
            "Password must be 6-20 characters and include at least one number, one lowercase, and one uppercase letter.";
        return;
    }
    if (password !== confirm_password) {
        document.getElementById("error-message").textContent = "Passwords do not match.";
        return;
    }

    // Send data via AJAX
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "../actions/register.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            if (response.success) {
                alert(response.message);
                window.location.href = "login.php"; // Redirect to login page
            } else {
                document.getElementById("error-message").textContent = response.message;
            }
        }
    };

    const data = `firstname=${encodeURIComponent(firstname)}&lastname=${encodeURIComponent(lastname)}&username=${encodeURIComponent(username)}&email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}`;
    xhr.send(data);
});
