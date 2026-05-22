<script>
    document.querySelectorAll('.password-toggle').forEach(btn => {
        btn.addEventListener('click', function () {
            const target = this.dataset.target;
            const input = document.getElementById(target);
            const icon = this.querySelector('i');
            if (!input || !icon) return;

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });

    document.querySelectorAll('.form-control').forEach(input => {
        if (input.value) {
            input.parentElement?.classList.add('focused');
        }
        input.addEventListener('focus', function () {
            this.parentElement?.classList.add('focused');
        });
        input.addEventListener('blur', function () {
            if (!this.value) {
                this.parentElement?.classList.remove('focused');
            }
        });
    });

    document.querySelectorAll('.auth-form').forEach(form => {
        form.addEventListener('submit', function () {
            const btn = this.querySelector('.btn-auth');
            if (btn) btn.classList.add('loading');
        });
    });
</script>
