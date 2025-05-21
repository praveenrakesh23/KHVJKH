// Add this pagination and lazy loading for menu items
function fetchMenuItems(page = 1, limit = 12) {
    const start = (page - 1) * limit;
    
    // Show loading state
    menuGrid.innerHTML += '<div class="loading-spinner"></div>';
    
    fetch(`fetch_menu_items.php?page=${page}&limit=${limit}`)
        .then(response => response.json())
        .then(data => {
            // Remove loading spinner
            document.querySelector('.loading-spinner')?.remove();
            
            // Append new items
            data.items.forEach(item => {
                const menuItem = createMenuItem(item);
                menuGrid.appendChild(menuItem);
            });
            
            // Setup intersection observer for infinite scroll
            if (data.hasMore) {
                observeLastItem();
            }
        });
}

// Add intersection observer for lazy loading
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const img = entry.target;
            img.src = img.dataset.src;
            observer.unobserve(img);
        }
    });
}); 