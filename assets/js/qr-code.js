document.addEventListener('DOMContentLoaded', function () {
    const qrButton = document.getElementById('generate_qr_code');

    // Обробник для кнопки "Згенерувати QR-код"
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
                    // Перевіряємо, чи існує контейнер для QR-коду
                    const qrCodeImageContainer = document.getElementById('qr_code_image');
                    if (!qrCodeImageContainer) {
                        // Якщо контейнера немає, створюємо його
                        const newQrCodeImageContainer = document.createElement('div');
                        newQrCodeImageContainer.id = 'qr_code_image';
                        newQrCodeImageContainer.classList.add('qr-code-image-preview');
                        qrButton.parentElement.parentElement.appendChild(newQrCodeImageContainer);
                    }

                    // Додаємо QR-код до контейнера
                    document.getElementById('qr_code_image').innerHTML = '<img src="' + response.data.image_url + '" alt="QR Code" style="max-width:100%;" />';

                    // Додаємо кнопку "Видалити QR-код", якщо її немає
                    if (!document.getElementById('delete_qr_code')) {
                        const deleteButtonHtml = '<p><button type="button" id="delete_qr_code" class="button button-secondary full-width-btn" data-post-id="' + qrButton.dataset.postId + '">Видалити QR-код</button></p>';
                        jQuery('#qr_code_image').after(deleteButtonHtml);

                        // Прив'язуємо обробник події для нової кнопки
                        bindDeleteButton();
                    }
                } else {
                    alert('Помилка при генерації QR-коду: ' + response.data.message);
                }
            }).fail(function(xhr, status, error) {
                console.error('AJAX error:', xhr.responseText); // Виведення помилки в консоль
                alert('Сталася помилка при генерації QR-коду.');
            });
        });
    }

    // Функція для прив'язки обробника події до кнопки видалення
    function bindDeleteButton() {
        const deleteButton = document.getElementById('delete_qr_code');
        if (deleteButton) {
            deleteButton.addEventListener('click', function () {
                const data = {
                    action: 'delete_qr_code',
                    post_id: deleteButton.dataset.postId
                };

                // Відправка AJAX-запиту на видалення QR-коду
                jQuery.post(ajaxurl, data, function (response) {
                    if (response.success) {
                        alert('QR-код успішно видалено.');
                        document.getElementById('qr_code_image').remove(); // Видаляємо контейнер з QR-кодом
                        deleteButton.remove(); // Видаляємо кнопку видалення
                    } else {
                        alert('Помилка при видаленні QR-коду: ' + response.data.message);
                    }
                }).fail(function(xhr, status, error) {
                    console.error('AJAX error:', xhr.responseText); // Виведення помилки в консоль
                    alert('Сталася помилка при видаленні QR-коду.');
                });
            });
        } else {
            console.log('Кнопка видалення QR-коду не знайдена');
        }
    }

    // Якщо кнопка видалення вже є на сторінці при завантаженні, прив'язуємо обробник події
    bindDeleteButton();
});
