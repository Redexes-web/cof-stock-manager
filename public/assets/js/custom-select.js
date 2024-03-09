$(document).ready(function () {

    function initializeSelect(select) {
        const optionBox = select.nextElementSibling;
        const options = [...optionBox.querySelectorAll('.item')];

        let activeOption = 0; // default should be 0

        select.addEventListener('click', function (e) {
            select.classList.toggle('active');
            optionBox.classList.toggle('active');
        });

        optionBox.addEventListener('click', function (e) {
            if (e.target.classList.contains('item')) {
                const index = options.indexOf(e.target);
                hoverOptions(index);
            }
        });

        function hoverOptions(i) {
            options[activeOption].classList.remove('active');
            options[i].classList.add('active');
            activeOption = i;
        }

        window.onkeydown = function (e) {
            if (select.classList.contains('active')) {
                e.preventDefault();
                if (e.key === 'ArrowDown' && activeOption < options.length - 1) {
                    hoverOptions(activeOption + 1);
                } else if (e.key === 'ArrowUp' && activeOption > 0) {
                    hoverOptions(activeOption - 1);
                } else if (e.key === 'Enter') {
                    select.classList.remove('active');
                    optionBox.classList.remove('active');
                }
            }
        }
    }

    document.addEventListener('htmx:afterSwap', function (event) {
        const selects = document.querySelectorAll('.select');
        selects.forEach(select => {
            initializeSelect(select);
        });
    });
});
