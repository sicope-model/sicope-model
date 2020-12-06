const options = {};

$(function () {
    initPlaces($('.places'));
    initTransitions($('.transitions'));
});

function initPlaces(elements) {
    elements.formCollection({
        'call_post_add_on_init': true,
        'other_btn_add': '.add-place',
        'btn_delete_selector': '.remove-place',
        'post_add': function($new_elem) {
            $new_elem.attr('index', $new_elem.index());
            initCommand($new_elem.find('.assertions'));
            addPlace($new_elem.index(), $new_elem.find('.place-label').val());
            $new_elem.find('.place-label').change(function () {
                updatePlace($new_elem.index(), $(this).val());
            });
        },
        'post_delete': function($delete_elem) {
            removePlace($delete_elem.attr('index'));
        },
    });
}

function initTransitions(elements) {
    elements.formCollection({
        'call_post_add_on_init': true,
        'other_btn_add': '.add-transition',
        'btn_delete_selector': '.remove-transition',
        'post_add': function($new_elem) {
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
        'call_post_add_on_init': true,
        'other_btn_add': elements.parents('.transition').find('.add-to-place'),
        'btn_delete_selector': '.remove-place',
        'post_add': function($new_elem, context) {
            initToPlace($new_elem.find('.select-to-place'));
        }
    });
}

function initToPlace(elements) {
    elements.each(function () {
        const selected = this.defaultValue;
        const $select = $(this).selectize({
            maxItems: 1,
            options: Object.values(options)
        });
        $select[0].selectize.addItem(selected);
    });
}

function initFromPlaces(elements) {
    elements.each(function () {
        const $select = $(this).selectize({
            maxItems: null,
            options: Object.values(options)
        });
        $select[0].selectize.addItem($(this).val());
    });
}

function addPlace(index, label) {
    const option = {
        value: index,
        text: label
    };
    options[index] = option;
    updateSelects(function (control) {
        control.addOption(option);
    });
}

function updatePlace(index, label) {
    options[index].text = label;
    updateSelects(function (control) {
        control.updateOption(index, {
            value: index,
            text: label
        });
    });
}

function removePlace(index) {
    delete options[index];
    updateSelects(function (control) {
        control.removeOption(index);
    });
}

function updateSelects(callback) {
    $('.select-from-places, .select-to-place').each(function () {
        let control = this.selectize;
        if (control) {
            callback(control);
        }
    });
}
