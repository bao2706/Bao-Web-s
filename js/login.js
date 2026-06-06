const emailInput = document.getElementById('email');
const passwordInput = document.getElementById('password');
const nameInput = document.getElementById('name');

if (emailInput) {
    emailInput.addEventListener('input', () => {
        emailInput.classList.remove('is-invalid');
    });
}

if (passwordInput) {
    passwordInput.addEventListener('input', () => {
        passwordInput.classList.remove('is-invalid');
    });
}

if (nameInput) {
    nameInput.addEventListener('input', () => {
        nameInput.classList.remove('is-invalid');
    });
}
console.log('Login script loaded');