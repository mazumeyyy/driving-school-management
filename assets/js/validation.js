// Login Form Validation
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('loginForm');
    if (!form) return;

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        let valid = true;

        const email = document.getElementById('email');
        const password = document.getElementById('password');
        const emailError = document.getElementById('emailError');
        const passwordError = document.getElementById('passwordError');

        // Reset
        email.classList.remove('is-invalid');
        password.classList.remove('is-invalid');

        // Validate email
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!email.value.trim()) {
            email.classList.add('is-invalid');
            emailError.textContent = 'Email is required.';
            valid = false;
        } else if (!emailRegex.test(email.value.trim())) {
            email.classList.add('is-invalid');
            emailError.textContent = 'Enter a valid email address.';
            valid = false;
        }

        // Validate password
        if (!password.value.trim()) {
            password.classList.add('is-invalid');
            passwordError.textContent = 'Password is required.';
            valid = false;
        } else if (password.value.length < 6) {
            password.classList.add('is-invalid');
            passwordError.textContent = 'Password must be at least 6 characters.';
            valid = false;
        }

        if (valid) submitLogin();
    });

    // Toggle password visibility
    const toggleBtn = document.getElementById('togglePassword');
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function () {
            const pwd = document.getElementById('password');
            const icon = document.getElementById('toggleIcon');
            if (pwd.type === 'password') {
                pwd.type = 'text';
                icon.classList.replace('bi-eye', 'bi-eye-slash');
            } else {
                pwd.type = 'password';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
            }
        });
    }
});