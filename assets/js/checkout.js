let selectedPaymentMethod = null;
let appliedPromoCode = null;
let discountAmount = 0;
let subtotal = 0;

// Get subtotal from the page
document.addEventListener('DOMContentLoaded', function() {
    const totalAmountElement = document.getElementById('total-amount');
    if (totalAmountElement) {
        const totalText = totalAmountElement.textContent;
        subtotal = parseFloat(totalText.replace('$', '').replace(',', ''));
    }
});

function applyPromoCode() {
    const promoCode = document.getElementById('promo-code').value;
    const promoMessage = document.getElementById('promo-message');
    
    if (promoCode === 'WELCOME10') {
        discountAmount = subtotal * 0.1; // 10% discount
        document.getElementById('discount-amount').textContent = '-$' + discountAmount.toFixed(2);
        document.getElementById('total-amount').textContent = 
            '$' + (subtotal - discountAmount).toFixed(2);
        promoMessage.textContent = 'Promo code applied successfully!';
        promoMessage.style.color = '#2ecc71';
        appliedPromoCode = promoCode;
        document.getElementById('promo-code-input').value = promoCode;
    } else {
        promoMessage.textContent = 'Invalid promo code';
        promoMessage.style.color = '#e74c3c';
        // Reset discount if invalid code
        discountAmount = 0;
        document.getElementById('discount-amount').textContent = '$0.00';
        document.getElementById('total-amount').textContent = '$' + subtotal.toFixed(2);
        document.getElementById('promo-code-input').value = '';
    }
}

function selectPaymentMethod(element) {
    // Remove selected class from all payment methods
    document.querySelectorAll('.payment-method').forEach(method => {
        method.classList.remove('selected');
    });
    
    // Add selected class to clicked payment method
    element.classList.add('selected');
    selectedPaymentMethod = element.querySelector('h4').textContent;
    document.getElementById('payment-method').value = selectedPaymentMethod;
}

document.getElementById('checkout-form').addEventListener('submit', function(e) {
    e.preventDefault();
    if (!selectedPaymentMethod) {
        alert('Please select a payment method');
        return;
    }
    this.submit();
});

// Add this new function for the dropdown
function toggleDropdown() {
    const dropdown = document.getElementById('userDropdown');
    dropdown.classList.toggle('show');
}

// Close dropdown when clicking outside
window.onclick = function(event) {
    if (!event.target.matches('.user-icon') && !event.target.matches('.user-icon *')) {
        const dropdown = document.getElementById('userDropdown');
        if (dropdown.classList.contains('show')) {
            dropdown.classList.remove('show');
        }
    }
}