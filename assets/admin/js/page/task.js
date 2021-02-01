function onChange(changed, findAndReplace) {
    $(document).on('change', 'select.' + changed, function () {
        const $form = $(this).closest('form');
        $.ajax({
            url: $form.attr('action'),
            type: $form.attr('method'),
            data: $form.serializeArray(),
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
