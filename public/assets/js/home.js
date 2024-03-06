document.addEventListener('DOMContentLoaded', function () {
    document.addEventListener('htmx:afterSwap', function (event) {
        if (event.detail.pathInfo.requestPath.includes("htmx/supplier/new")) {
            $.ajax({
                url: "htmx/suppliers-load/",
                success: function (result) {
                    $('#suppliers').html(result);
                }
            });
        }
    });
});