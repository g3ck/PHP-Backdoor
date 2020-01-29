<script>
// Submit a command by jjax
function ajax(command){
    $.ajax({
        type: "POST",
        dataType:"json",
        // Location = file itself
        url: window.location.href,
        // Set command as post data
        data: "command=" + command,
        beforeSend: function (data) {
            // Waiting for the answer - disable input
            $("#input").addClass('waiting');
            $("#command").prop('disabled', true);
        },
        // Success
        success: function (data)
        {
            // Log answer in console
            // Get answer array and log each line
            $.each(data, function(i, item) {
                answer(item) + ".";
            });
            // Enable input
            setTimeout(function(){
                $("#input").removeClass('waiting');
                $("#command").prop('disabled', false);
            },250);

        },
        // an error occures
        error: function (data)
        {
            answer('unknow command', 'error') + ".";
            setTimeout(function(){
                $("#input").removeClass('waiting');
                $("#command").prop('disabled', false);
            },250);

        }
    });
}
// Get welcome message
welcome();
// Submit command
$("#command").on('keyup', function (e) {
    var value = $("#command").val();
    // On enter click
    if (e.keyCode == 13) {
        if (value != ''){
            // Log user command into console
            log(value);
            // Custom command
            // Clear terminal screan
            if (value == 'cls'){
                $("#console").html('');
            // Reset terminal
            } else if (value == 'reset'){
                $("#console").html('');
                welcome();
            } else{
                ajax(value);
            }
            // Scroll terminal to bottom
            $("html, body").animate({ scrollTop: $(document).height() }, "slow");
            $("#command").val('');
        }
        return false;
    }
});
$("#command").val('');
// Copy clicked command
$(document).on('click','#console li:not(.answer)',function(e) {
    $("#command").val($(this).html());
});
// Focus command input
$(window).click(function() {
    $("#command").focus();
});
// Focus command every 0.5 minute
$(document).ready(function() {
    setTimeout(function(){
        $("#command").focus();
    }, 500);
});
