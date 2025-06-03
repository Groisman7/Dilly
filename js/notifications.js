document.addEventListener('DOMContentLoaded', () => {
    const notificationForm = document.getElementById('notificationForm');
    
    // Load saved preferences
    const savedPreferences = JSON.parse(localStorage.getItem('notificationPreferences') || '{}');
    
    // Apply saved preferences to form
    if (savedPreferences) {
        Object.keys(savedPreferences).forEach(key => {
            const element = notificationForm.elements[key];
            if (element) {
                if (element.type === 'checkbox') {
                    element.checked = savedPreferences[key];
                } else {
                    element.value = savedPreferences[key];
                }
            }
        });
    }

    // Handle form submission
    notificationForm.addEventListener('submit', (e) => {
        e.preventDefault();
        
        // Collect form data
        const formData = {
            voucherExpiry: notificationForm.voucherExpiry.checked,
            newOffers: notificationForm.newOffers.checked,
            marketplaceUpdates: notificationForm.marketplaceUpdates.checked,
            // emailFrequency: notificationForm.emailFrequency.value
        };
        
        // Save to localStorage
        localStorage.setItem('notificationPreferences', JSON.stringify(formData));
        
        // Show success message
        alert('Notification preferences saved successfully!');
    });
});
