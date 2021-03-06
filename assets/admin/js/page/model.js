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
        'btn_up_selector': '.move-up-place',
        'btn_down_selector': '.move-down-place',
        'post_add': function($new_elem) {
            $new_elem.attr('index', $new_elem.index());
            initCommands($new_elem.find('.commands'));
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
        'btn_up_selector': '.move-up-transition',
        'btn_down_selector': '.move-down-transition',
        'post_add': function($new_elem) {
            initCommands($new_elem.find('.commands'));
            initToPlaces($new_elem.find('.select-to-places'));
            initFromPlaces($new_elem.find('.select-from-places'));
        }
    });
}

function initCommands(elements) {
    const parent = elements.closest('.place, .transition');
    if (parent.length) {
        elements.formCollection({
            'other_btn_add': parent.find('.add-command'),
            'btn_delete_selector': '.command .remove-command',
            'btn_up_selector': '.command .move-up-command',
            'btn_down_selector': '.command .move-down-command',
            'prototype_name': '__command__',
            'post_add': function($new_elem) {
                initCommand($new_elem.find('.select-command'));
            }
        });
    }
}

function initCommand(elements) {
    elements.each(function () {
        const $select = $(this).selectize();
        $select[0].selectize.addItem($(this).val());
    });
}

function initToPlaces(elements) {
    initFromPlaces(elements);
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
    $('.select-from-places, .select-to-places').each(function () {
        let control = this.selectize;
        if (control) {
            callback(control);
        }
    });
}
