const typeSelect = document.getElementById('type');
const categorySelect = document.getElementById('category');

const categoryOptions = {
    income: ['Salary', 'Bonus', 'Investment'],
    expense: ['Food', 'Transportation', 'Shopping', 'Entertainment'],
};

function updateCategory(selectedCategory = '') {
    if (!typeSelect || !categorySelect) {
        return;
    }

    const categories = categoryOptions[typeSelect.value] || categoryOptions.expense;

    categorySelect.innerHTML = categories
        .map((category) => `<option value="${category}">${category}</option>`)
        .join('');

    if (selectedCategory && categories.includes(selectedCategory)) {
        categorySelect.value = selectedCategory;
    }
}

if (typeSelect && categorySelect) {
    const currentType = typeSelect.dataset.currentType;
    const currentCategory = categorySelect.dataset.currentCategory;

    if (currentType && categoryOptions[currentType]) {
        typeSelect.value = currentType;
    }

    updateCategory(currentCategory);
    typeSelect.addEventListener('change', () => updateCategory());
}
