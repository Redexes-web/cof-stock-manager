$(function () {
    function main() {
        $('#name').on('focus', function () {
            console.log('hello');
            $('#choices').show();
        });

        $('#name').on('blur', function () {
            setTimeout(function () {
                $('#choices').hide();
            }, 100);
        });

        // on load and resize set size of choices same as input
        // on resize
        function setChoicesSize() {
            $('#choices').css('width', $('#name').outerWidth());
            $('#choices').css('left', $('#name').position().left);
            $('#choices').css('top', $('#name').position().top + $('#name').outerHeight());
        };
        setChoicesSize();
        $(window).on('resize', setChoicesSize);
        // on click of choice
        $('.choice').on('click', function () {
            console.log($(this).data('name'));
            $('#name').val($(this).data('name'));
            $('#price').val($(this).data('price'));
            $('#choices').hide();
        });

        //on click on .minus-btn check if new value is 1 then add <i class="fa-solid fa-trash-can"></i> to .minus-btn
        $('.minus-btn').on('click', function () {
            var input = $(this).parent().find('input');
            var value = parseInt(input.val());
            if (value -1 === 1) {
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
    });
});
