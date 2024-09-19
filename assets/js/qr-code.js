document.addEventListener('DOMContentLoaded', function () {
    const qrButton = document.getElementById('generate_qr_code');
    const uploadButton = document.getElementById('upload_qr_code');
    const removeButton = document.getElementById('remove_qr_code');

    // Генерація QR-коду
    if (qrButton) {
        qrButton.addEventListener('click', function () {
            const url = document.getElementById('worker_qr_url').value;
            if (!url) {
                alert('Будь ласка, введіть URL-адресу.');
                return;
            }

            // Створення об'єкта для запиту AJAX
            const data = {
                action: 'generate_qr_code',
                qr_url: url,
                post_id: qrButton.dataset.postId
            };

            // Відправка AJAX-запиту
            jQuery.post(ajaxurl, data, function (response) {
                if (response.success) {
                    const qrCodeImage = document.getElementById('qr_code_image');
                    qrCodeImage.innerHTML = '<img src="' + response.data.image_url + '" alt="QR Code" style="max-width: 100%;">';
                } else {
                    alert('Помилка при генерації QR-коду: ' + response.data.message);
                }
            });
        });
    }

    // Видалення QR-коду
    if (removeButton) {
        removeButton.addEventListener('click', function () {
            const postId = qrButton.dataset.postId;

            // AJAX запит для видалення QR-коду
            const data = {
                action: 'remove_qr_code',
                post_id: postId
            };

            jQuery.post(ajaxurl, data, function (response) {
                if (response.success) {
                    document.getElementById('qr_code_image').innerHTML = '';
                    removeButton.style.display = 'none';
                } else {
                    alert('Помилка при видаленні QR-коду: ' + response.data.message);
                }
            });
        });
    }

    // Завантаження QR-коду
    if (uploadButton) {
        uploadButton.addEventListener('click', function () {
            const customUploader = wp.media({
                title: 'Завантажте QR-код',
                button: {
                    text: 'Вибрати QR-код'
                },
                multiple: false
            })
            .on('select', function () {
                const attachment = customUploader.state().get('selection').first().toJSON();
                const qrCodeImage = document.getElementById('qr_code_image');
                qrCodeImage.innerHTML = '<img src="' + attachment.url + '" alt="QR Code" style="max-width: 100%;">';

                // Збереження ID зображення через AJAX
                const data = {
                    action: 'save_qr_code_image',
                    post_id: qrButton.dataset.postId,
                    attachment_id: attachment.id
                };

                jQuery.post(ajaxurl, data, function (response) {
                    if (!response.success) {
                        alert('Помилка при збереженні QR-коду: ' + response.data.message);
                    }
                });
            })
            .open();
        });
    }
});
