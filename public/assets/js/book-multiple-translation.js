$(document).ready(function () {

    $('.load-translation').on('click', function (e) {
        e.preventDefault();

        let btn = $(this);
        let maxLanguagesCount = 4;
        let requestUri = $(btn).data('request-uri');
        let requestUriLangCount = requestUri.split('~').length;
        let langCode = $(btn).data('lang-code');

        if (requestUriLangCount < maxLanguagesCount) {
            let url = requestUri + '~' + langCode;
            window.location.replace(url);
        }

        if (requestUriLangCount === maxLanguagesCount) {
            console.log('you  have reached max number of languages');
        }

    });


    $('.disable-lang').on('change', function (e) {
        let checkbox = $(this);
        let langCode = $(checkbox).data('lang-code');
        let requestUri = $(checkbox).data('request-uri');
        let requestUriArray = requestUri.split('&');
        let requestedBookAndChapter = requestUriArray[0];
        let requestUriLang = requestUriArray[1].split('~');
        let uriToRedirect = '';

        for (let i = 0; i < requestUriLang.length; i++) {
            if (requestUriLang[i] === langCode) {
                requestUriLang.splice(i, 1);
            }
        }

        requestUriLang = requestUriLang.join('~')
        uriToRedirect = requestedBookAndChapter + '&' + requestUriLang;

        window.location.replace(uriToRedirect);
    })

});
