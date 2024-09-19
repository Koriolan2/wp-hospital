jQuery(document).ready(function($) {
    const tableBody = $('#qualification_courses tbody');
    const addRowButtonOutside = $('#add_row_outside'); // Кнопка після таблиці

    // Функція для створення нового рядка
    function createNewRow(rowCount) {
        return `
            <tr>
                <td><input type="text" name="worker_courses[${rowCount}][institution]" style="width: 100%;" /></td>
                <td><input type="text" name="worker_courses[${rowCount}][course]" style="width: 100%;" /></td>
                <td>
                    <span class="remove-row dashicons dashicons-remove"></span>
                    <span class="add-row dashicons dashicons-plus-alt"></span>
                </td>
            </tr>
        `;
    }

    // Делегування подій для кнопок "Додати" та "Видалити" всередині таблиці
    tableBody.on('click', '.add-row', function() {
        const currentRow = $(this).closest('tr');
        const rowCount = tableBody.find('tr').length;
        const newRow = createNewRow(rowCount);

        // Вставляємо новий рядок безпосередньо після поточного
        currentRow.after(newRow);
    });

    // Подія для кнопки "Видалити" всередині таблиці
    tableBody.on('click', '.remove-row', function() {
        const row = $(this).closest('tr');
  
            row.remove(); 
      
    });

    // Подія для кнопки "Додати новий рядок" після таблиці
    if (addRowButtonOutside.length) {
        addRowButtonOutside.on('click', function() {
            const rowCount = tableBody.find('tr').length;
            const newRow = createNewRow(rowCount);

            // Додаємо новий рядок у кінець таблиці
            tableBody.append(newRow);
        });
    }
});
