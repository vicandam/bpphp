document.addEventListener("DOMContentLoaded", function () {
    const fields = document.querySelectorAll('.input-group .form-control');

    fields.forEach(field => {
        const parent = field.closest('.input-group');

        if (field.value.trim() !== '') {
            parent.classList.add('is-filled');
        }

        field.addEventListener('input', function () {
            if (field.value.trim() !== '') {
                parent.classList.add('is-filled');
            } else {
                parent.classList.remove('is-filled');
            }
        });

        field.addEventListener('blur', function () {
            if (field.value.trim() === '') {
                parent.classList.remove('is-filled');
            }
        });
    });
});
