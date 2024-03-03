$(function () {
    function productChoices(rowId, hasPrice = true) {
        $(rowId + ' .name').on('focus', function () {
            $(rowId + ' .choices').show();
        });

        $(rowId + ' .name').on('blur', function () {
            setTimeout(function () {
                $(rowId + ' .choices').hide();
            }, 100);
        });

        // on load and resize set size of choices same as input
        // on resize
        function setChoicesSize() {
            $(rowId + ' .choices').css('width', $(rowId + ' .name').outerWidth());
            $(rowId + ' .choices').css('left', $(rowId + ' .name').position().left);
            $(rowId + ' .choices').css('top', $(rowId + ' .name').position().top + $(rowId + ' .name').outerHeight());
        };
        setChoicesSize();
        $(window).on('resize', setChoicesSize);
        // on click of choice
        $(rowId + ' .choice').on('click', function () {
            $(rowId + ' .name').val($(this).data('name'));
            if (hasPrice)
                $(rowId + ' .price').val($(this).data('price'));
            //if $(this).data('id') exists then add value to hidden input rowId + ' .product-id'
            console.log($(this))
            if ($(this).data('id'))
                $(rowId + ' .product-id').val($(this).data('id'));
            $(rowId + ' .choices').hide();
        });
    }
    function main() {
        productChoices('#product-row-1');
        //check if #product-row-2 exists
        if ($('#product-row-2').length)
            productChoices('#product-row-2', false);

        //on click on .minus-btn check if new value is 1 then add <i class="fa-solid fa-trash-can"></i> to .minus-btn
        $('.minus-btn').on('click', function () {
            var input = $(this).parent().find('input');
            var value = parseInt(input.val());
            if (value - 1 === 1) {
                $(this).html('<i class="fa-solid fa-trash-can"></i>');
                //add class .btn-danger to .minus-btn and remove class .btn-primary
                $(this).addClass('btn-danger');
                $(this).removeClass('btn-primary');
                //add alert to confirm delete
            }
        });

        // on click of .plus-btn remove <i class="fa-solid fa-trash-can"></i> from .minus-btn
        $('.plus-btn').on('click', function () {
            $(this).parent().find('.minus-btn').html('-');
            //remove class .btn-danger
            $(this).parent().find('.minus-btn').removeClass('btn-danger');
            //add class .btn-primary
            $(this).parent().find('.minus-btn').addClass('btn-primary');
            //remove alert from .minus-btn
            $(this).parent().find('.minus-btn').off('click');
        });
    }

    main(); // Call main initially

    document.addEventListener('htmx:afterSwap', function (event) {
        main(); // Call main again after htmx swap
        console.log('htmx:afterSwap', event);
        // if target has class .stock then get id and remove "stock-" from id and find #stock-row-<id> and remove it
        if (event.target.classList.contains('stock')) {
            //check if target>input exists
            if (!event.target.querySelector('input')) {
                var id = event.target.id.replace('stock-', '');
                $('#stock-row-' + id).remove();
            }
        }
        // event.detail.pathInfo.requestPath contains "htmx_modal_new_sell"
        if (event.detail.pathInfo.requestPath.includes("htmx_modal_new_sell")) {
            //get id from event.detail.pathInfo.requestPath like "htmx/htmx_modal_new_sell/1"
            //call with jquery ajax "htmx/htmx_reload_products/{id}" and target "#products"
            console.log(event.detail.pathInfo.requestPath.split("/").pop());
            var id = event.detail.pathInfo.requestPath.split("/").pop();
            $.ajax({
                url: "htmx/htmx_reload_products/" + id,
                success: function (result) {
                    $('#products').html(result);
                }
            });
            // htmx/htmx_reload_sells/{id}
            $.ajax({
                url: "htmx/htmx_reload_sells/" + id,
                success: function (result) {
                    $('#sells').html(result);
                }
            });
        }
    });
});
