<?php
require_once '../config/header.php';
?>

<h1>Online Pharmacy</h1>
<div class="breadcrumb">
    <span><i class="fas fa-home"></i> Home</span>
    <span>/</span>
    <span>Online Pharmacy</span>
</div>
</div>
</div>

<div class="content-wrapper">
    <!-- Search Box -->
    <div class="search-box">
        <i class="fas fa-search search-icon"></i>
        <input type="text" class="search-input" data-search="medicines" placeholder="Search medicines by name or category...">
    </div>
    
    <!-- Category Filters -->
    <div class="filter-buttons">
        <button class="filter-btn active" data-type="medicine" data-filter="all">All Categories</button>
        <button class="filter-btn" data-type="medicine" data-filter="Pain Relief">Pain Relief</button>
        <button class="filter-btn" data-type="medicine" data-filter="Antibiotic">Antibiotic</button>
        <button class="filter-btn" data-type="medicine" data-filter="Gastric">Gastric</button>
        <button class="filter-btn" data-type="medicine" data-filter="Allergy">Allergy</button>
        <button class="filter-btn" data-type="medicine" data-filter="Diabetes">Diabetes</button>
        <button class="filter-btn" data-type="medicine" data-filter="Blood Pressure">Blood Pressure</button>
        <button class="filter-btn" data-type="medicine" data-filter="Vitamin">Vitamin</button>
    </div>
    
    <!-- Medicines Grid -->
    <div class="items-grid" id="medicinesContainer">
        <div class="spinner"></div>
    </div>
</div>

<!-- Order Modal -->
<div class="modal" id="orderModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Order Medicine</h3>
            <button class="modal-close" onclick="closeModal('orderModal')">&times;</button>
        </div>
        <form id="orderForm" onsubmit="event.preventDefault(); submitOrder();">
            <div class="modal-body">
                <input type="hidden" id="orderMedicineId" name="medicine_id">
                <input type="hidden" id="orderMedicinePrice" name="price">
                
                <div class="alert alert-info">
                    <i class="fas fa-pills"></i>
                    <strong id="orderMedicineName"></strong>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Quantity</label>
                    <input type="number" class="form-input" id="orderQuantity" name="quantity" min="1" value="1" required onchange="updateOrderTotal()">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Delivery Address</label>
                    <textarea class="form-input" name="delivery_address" rows="3" placeholder="Enter your complete delivery address" required></textarea>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Phone Number</label>
                    <input type="tel" class="form-input" name="phone" placeholder="Enter your phone number" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Payment Method</label>
                    <select class="form-input" name="payment_method" required>
                        <option value="cash">Cash on Delivery</option>
                        <option value="card">Credit/Debit Card</option>
                        <option value="mobile_banking">Mobile Banking (bKash/Nagad)</option>
                    </select>
                </div>
                
                <div style="background: var(--dark-lighter); padding: 1rem; border-radius: var(--radius); margin-top: 1rem;">
                    <div style="display: flex; justify-content: space-between; font-size: 1.25rem;">
                        <strong>Total Amount:</strong>
                        <strong style="color: var(--success);">৳<span id="orderTotal">0.00</span></strong>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('orderModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Place Order</button>
            </div>
        </form>
    </div>
</div>

<script>
// Load medicines on page load
document.addEventListener('DOMContentLoaded', function() {
    loadMedicines();
});

function loadMedicines() {
    fetch('../api/get_medicines.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderMedicines(data.medicines);
            }
        })
        .catch(error => console.error('Error:', error));
}
</script>

<?php require_once '../config/footer.php'; ?>