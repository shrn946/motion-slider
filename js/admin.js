jQuery(document).ready(function ($) {
    let counter = $('#motion-slider-list li').length;

    $('#add-slide').on('click', function () {
        wp.media.editor.send.attachment = function (props, attachment) {
            let html = `
            <li class="motion-slide-item" data-index="${counter}">
                <img src="${attachment.url}" width="100">
                <input type="text" class="motion-title" value="" placeholder="Enter title">
                <button class="remove-slide">Remove</button>
            </li>`;
            $('#motion-slider-list').append(html);
            counter++;
        };
        wp.media.editor.open();
    });

    $('#motion-slider-list').on('click', '.remove-slide', function () {
        $(this).closest('li').remove();
    });

    $('#motion-slider-list').sortable();

    $('#save-slides').on('click', function () {
        let slides = [];
        $('#motion-slider-list li').each(function () {
            slides.push({
                img: $(this).find('img').attr('src'),
                title: $(this).find('.motion-title').val()
            });
        });

        $.post(motion_slider_ajax.ajax_url, {
            action: 'save_motion_slider',
            nonce: motion_slider_ajax.nonce,
            slides: slides
        }, function (res) {
            if (res.success) alert('Slides saved!');
            else alert('Error saving slides.');
        });
    });
});
