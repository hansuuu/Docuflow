<script>
// Toggle notifications dropdown
function toggleNotifications() {
    const dropdown = document.getElementById('notification-dropdown');
    dropdown.classList.toggle('hidden');
}

// Close notifications dropdown when clicking outside
document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('notification-dropdown');
    const notificationButton = event.target.closest('.navbar-icon');
    
    if (!dropdown) return;
    
    if (notificationButton && notificationButton.querySelector('[data-lucide="bell"]')) {
        return;
    }
    
    if (!dropdown.contains(event.target)) {
        dropdown.classList.add('hidden');
    }
});

// Optional: Real-time notifications with Laravel Echo
// Uncomment this section if you have Laravel Echo set up
/*
document.addEventListener('DOMContentLoaded', function() {
    window.Echo.private('App.Models.User.' + {{ auth()->id() }})
        .notification((notification) => {
            // Add notification to the list
            const notificationsContainer = document.querySelector('.max-h-64.overflow-y-auto');
            const noNotificationsMessage = notificationsContainer.querySelector('p.text-sm.text-gray-500');
            
            if (noNotificationsMessage) {
                notificationsContainer.innerHTML = '';
            }
            
            const notificationElement = document.createElement('div');
            notificationElement.className = 'px-4 py-2 border-b border-gray-100 hover:bg-gray-50 bg-purple-50';
            notificationElement.innerHTML = `
                <div class="flex justify-between">
                    <p class="text-sm text-gray-700">${notification.message}</p>
                    <form action="/notifications/${notification.id}/mark-read" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-xs text-purple-600 hover:text-purple-800">
                            Mark read
                        </button>
                    </form>
                </div>
                <p class="text-xs text-gray-500">Just now</p>
            `;
            
            notificationsContainer.prepend(notificationElement);
            
            // Show notification dot
            const notificationDot = document.querySelector('.notification-dot');
            if (notificationDot) {
                notificationDot.style.display = 'block';
            } else {
                const bellIcon = document.querySelector('[data-lucide="bell"]');
                if (bellIcon) {
                    const dot = document.createElement('div');
                    dot.className = 'notification-dot';
                    bellIcon.parentElement.appendChild(dot);
                }
            }
            
            // Play notification sound
            const audio = new Audio('/sounds/notification.mp3');
            audio.play();
        });
});
*/
</script>