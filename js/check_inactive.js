$(document).ready(function() {
    setInterval(function() {
        $.ajax({
            type: 'GET',
            async: false,
            url: '/SPAS/assets/includes/checkinactive.ajax.php',
            success: function(response) {
                if (response == 'logout_redirect') {
                    location.href = "../login/login.php";
                }
            }
        });
    }, 5000);
});