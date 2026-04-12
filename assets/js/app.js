// MedXpert - Modern JavaScript

// Initialize on DOM load
document.addEventListener('DOMContentLoaded', function() {
    initAuthTabs();
    initSidebar();
    initModals();
    initSearch();
    initFilters();
});

// Auth Page - Tab Switching
function initAuthTabs() {
    const tabs = document.querySelectorAll('.auth-tab');
    const forms = document.querySelectorAll('.auth-form');
    
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const target = this.dataset.tab;
            
            // Remove active class from all tabs and forms
            tabs.forEach(t => t.classList.remove('active'));
            forms.forEach(f => f.classList.remove('active'));
            
            // Add active class to clicked tab and corresponding form
            this.classList.add('active');
            document.getElementById(target).classList.add('active');
        });
    });
}

// Sidebar Toggle for Mobile
function initSidebar() {
    const sidebar = document.querySelector('.sidebar');
    const toggleBtn = document.getElementById('sidebarToggle');
    const overlay = document.getElementById('sidebarOverlay');
    
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            sidebar.classList.toggle('active');
            if (overlay) overlay.classList.toggle('active');
        });
    }
    
    if (overlay) {
        overlay.addEventListener('click', function() {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
        });
    }
}

// Modal Functions
function initModals() {
    const modals = document.querySelectorAll('.modal');
    const modalTriggers = document.querySelectorAll('[data-modal]');
    const modalCloses = document.querySelectorAll('.modal-close');
    
    modalTriggers.forEach(trigger => {
        trigger.addEventListener('click', function() {
            const modalId = this.dataset.modal;
            openModal(modalId);
        });
    });
    
    modalCloses.forEach(close => {
        close.addEventListener('click', function() {
            const modal = this.closest('.modal');
            closeModal(modal.id);
        });
    });
    
    // Close modal on background click
    modals.forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal(this.id);
            }
        });
    });
}

function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = 'auto';
    }
}

// Live Search
function initSearch() {
    const searchInputs = document.querySelectorAll('.search-input');
    
    searchInputs.forEach(input => {
        input.addEventListener('input', debounce(function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const searchType = e.target.dataset.search;
            
            if (searchType === 'doctors') {
                searchDoctors(searchTerm);
            } else if (searchType === 'donors') {
                searchDonors(searchTerm);
            } else if (searchType === 'medicines') {
                searchMedicines(searchTerm);
            }
        }, 300));
    });
}

function searchDoctors(term) {
    fetch(`api/get_doctors.php?search=${term}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderDoctors(data.doctors);
            }
        })
        .catch(error => console.error('Error:', error));
}

function searchDonors(term) {
    const bloodType = document.querySelector('.filter-btn.active')?.dataset.filter || 'all';
    fetch(`api/get_donors.php?search=${term}&blood_type=${bloodType}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderDonors(data.donors);
            }
        })
        .catch(error => console.error('Error:', error));
}

function searchMedicines(term) {
    const category = document.querySelector('.filter-btn.active')?.dataset.filter || 'all';
    fetch(`api/get_medicines.php?search=${term}&category=${category}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderMedicines(data.medicines);
            }
        })
        .catch(error => console.error('Error:', error));
}

// Filter Functions
function initFilters() {
    const filterBtns = document.querySelectorAll('.filter-btn');
    
    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const filterType = this.dataset.type;
            const filterValue = this.dataset.filter;
            
            // Remove active class from siblings
            this.parentElement.querySelectorAll('.filter-btn').forEach(b => {
                b.classList.remove('active');
            });
            
            // Add active class to clicked button
            this.classList.add('active');
            
            // Apply filter
            if (filterType === 'blood') {
                filterDonors(filterValue);
            } else if (filterType === 'medicine') {
                filterMedicines(filterValue);
            }
        });
    });
}

function filterDonors(bloodType) {
    const searchTerm = document.querySelector('[data-search="donors"]')?.value || '';
    fetch(`api/get_donors.php?blood_type=${bloodType}&search=${searchTerm}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderDonors(data.donors);
            }
        })
        .catch(error => console.error('Error:', error));
}

function filterMedicines(category) {
    const searchTerm = document.querySelector('[data-search="medicines"]')?.value || '';
    fetch(`api/get_medicines.php?category=${category}&search=${searchTerm}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderMedicines(data.medicines);
            }
        })
        .catch(error => console.error('Error:', error));
}

// Render Functions
function renderDoctors(doctors) {
    const container = document.getElementById('doctorsContainer');
    if (!container) return;
    
    if (doctors.length === 0) {
        container.innerHTML = '<p class="empty-state">No doctors found.</p>';
        return;
    }
    
    container.innerHTML = doctors.map(doctor => {
        const imageUrl = (doctor.image && doctor.image !== 'default-doctor.png') 
            ? `/medxpert/uploads/doctors/${doctor.image}` 
            : null;
        return `
        <div class="item-card">
            <div class="item-header">
                <div class="item-avatar" style="width: 60px; height: 60px; border-radius: 50%; overflow: hidden; background: #f0f0f0; display: flex; align-items: center; justify-content: center;">
                    ${imageUrl ? `<img src="${imageUrl}" style="width: 100%; height: 100%; object-fit: cover;">` : `<span style="font-size: 1.5rem;">${doctor.name.charAt(0)}</span>`}
                </div>
                <div class="item-info">
                    <h3>${doctor.name}</h3>
                    <p style="color: var(--primary-light); margin: 0;">${doctor.specialization}</p>
                </div>
            </div>
            <div class="item-meta">
                <div class="meta-item">
                    <i class="fas fa-graduation-cap"></i>
                    <span>${doctor.qualification}</span>
                </div>
                <div class="meta-item">
                    <i class="fas fa-hospital"></i>
                    <span>${doctor.hospital}</span>
                </div>
                <div class="meta-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>${doctor.location}</span>
                </div>
                <div class="meta-item">
                    <i class="fas fa-star"></i>
                    <span>${doctor.rating} • ${doctor.experience} years exp</span>
                </div>
                <div class="meta-item">
                    <i class="fas fa-dollar-sign"></i>
                    <span>৳${doctor.fee}</span>
                </div>
            </div>
            <div class="item-actions">
                <button class="btn btn-primary w-100" onclick="bookAppointment(${doctor.id})">
                    <i class="fas fa-calendar-check"></i> Book Appointment
                </button>
            </div>
        </div>
    `;
    }).join('');
}

function renderDonors(donors) {
    const container = document.getElementById('donorsContainer');
    if (!container) return;
    
    if (donors.length === 0) {
        container.innerHTML = '<p class="empty-state">No donors found.</p>';
        return;
    }
    
    container.innerHTML = donors.map(donor => {
        const imageUrl = (donor.image && donor.image !== 'default-donor.png') 
            ? `/medxpert/uploads/donors/${donor.image}` 
            : null;
        return `
        <div class="item-card">
            <div class="item-header">
                <div class="item-avatar" style="width: 60px; height: 60px; border-radius: 50%; overflow: hidden; background: #f0f0f0; display: flex; align-items: center; justify-content: center;">
                    ${imageUrl ? `<img src="${imageUrl}" style="width: 100%; height: 100%; object-fit: cover;">` : `<span style="font-size: 1.5rem;">${donor.name.charAt(0)}</span>`}
                </div>
                <div class="item-info">
                    <h3>${donor.name}</h3>
                    <span class="badge badge-danger" style="display: inline-block; margin-top: 0.25rem;">
                        ${donor.blood_type}
                    </span>
                </div>
            </div>
            <div class="item-meta">
                <div class="meta-item">
                    <i class="fas fa-birthday-cake"></i>
                    <span>${donor.age} years old</span>
                </div>
                <div class="meta-item">
                    <i class="fas fa-phone"></i>
                    <span>${donor.phone}</span>
                </div>
                <div class="meta-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>${donor.location}</span>
                </div>
                ${donor.last_donation ? `
                <div class="meta-item">
                    <i class="fas fa-calendar"></i>
                    <span>Last donated: ${donor.last_donation}</span>
                </div>
                ` : ''}
            </div>
            <div class="item-actions">
                <button class="btn btn-primary w-100" onclick="contactDonor('${donor.phone}', '${donor.name}')">
                    <i class="fas fa-phone"></i> Contact Donor
                </button>
            </div>
        </div>
    `;
    }).join('');
}

function renderMedicines(medicines) {
    const container = document.getElementById('medicinesContainer');
    if (!container) return;
    
    if (medicines.length === 0) {
        container.innerHTML = '<p class="empty-state">No medicines found.</p>';
        return;
    }
    
    container.innerHTML = medicines.map(med => {
        const imageUrl = (med.image && med.image !== 'default-medicine.png') 
            ? `/medxpert/uploads/medicines/${med.image}` 
            : null;
        return `
        <div class="item-card">
            ${imageUrl ? `<div style="width: 100%; height: 150px; border-radius: 4px; overflow: hidden; margin-bottom: 1rem; background: #f0f0f0;"><img src="${imageUrl}" style="width: 100%; height: 100%; object-fit: cover;"></div>` : ''}
            <div class="item-info">
                <h3>${med.name}</h3>
                <p style="color: var(--primary-light); margin: 0.25rem 0;">${med.category}</p>
                ${med.prescription_required ? '<span class="badge badge-warning">Prescription Required</span>' : ''}
            </div>
            <div class="item-meta">
                <div class="meta-item">
                    <i class="fas fa-pills"></i>
                    <span>${med.dosage}</span>
                </div>
                <div class="meta-item">
                    <i class="fas fa-industry"></i>
                    <span>${med.manufacturer}</span>
                </div>
                <div class="meta-item">
                    <i class="fas fa-dollar-sign"></i>
                    <span style="font-weight: 700; font-size: 1.25rem;">৳${med.price}</span>
                </div>
                ${med.stock > 0 ? 
                    `<span class="badge badge-success">In Stock (${med.stock})</span>` :
                    '<span class="badge badge-danger">Out of Stock</span>'
                }
            </div>
            <div class="item-actions">
                <button class="btn btn-primary w-100" onclick="orderMedicine(${med.id}, '${med.name}', ${med.price})" ${med.stock === 0 ? 'disabled' : ''}>
                    <i class="fas fa-shopping-cart"></i> Order Now
                </button>
            </div>
        </div>
    `;
    }).join('');
}

// Action Functions
function bookAppointment(doctorId) {
    // Set doctor ID in hidden field
    document.getElementById('appointmentDoctorId').value = doctorId;
    openModal('appointmentModal');
}

function submitAppointment() {
    const form = document.getElementById('appointmentForm');
    const formData = new FormData(form);
    
    // Ensure doctor_id is properly set
    const doctorId = document.getElementById('appointmentDoctorId').value;
    if (doctorId) {
        formData.set('doctor_id', doctorId);
    }
    
    fetch('../api/book_appointment.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', 'Appointment booked successfully!');
            closeModal('appointmentModal');
            form.reset();
        } else {
            showAlert('error', data.message || 'Failed to book appointment');
        }
    })
    .catch(error => {
        showAlert('error', 'An error occurred. Please try again.');
        console.error('Error:', error);
    });
}

function contactDonor(phone, name) {
    if (confirm(`Contact ${name} at ${phone}?`)) {
        window.location.href = `tel:${phone}`;
    }
}

function orderMedicine(medicineId, medicineName, price) {
    document.getElementById('orderMedicineId').value = medicineId;
    document.getElementById('orderMedicineName').textContent = medicineName;
    document.getElementById('orderMedicinePrice').value = price;
    document.getElementById('orderQuantity').value = 1;
    updateOrderTotal();
    openModal('orderModal');
}

function updateOrderTotal() {
    const quantity = parseInt(document.getElementById('orderQuantity').value) || 1;
    const price = parseFloat(document.getElementById('orderMedicinePrice').value);
    const total = quantity * price;
    document.getElementById('orderTotal').textContent = total.toFixed(2);
}

function submitOrder() {
    const form = document.getElementById('orderForm');
    const formData = new FormData(form);
    
    // Ensure price is properly set
    const price = document.getElementById('orderMedicinePrice').value;
    if (price) {
        formData.set('price', price);
    }
    
    fetch('../api/order_medicine.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', 'Order placed successfully! Order #' + data.order_number);
            closeModal('orderModal');
            form.reset();
        } else {
            showAlert('error', data.message || 'Failed to place order');
        }
    })
    .catch(error => {
        showAlert('error', 'An error occurred. Please try again.');
        console.error('Error:', error);
    });
}

function requestAmbulance(type) {
    if (type === 'emergency') {
        if (confirm('This will immediately dispatch an ambulance. Continue?')) {
            window.location.href = 'tel:999';
        }
    } else {
        openModal('ambulanceModal');
    }
}

function submitAmbulanceRequest() {
    const form = document.getElementById('ambulanceForm');
    const formData = new FormData(form);
    
    // Set request_type to 'scheduled' for form submissions
    formData.set('request_type', 'scheduled');
    
    fetch('api/request_ambulance.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', 'Ambulance request submitted successfully!');
            closeModal('ambulanceModal');
            form.reset();
        } else {
            showAlert('error', data.message || 'Failed to submit request');
        }
    })
    .catch(error => {
        showAlert('error', 'An error occurred. Please try again.');
        console.error('Error:', error);
    });
}

// Utility Functions
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function showAlert(type, message) {
    const alertContainer = document.getElementById('alertContainer');
    if (!alertContainer) {
        const container = document.createElement('div');
        container.id = 'alertContainer';
        container.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; max-width: 400px;';
        document.body.appendChild(container);
    }
    
    const alert = document.createElement('div');
    alert.className = `alert alert-${type === 'success' ? 'success' : 'error'}`;
    alert.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
        <span>${message}</span>
    `;
    
    document.getElementById('alertContainer').appendChild(alert);
    
    setTimeout(() => {
        alert.style.opacity = '0';
        alert.style.transform = 'translateX(400px)';
        setTimeout(() => alert.remove(), 300);
    }, 5000);
}

// Form Validation
function validateForm(formId) {
    const form = document.getElementById(formId);
    const inputs = form.querySelectorAll('[required]');
    let isValid = true;
    
    inputs.forEach(input => {
        if (!input.value.trim()) {
            input.style.borderColor = 'var(--danger)';
            isValid = false;
        } else {
            input.style.borderColor = '';
        }
    });
    
    return isValid;
}
