
const form = document.getElementById("registrationForm");

form.addEventListener("submit", function(event) {
    event.preventDefault();

    document.querySelectorAll(".error").forEach(function(error) {
        error.textContent = "";
    });

    document.getElementById("successMessage").textContent = "";

    let valid = true;

    const name = document.getElementById("name").value.trim();
    const roll = document.getElementById("roll").value.trim();
    const email = document.getElementById("email").value.trim();
    const course = document.getElementById("course").value;
    const password = document.getElementById("password").value;
    const file = document.getElementById("file").files[0];

    if (name === "") {
        document.getElementById("nameError").textContent =
            "Name is required";
        valid = false;
    }

    if (roll === "") {
        document.getElementById("rollError").textContent =
            "Roll number is required";
        valid = false;
    }

    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (email === "") {
        document.getElementById("emailError").textContent =
            "Email is required";
        valid = false;
    } else if (!emailPattern.test(email)) {
        document.getElementById("emailError").textContent =
            "Enter a valid email address";
        valid = false;
    }

    if (course === "") {
        document.getElementById("courseError").textContent =
            "Please select a course";
        valid = false;
    }

    const passwordPattern =
        /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/;

    if (password === "") {
        document.getElementById("passwordError").textContent =
            "Password is required";
        valid = false;
    } else if (!passwordPattern.test(password)) {
        document.getElementById("passwordError").textContent =
            "Use 8+ characters with uppercase, lowercase, number and special character";
        valid = false;
    }

    if (!file) {
        document.getElementById("fileError").textContent =
            "Please upload a document";
        valid = false;
    } else if (file.size > 2 * 1024 * 1024) {
        document.getElementById("fileError").textContent =
            "File size must be below 2 MB";
        valid = false;
    } else if (!/\.(pdf|jpg|jpeg|png)$/i.test(file.name)) {
        document.getElementById("fileError").textContent =
            "Upload a PDF, JPG or PNG file";
        valid = false;
    }

    if (valid) {
        document.getElementById("successMessage").textContent =
            "Registration form validated successfully!";
        form.reset();
    }
});
