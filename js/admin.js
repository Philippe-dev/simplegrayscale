$(function () {
    // default image
    $('#default-image-selector').on('click', function (e) {
        $('input[name="change-button-id"]').val(this.id);
        window.open('media.php?plugin_id=admin.blog.theme&popup=1&select=1', 'dc_popup', 'alwaysRaised=yes,dependent=yes,toolbar=yes,height=500,width=760,menubar=no,resizable=yes,scrollbars=yes,status=no');
        e.preventDefault();
        return false;
    });

    $('#default-image-selector-reset').on('click', function (e) {
        const url = `${$('input[name="theme-url"]').val()}/img/intro-bg.jpg`;
        const thumb = `${$('input[name="theme-url"]').val()}/img/.intro-bg_s.jpg`;
        $('#default-image-url').val(url);
        $('#default-image-tb-url').val(thumb);
        $('#default-image-thumb-src').attr('src', thumb);
    });

    $('#default-image-url').on('change', function (e) {
        const url = `${$('input[name="theme-url"]').val()}/img/intro-bg.jpg`;
        let thumb = `${$('input[name="theme-url"]').val()}/img/.intro-bg_s.jpg`;
        if ($('#default-image-url').val() == url) {
        } else {
            thumb = $('#default-image-tb-url').val();
        }
        $('#default-image-thumb-src').attr('src', thumb);
    });

    // random images
    for (let i = 0; i < 6; i++) {
        $(`#random-image-${i}-selector`).on('click', function (e) {
            $('input[name="change-button-id"]').val(this.id);
            window.open('media.php?plugin_id=admin.blog.theme&popup=1&select=1', 'dc_popup', 'alwaysRaised=yes,dependent=yes,toolbar=yes,height=500,width=760,menubar=no,resizable=yes,scrollbars=yes,status=no');
            e.preventDefault();
            return false;
        });

        $(`#random-image-${i}-selector-reset`).on('click', function (e) {
            const url = `${$('input[name="theme-url"]').val()}/img/bg-intro-${i}.jpg`;
            const thumb = `${$('input[name="theme-url"]').val()}/img/.bg-intro-${i}_s.jpg`;
            $(`#random-image-${i}-url`).val(url);
            $(`#random-image-${i}-tb-url`).val(thumb);
            $(`#random-image-${i}-thumb-src`).attr('src', thumb);
        });

        $(`#random-image-${i}-url`).on('change', function (e) {
            const url = `${$('input[name="theme-url"]').val()}/img/bg-intro-${i}.jpg`;
            let thumb = `${$('input[name="theme-url"]').val()}/img/.bg-intro-${i}_s.jpg`;
            if ($(`#random-image-${i}-url`).val() == url) {
            } else {
                thumb = $(`#random-image-${i}-tb-url`).val();
            }
            $(`#random-image-${i}-thumb-src`).attr('src', thumb);
        });
    }

    // stickers reorder
    $('#stickerslist').sortable({
        'cursor': 'move'
    });
    $('#stickerslist tr').hover(function () {
        $(this).css({
            'cursor': 'move'
        });
    }, function () {
        $(this).css({
            'cursor': 'auto'
        });
    });
    $('#theme_config').submit(function () {
        const order = [];
        $('#stickerslist tr td input.position').each(function () {
            order.push(this.name.replace(/^order\[([^\]]+)\]$/, '$1'));
        });
        $('input[name=ds_order]')[0].value = order.join(',');
        return true;
    });
    $('#stickerslist tr td input.position').hide();
    $('#stickerslist tr td.handle').addClass('handler');
});
