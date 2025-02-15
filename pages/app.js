// JavaScript to handle AJAX for removing items from the wishlist
document.addEventListener('DOMContentLoaded', function () {
    const removeButtons = document.querySelectorAll('.remove-btn');

    removeButtons.forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault(); // Prevent form submission

            const productId = this.closest('form').querySelector('input[name="product_id"]').value;
            
            // Send AJAX request to remove the product from wishlist
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'wishlist.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function () {
                if (xhr.status === 200) {
                    // Update the wishlist by removing the product from the page
                    button.closest('.product-card').remove();
                } else {
                    alert('Error removing item from wishlist!');
                }
            };
            xhr.send('remove_from_wishlist=true&product_id=' + productId);
        });
    });
});
