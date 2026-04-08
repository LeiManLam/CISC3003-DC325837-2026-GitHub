// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Get elements
    const container = document.querySelector('.container');
    const signInBtn = document.getElementById('signInBtn');
    const signUpBtn = document.getElementById('signUpBtn');
    const registerForm = document.getElementById('registerForm');
    const loginForm = document.getElementById('loginForm');

    // Event listener for Sign In button - switches to login view
    if (signInBtn) {
        signInBtn.addEventListener('click', function() {
            container.classList.add('sign-in-mode');
        });
    }

    // Event listener for Sign Up button - switches to register view
    if (signUpBtn) {
        signUpBtn.addEventListener('click', function() {
            container.classList.remove('sign-in-mode');
        });
    }

    // Form validation and submission for Register form
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const fullName = document.getElementById('fullName').value;
            const email = document.getElementById('signUpEmail').value;
            const password = document.getElementById('signUpPassword').value;

            if (fullName && email && password) {
                alert('Registration successful!\nName: ' + fullName + '\nEmail: ' + email);
                registerForm.reset();
            } else {
                alert('Please fill in all required fields: Full Name, Email, and Create Password');
            }
        });
    }

    // Form validation and submission for Login form
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const email = document.getElementById('loginEmail').value;
            const password = document.getElementById('loginPassword').value;

            if (email && password) {
                alert('Login successful!\nEmail: ' + email);
                loginForm.reset();
            } else {
                alert('Please fill in all required fields: Email and Password');
            }
        });
    }
});
