(function($) {
    "use strict"

    new dezSettings({
        typography: "roboto",
        version: "light",
        layout: "Vertical",
        headerBg: "color_2",
        navheaderBg: "color_2",
        sidebarBg: "color_2",
        sidebarStyle: "full",
        sidebarPosition: "fixed",
        headerPosition: "fixed",
        containerLayout: "full",
        direction: "ltr"
    }); 
	
})(jQuery);


// Dynamic Notifications
$(document).ready(function() {
    if($('.notification_dropdown .list-unstyled').length > 0) {
        fetch('../backend/dashboard_api.php')
        .then(response => response.json())
        .then(data => {
            if(data && data.recentRegistrations) {
                let html = '';
                data.recentRegistrations.forEach(reg => {
                    html += `
                        <li class="media dropdown-item">
                            <span class="success" style="background: #99C83D; color: white; display: flex; align-items: center; justify-content: center; border-radius: 50%; width: 35px; height: 35px; margin-right: 15px;"><i class="la la-user"></i></span>
                            <div class="media-body">
                                <a href="all-students.html" style="text-decoration: none;">
                                    <p style="margin-bottom: 0;"><strong>${reg.name}</strong> registered for <strong>${reg.class_name || 'a class'}</strong></p>
                                </a>
                            </div>
                        </li>
                    `;
                });
                if(html === '') {
                    html = '<li class="media dropdown-item"><div class="media-body"><p>No recent registrations</p></div></li>';
                }
                $('.notification_dropdown .list-unstyled').html(html);
            }
        })
        .catch(err => console.error("Error loading notifications:", err));
    }
});
