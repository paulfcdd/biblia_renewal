$(document).ready(function () {
    $('.play-audio').on('click', function () {
        let btn = $(this);
        let bookAudioTray = $('.book-audio-tray');
        let buttonLabel = $('span.button-label');
        let btnToggle = $(btn).data('toggle');

        switch (btnToggle) {
            case 'off':
                $(bookAudioTray).animate({'height': '3.5em'}, 800);
                $(btn).data('toggle', 'on');
                $(buttonLabel).text('Перестать слушать');
                break;
            case 'on':
                $(bookAudioTray).animate({'height': '0'}, 800);
                $(btn).data('toggle', 'off');
                $(buttonLabel).text('Слушать аудио');
                break;
        }
    })
});
