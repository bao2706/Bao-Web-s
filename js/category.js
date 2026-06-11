const typeSelect = document.getElementById('type');
const categorySelect = document.getElementById('category');
const filterSelect = document.getElementById('filter');

function updateCategory() {
    if (!typeSelect || !categorySelect) {
        return;
    }

    if (typeSelect.value === 'income') {
        categorySelect.innerHTML = `
            <option>Salary 💰</option>
            <option>Bonus 🎁</option>
            <option>Investment 📈</option>
        `;
    } else {
        categorySelect.innerHTML = `
            <option>Food 🍔</option>
            <option>Transportation 🚗</option>
            <option>Shopping 🛍️</option>
            <option>Entertainment 🎬</option>
        `;
    }
}

updateCategory();

if (typeSelect && categorySelect) {
    const currentType = typeSelect.dataset.currentType;
    const currentCategory = categorySelect.dataset.currentCategory;

    if (currentType) {
        typeSelect.value = currentType;
        updateCategory();

        if (currentCategory) {
            categorySelect.value = currentCategory;
        }
    }
}

if (typeSelect) {
    typeSelect.addEventListener('change', updateCategory);
}

if (filterSelect) {
    filterSelect.addEventListener('change', function () {
        const period = this.value;

        fetch('get_transactions.php?period=' + period)
            .then(response => response.text())
            .then(data => {
                const transactions = document.getElementById('transactions');

                if (transactions) {
                    transactions.innerHTML = data;
                }
            });
    });
}
