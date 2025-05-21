<style>
    .navbar {
        background-color: #000;
        color: white;
        height: 56px;
        display: flex;
        align-items: center;
        padding: 0 16px;
        position: sticky;
        top: 0;
        z-index: 1000;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    .navbar-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
        max-width: 1400px;
        margin: 0 auto;
    }
    
    .navbar-logo {
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 600;
        font-size: 18px;
    }
    
    .navbar-logo-icon {
        background-color: white;
        color: black;
        width: 28px;
        height: 28px;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .navbar-nav {
        display: flex;
        align-items: center;
        gap: 16px;
    }
    
    .navbar-nav-item {
        color: white;
        opacity: 0.8;
        transition: opacity 0.2s;
    }
    
    .navbar-nav-item:hover {
        opacity: 1;
    }
    
    .navbar-nav-item.active {
        opacity: 1;
    }
    
    .navbar-actions {
        display: flex;
        align-items: center;
        gap: 16px;
    }
    
    .notification-dropdown {
        position: absolute;
        top: 100%;
        right: 0;
        width: 320px;
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        margin-top: 8px;
        color: #333;
        z-index: 1000;
        max-height: 400px;
        overflow-y: auto;
        display: none;
    }
    
    .notification-dropdown.show {
        display: block;
    }
    
    .notification-header {
        padding: 12px 16px;
        border-bottom: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .notification-title {
        font-weight: 600;
        font-size: 14px;
    }
    
    .notification-action {
        color: #6366f1;
        font-size: 12px;
        cursor: pointer;
    }
    
    .notification-list {
        max-height: 320px;
        overflow-y: auto;
    }
    
    .notification-item {
        padding: 12px 16px;
        border-bottom: 1px solid #eee;
        transition: background-color 0.2s;
    }
    
    .notification-item:hover {
        background-color: #f9fafb;
    }
    
    .notification-item.unread {
        background-color: #f0f7ff;
    }
    
    .notification-content {
        font-size: 13px;
        margin-bottom: 4px;
    }
    
    .notification-time {
        font-size: 11px;
        color: #6b7280;
    }
    
    .notification-footer {
        padding: 8px 16px;
        text-align: center;
        border-top: 1px solid #eee;
    }
    
    .notification-footer a {
        color: #6366f1;
        font-size: 13px;
        font-weight: 500;
    }
    
    .notification-badge {
        position: absolute;
        top: -2px;
        right: -2px;
        width: 8px;
        height: 8px;
        background-color: #ef4444;
        border-radius: 50%;
    }
    
    .dropdown-container {
        position: relative;
    }
    
    .user-dropdown {
        position: absolute;
        top: 100%;
        right: 0;
        width: 200px;
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        margin-top: 8px;
        color: #333;
        z-index: 1000;
        display: none;
    }
    
    .user-dropdown.show {
        display: block;
    }
    
    .user-dropdown-header {
        padding: 12px 16px;
        border-bottom: 1px solid #eee;
    }
    
    .user-dropdown-name {
        font-weight: 600;
        font-size: 14px;
    }
    
    .user-dropdown-email {
        font-size: 12px;
        color: #6b7280;
        margin-top: 2px;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .user-dropdown-item {
        padding: 8px 16px;
        font-size: 13px;
        transition: background-color 0.2s;
        display: block;
        width: 100%;
        text-align: left;
    }
    
    .user-dropdown-item:hover {
        background-color: #f9fafb;
    }
    
    .user-dropdown-item.danger {
        color: #ef4444;
    }
</style>