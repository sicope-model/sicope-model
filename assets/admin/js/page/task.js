function onChange(selector, findAndReplaceSelectors) {
    $(document).on('change', selector, function () {
        const $form = $(this).closest('form');
        const names = [
            'task[selenium_config][provider]',
            'task[selenium_config][platform]',
            'task[selenium_config][browser]',
            'task[selenium_config][browserVersion]',
            'task[selenium_config][resolution]',
        ];
        const data = $form.serializeArray().reduce(function(obj, item) {
            if (names.includes(item.name)) {
                obj[item.name] = item.value;
            }
            return obj;
        }, {});
        $.ajax({
            url: $form.attr('action'),
            type: $form.attr('method'),
            data: data,
            success: function (html) {
                findAndReplaceSelectors.forEach(findAndReplace => {
                    const options = $(html).find(findAndReplace + ' > option').map(function () {
                        return {
                            value: this.value,
                            text: this.text
                        };
                    });
                    const selectize = $(findAndReplace)[0].selectize;
                    selectize.clear();
                    selectize.clearOptions();
                    selectize.addOption(Object.values(options));
                    selectize.addItem(options[0].value);
                });
            }
        });
    });
}

$(function () {
    onChange('select.providers', ['select.platforms']);
    onChange('select.platforms', ['select.browsers', 'select.resolutions']);
    onChange('select.browsers', ['select.browser-versions']);
});
