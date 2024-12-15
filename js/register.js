document.getElementById("signupForm").addEventListener("submit", function(event) {
    event.preventDefault();

    let username = document.getElementById("username").value;
    let email = document.getElementById("email").value;
    let password = document.getElementById("password").value;
    let confirmPassword = document.getElementById("confirm-password").value;

    // Validate username and email with regex
    const usernameRegex = /^[a-zA-Z0-9_]{3,20}$/;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const passwordRegex = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,20}$/;

    if (!usernameRegex.test(username)) {
        document.getElementById("error-message").textContent = "Invalid username.";
        return;
    }

    if (!emailRegex.test(email)) {
        document.getElementById("error-message").textContent = "Invalid email format.";
        return;
    }

    if (!passwordRegex.test(password)) {
        document.getElementById("error-message").textContent = "Password must be between 6-20 characters and include at least one number, one lowercase, and one uppercase letter.";
        return;
    }

    if (password !== confirmPassword) {
        document.getElementById("error-message").textContent = "Passwords do not match.";
        return;
    }

    // Send data via AJAX
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "actions/register.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            let response = JSON.parse(xhr.responseText);
            if (response.success) {
                window.location.href = "login.html";  // Redirect to login page
            } else {
                document.getElementById("error-message").textContent = response.message;
            }
        }
    };

    let data = "username=" + encodeURIComponent(username) + "&email=" + encodeURIComponent(email) + "&password=" + encodeURIComponent(password);
    xhr.send(data);
});
