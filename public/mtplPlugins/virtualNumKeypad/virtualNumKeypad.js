$(function () {
    let $activeInput = null;
    let inputValue = '';
    let $keypad = null;
    let $previewText = null;

    function createKeypad() {
        $keypad = $('<div class="virtual-keypad" style="display:none;"></div>');

        const $preview = $('<div class="preview"></div>');
        $previewText = $('<div class="text"></div>');
        const $clearBtn = $('<button type="button" class="clear-btn"><i class="fas fa-eraser"></i></button>');

        $clearBtn.on('click', function () {
            inputValue = '';
            $previewText.text('');
        });

        $preview.append($previewText).append($clearBtn);
        $keypad.append($preview);

        const keys = [
            '7', '8', '9',
            '4', '5', '6',
            '1', '2', '3',
            '-', '0', '.', '←',
            'ENTER', 'CANCEL'
        ];

        $.each(keys, function (_, key) {
            const $btn = $('<div class="key"></div>').text(key);

            if (['←', 'ENTER', 'CANCEL'].includes(key)) $btn.addClass('wide');
            if (key === 'ENTER') $btn.addClass('action');
            if (key === 'CANCEL') $btn.addClass('danger');

            $btn.on('click', function () {
                handleKeyPress(key);
            });

            $keypad.append($btn);
        });

        $('body').append($keypad);
    }

    function handleKeyPress(key) {
        if (!$activeInput) return;

        if (key === 'CANCEL') {
            hideKeypad();
        } else if (key === 'ENTER') {

            //if value is number, limit to 3 decimal places
            if (inputValue && !isNaN(inputValue)) {
                let num = parseFloat(inputValue);
                if (Number.isInteger(num)) {
                    inputValue = num.toString();
                } else {
                    // Check if more than 3 decimals exist
                    const decimals = inputValue.split('.')[1];
                    if (decimals && decimals.length > 3) {
                        inputValue = num.toFixed(3);
                    } else {
                        inputValue = num.toString();
                    }
                }
            }

            $activeInput.val(inputValue).trigger('change');
            hideKeypad();
        } else if (key === '←') {
            inputValue = inputValue.slice(0, -1);
            $previewText.text(inputValue);
        } else {
            inputValue += key;
            $previewText.text(inputValue);
        }
    }

    function showKeypad($input) {
        $activeInput = $input;
        // inputValue = $input.val() || '';
        // $previewText.text(inputValue);
        $keypad.show();
    }

    function hideKeypad() {
        $keypad.hide();
        $activeInput = null;
        inputValue = '';
        $previewText.text('');
    }

    createKeypad();

    $(document).on('mousedown touchstart', function (e) {
        if (
            $keypad.is(':visible') &&
            !$(e.target).closest('.virtual-keypad').length &&
            !$(e.target).is('.numInput')
        ) {
            hideKeypad();
        }
    });


    $(document).on('click focus', '.virtualNumKeypad', function () {
        // if element is not readonly or disabled
        if ($(this).is('[disabled]')) return;
        showKeypad($(this));
    });

    $(document).on('keydown', function (e) {
        if (e.key === 'Escape') {
            hideKeypad();
            return;
        }

        // Handle physical keyboard input when virtual keypad is active
        if ($keypad && $keypad.is(':visible') && $activeInput) {
            e.preventDefault();
            e.stopPropagation();

            const key = e.key;

            // Map physical keys to virtual keypad keys
            if (key >= '0' && key <= '9') {
                handleKeyPress(key);
            } else if (key === '.') {
                handleKeyPress('.');
            } else if (key === '-' || key === 'Minus') {
                handleKeyPress('-');
            } else if (key === 'Backspace') {
                handleKeyPress('←');
            } else if (key === 'Enter') {
                handleKeyPress('ENTER');
            } else if (key === 'Escape') {
                handleKeyPress('CANCEL');
            }
        }
    });
});
