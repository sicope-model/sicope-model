const options = {};

$(function () {
    $('.places').formCollection({
        'other_btn_add': '.add-place',
        'btn_delete_selector': '.remove-place',
        'post_add': function($new_elem, context) {
            $new_elem.find('.assertions').formCollection({
                'other_btn_add': $new_elem.find('.add-assertion'),
                'btn_delete_selector': '.assertion .remove-command'
            });
            const index = $new_elem.find('.place').attr('index');
            addPlace(index);
            $new_elem.find('.place-label').change(function () {
                updatePlace(index, $(this).val());
            });
        },
        'post_delete': function($delete_elem, context) {
            removePlace($delete_elem.find('.place').attr('index'));
            return true;
        },
    });
    $('.transitions').formCollection({
        'other_btn_add': '.add-transition',
        'btn_delete_selector': '.remove-transition',
        'post_add': function($new_elem, context) {
            $new_elem.find('.actions').formCollection({
                'other_btn_add': $new_elem.find('.add-action'),
                'btn_delete_selector': '.action .remove-command'
            });
            $new_elem.find('.to-places').formCollection({
                'other_btn_add': $new_elem.find('.add-place'),
                'btn_delete_selector': '.remove-place',
                'post_add': function($new_elem, context) {
                    $new_elem.find('.select-to-place').selectize({
                        maxItems: 1,
                        options: Object.values(options)
                    });
                }
            });
            $new_elem.find('.select-from-places').selectize({
                maxItems: null,
                options: Object.values(options)
            });
        }
    });
});

function addPlace(index) {
    console.log('add', index);
    const option = {
        value: index,
        text: ''
    };
    options[index] = option;
    updateSelects(function (control) {
        control.addOption(option);
    });
}

function updatePlace(index, label) {
    console.log('update', index, label);
    options[index].text = label;
    updateSelects(function (control) {
        control.updateOption(index, {
            value: index,
            text: label
        });
    });
}

function removePlace(index) {
    console.log('remove', index);
    delete options[index];
    updateSelects(function (control) {
        control.removeOption(index);
    });
}

function updateSelects(callback) {
    $('.select-from-places, .select-to-place').each(function () {
        let control = this.selectize;
        console.log('control', control);
        callback(control);
    });
}
