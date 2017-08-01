$(document).ready(function () {
    $("#new_task").on("pjax:end", function() {
        $.pjax.reload({container:"#tasks"});
    });

    $("#tasks").on("pjax:end", function() {
        setTimeout(function () {
            $.pjax.reload({container:"#tasks"});
        }, 5000);
    });

    // $.pjax.reload({container:"#tasks"});
});
