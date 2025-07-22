document.addEventListener("DOMContentLoaded", function () {
    const textareas = document.querySelectorAll('.input-group textarea.form-control');

    textareas.forEach(textarea => {
        const parent = textarea.closest('.input-group');

        if (textarea.value.trim() !== '') {
            parent.classList.add('is-filled');
        }

        textarea.addEventListener('input', function () {
            if (textarea.value.trim() !== '') {
                parent.classList.add('is-filled');
            } else {
                parent.classList.remove('is-filled');
            }
        });

        textarea.addEventListener('blur', function () {
            if (textarea.value.trim() === '') {
                parent.classList.remove('is-filled');
            }
        });
    });
});
