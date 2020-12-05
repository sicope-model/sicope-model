const options = {};

$(function () {
    initOptions();
    initPlaces($('.places'));
    initTransitions($('.transitions'));
    initCommand($('.actions,.assertions'));
    initToPlaces($('.to-places'));
    initToPlace($('.select-to-place'));
    initFromPlaces($('.select-from-places'));
});

function initPlaces(elements) {
    elements.formCollection({
        'other_btn_add': '.add-place',
        'btn_delete_selector': '.remove-place',
        'post_add': function($new_elem, context) {
            initCommand($new_elem.find('.assertions'));
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
}

function initTransitions(elements) {
    elements.formCollection({
        'other_btn_add': '.add-transition',
        'btn_delete_selector': '.remove-transition',
        'post_add': function($new_elem, context) {
            initCommand($new_elem.find('.actions'));
            initToPlaces($new_elem.find('.to-places'));
            initFromPlaces($new_elem.find('.select-from-places'));
        }
    });
}

function initCommand(elements) {
    const place = elements.parents('.place');
    if (place.length) {
        elements.formCollection({
            'other_btn_add': place.find('.add-assertion'),
            'btn_delete_selector': '.assertion .remove-command'
        });
    }
    const transition = elements.parents('.transition');
    if (transition.length) {
        elements.formCollection({
            'other_btn_add': transition.find('.add-action'),
            'btn_delete_selector': '.action .remove-command'
        });
    }
}

function initToPlaces(elements) {
    elements.formCollection({
        'other_btn_add': elements.parents('.transition').find('.add-place'),
        'btn_delete_selector': '.remove-place',
        'post_add': function($new_elem, context) {
            initToPlace($new_elem.find('.select-to-place'));
        }
    });
}

function initToPlace(elements) {
    elements.selectize({
        maxItems: 1,
        options: Object.values(options)
    });
}

function initFromPlaces(elements) {
    const $select = elements.selectize({
        maxItems: null,
        options: Object.values(options)
    });
    $select[0].selectize.setValue(elements.val());
}

function initOptions() {
    $('.place-label').each(function (index) {
        options[index] = $(this).val();
    });
}

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
