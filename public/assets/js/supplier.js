$(function () {
    const id = $('#supplier-slug').data('id');
    function productChoices(rowId, hasPrice = true) {
        $(rowId + ' .name').on('focus', function () {
            $(rowId + ' .choices').show();
        });
        $(rowId + ' .name').on('blur', function () {
            setTimeout(function () {
                $(rowId + ' .choices').hide();
            }, 400);
        });
        function setChoicesSize() {
            if (!$(rowId + ' .choices').length)
                return;
            $(rowId + ' .choices').css('width', $(rowId + ' .name').outerWidth());
            $(rowId + ' .choices').css('left', $(rowId + ' .name').position().left);
            $(rowId + ' .choices').css('top', $(rowId + ' .name').position().top + $(rowId + ' .name').outerHeight());
        };
        setChoicesSize();
        $(window).on('resize', setChoicesSize);
        $(rowId + ' .choice').on('click', function () {
            $(rowId + ' .name').val($(this).data('name'));
            if (hasPrice)
                $(rowId + ' .price').val($(this).data('price'));
            if ($(this).data('id'))
                $(rowId + ' .product-id').val($(this).data('id'));
            $(rowId + ' .choices').hide();
        });
    }
    function main() {
        productChoices('#product-row-1');
        if ($('#product-row-2').length)
            productChoices('#product-row-2', false);
        $('.minus-btn').on('click', function () {
            var input = $(this).parent().find('input');
            var value = parseInt(input.val());
            if (value - 1 === 1) {
                $(this).html('<i class="fa-solid fa-trash-can"></i>');
                $(this).addClass('btn-danger');
                $(this).removeClass('btn-primary');
            }
        });

        $('.plus-btn').on('click', function () {
            $(this).parent().find('.minus-btn').html('-');
            $(this).parent().find('.minus-btn').removeClass('btn-danger');
            $(this).parent().find('.minus-btn').addClass('btn-primary');
            $(this).parent().find('.minus-btn').off('click');
        });
    }
    main();
    document.addEventListener('htmx:afterSwap', function (event) {
        main();
        let id = $('#supplier-slug').data('id');
        if (id) {
            $.ajax({
                url: "htmx/supplier/" + id + "/load-details",
                success: function (result) {
                    $('#supplier-info').html(result);
                }
            });
        }
        if (event.target.classList.contains('stock')) {
            if (!event.target.querySelector('input')) {
                let id = event.target.id.replace('stock-', '');
                $('#stock-row-' + id).remove();
            }
        }
        if (event.detail.pathInfo.requestPath.match(/^\/htmx\/\d+\/sell\/new$/g)) {
            let id = $('#supplier-slug').data('id');
            $.ajax({
                url: "htmx/" + id + "/product/load",
                success: function (result) {
                    $('#products').html(result);
                }
            });
            $.ajax({
                url: "htmx/" + id + "/sell/load",
                success: function (result) {
                    $('#sells').html(result);
                }
            });
        }
        if (event.detail.pathInfo.requestPath.match(/^\/htmx\/supplier\/\d+\/edit$/g)) {
            let id = $('#supplier-slug').data('id');
            $.ajax({
                url: "htmx/supplier/" + id + "/load-details",
                success: function (result) {
                    $('#supplier-info').html(result);
                    console.log(result);
                },
                error: function (result) {
                    console.log(result);
                }
            }).done(function () {
                let slug = $('#supplier-slug').data('slug');
                console.log(slug);
                history.pushState(null, null, '/' + slug);
            });

        }
    });
});
