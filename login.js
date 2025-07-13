document.getElementById("loginForm").addEventListener("submit", function (event) {
    event.preventDefault();

    const email = document.getElementById("email").value;
    const password = document.getElementById("password").value;
    const role = document.getElementById("role").value; // Alumni or Admin

    fetch("http://localhost/xie_alumni/login.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ email, password, role })
    })
    .then(response => response.json())
    .then(data => {
        const message = document.getElementById("message");
        if (data.success) {
            message.style.color = "green";
            message.innerText = "✅ Login Successful!";
            setTimeout(() => {
                window.location.href = data.role === "admin" 
                ? "http://localhost/xie_alumni/admin.php" 
                : "http://localhost/xie_alumni/alumni.php";
            
            }, 1000);
        } else {
            message.style.color = "red";
            message.innerText = "❌ " + data.message;
        }
    })
    .catch(error => console.error("Error:", error));
});
