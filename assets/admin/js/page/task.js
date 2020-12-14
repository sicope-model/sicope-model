function onChange(changed, findAndReplace) {
    $(document).on('change', 'select.' + changed, function () {
        const $form = $(this).closest('form');
        const names = [
            'provider',
            'platform',
            'browser',
            'browserVersion',
            'resolution',
        ];
        const data = $form.serializeArray().reduce(function(obj, item) {
            if (names.map(name => 'task[selenium_config][' + name + ']').includes(item.name)) {
                obj[item.name] = item.value;
            }
            return obj;
        }, {});
        $.ajax({
            url: $form.attr('action'),
            type: $form.attr('method'),
            data: data,
            success: function (html) {
                findAndReplace.map(className => 'select.' + className).forEach(select => {
                    const selectize = $(select)[0].selectize;
                    selectize.destroy();
                    $(select).replaceWith(
                        $(html).find(select)
                    );
                });
                selectReload();
            }
        });
    });
}

$(function () {
    onChange('providers', ['platforms', 'browsers', 'browser-versions', 'resolutions']);
    onChange('platforms', ['browsers', 'browser-versions', 'resolutions']);
    onChange('browsers', ['browser-versions']);
});
