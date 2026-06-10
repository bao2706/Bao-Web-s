const typeSelect = document.getElementById("type");
const categorySelect = document.getElementById("category");

function updateCategory() {

    if (typeSelect.value === "income") {
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

typeSelect.addEventListener("change", updateCategory);