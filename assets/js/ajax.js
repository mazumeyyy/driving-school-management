// AJAX Login Handler
function submitLogin() {
    const btn = document.getElementById('loginBtn');
    const btnText = document.getElementById('loginBtnText');
    const spinner = document.getElementById('loginSpinner');
    const alert = document.getElementById('loginAlert');

    // Show loading state
    btn.disabled = true;
    btnText.textContent = 'Signing in...';
    spinner.classList.remove('d-none');
    alert.classList.add('d-none');

    const formData = new FormData(document.getElementById('loginForm'));

    fetch('php/auth/login.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            alert.className = 'alert alert-success';
            alert.textContent = 'Login successful! Redirecting...';
            alert.classList.remove('d-none');
            setTimeout(() => window.location.href = data.redirect, 1000);
        } else {
            alert.className = 'alert alert-danger';
            alert.textContent = data.message || 'Invalid email or password.';
            alert.classList.remove('d-none');
            btn.disabled = false;
            btnText.textContent = 'Sign In';
            spinner.classList.add('d-none');
        }
    })
    .catch(() => {
        alert.className = 'alert alert-danger';
        alert.textContent = 'Something went wrong. Please try again.';
        alert.classList.remove('d-none');
        btn.disabled = false;
        btnText.textContent = 'Sign In';
        spinner.classList.add('d-none');
    });
}