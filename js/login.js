document.getElementById("loginForm").addEventListener("submit", function(event) {
    event.preventDefault();

    let username = document.getElementById("username").value;
    let password = document.getElementById("password").value;

    // Simple validation
    if (username === "" || password === "") {
        document.getElementById("error-message").textContent = "Please fill in all fields.";
        return;
    }

    // Send data via AJAX
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "actions/login.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            let response = JSON.parse(xhr.responseText);
            if (response.success) {
                window.location.href = "dashboard.html";  // Redirect to dashboard
            } else {
                document.getElementById("error-message").textContent = response.message;
            }
        }
    };

    let data = "username=" + encodeURIComponent(username) + "&password=" + encodeURIComponent(password);
    xhr.send(data);
});
