function onChange(changed, findAndReplace) {
    $(document).on('change', 'select.' + changed, function () {
        const $form = $(this).closest('form');
        const names = [
              ...[
                  'provider',
                  'platform',
                  'browser',
                  'browserVersion',
                  'resolution',
            ].map(name => 'task[selenium_config][' + name + ']'),
            ...[
                'generator',
                'reducer',
            ].map(name => 'task[task_config][' + name + ']'),
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
                findAndReplace.map(className => 'select.' + className).forEach(select => {
                    const selects = $(select);
                    if (selects.length) {
                        const selectize = selects[0].selectize;
                        selectize.destroy();
                    }
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
    onChange('generators', ['generator-config']);
});
