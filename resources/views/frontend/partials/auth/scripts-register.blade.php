<script>
    const passwordInput = document.getElementById('password');
    const strengthFill = document.getElementById('strengthFill');
    const strengthText = document.getElementById('strengthText');
    const confirmPasswordInput = document.getElementById('password_confirmation');
    const passwordMatch = document.getElementById('passwordMatch');

    if (passwordInput && strengthFill && strengthText) {
        passwordInput.addEventListener('input', function () {
            const password = this.value;
            let strength = 0;

            if (password.length >= 8) strength++;
            if (password.match(/[a-z]/)) strength++;
            if (password.match(/[A-Z]/)) strength++;
            if (password.match(/[0-9]/)) strength++;
            if (password.match(/[^a-zA-Z0-9]/)) strength++;

            const percentage = (strength / 5) * 100;
            strengthFill.style.width = percentage + '%';

            if (percentage <= 20) {
                strengthFill.className = 'strength-fill weak';
                strengthText.textContent = 'ضعيفة جداً';
                strengthText.className = 'strength-text weak';
            } else if (percentage <= 40) {
                strengthFill.className = 'strength-fill weak';
                strengthText.textContent = 'ضعيفة';
                strengthText.className = 'strength-text weak';
            } else if (percentage <= 60) {
                strengthFill.className = 'strength-fill medium';
                strengthText.textContent = 'متوسطة';
                strengthText.className = 'strength-text medium';
            } else if (percentage <= 80) {
                strengthFill.className = 'strength-fill strong';
                strengthText.textContent = 'قوية';
                strengthText.className = 'strength-text strong';
            } else {
                strengthFill.className = 'strength-fill very-strong';
                strengthText.textContent = 'قوية جداً';
                strengthText.className = 'strength-text very-strong';
            }
        });
    }

    if (confirmPasswordInput && passwordMatch && passwordInput) {
        confirmPasswordInput.addEventListener('input', function () {
            const password = passwordInput.value;
            const confirmPassword = this.value;

            if (confirmPassword === '') {
                passwordMatch.textContent = '';
                passwordMatch.className = 'password-match';
            } else if (password === confirmPassword) {
                passwordMatch.innerHTML = '<i class="fas fa-check-circle"></i> كلمات المرور متطابقة';
                passwordMatch.className = 'password-match match';
            } else {
                passwordMatch.innerHTML = '<i class="fas fa-times-circle"></i> كلمات المرور غير متطابقة';
                passwordMatch.className = 'password-match no-match';
            }
        });
    }
</script>
