window.onload = function() {
    if (window.userRole === 'admin') {
        window.location.href = window.adminDashboardUrl;
    } else if (window.userRole === 'subadmin') {
        window.location.href = window.subadminDashboardUrl;
    }
};
