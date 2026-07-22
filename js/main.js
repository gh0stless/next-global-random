(function () {
    'use strict';

    var wrapper = document.getElementById('globalrandom-wrapper');
    var frame = document.getElementById('globalrandom-frame');
    var buttons = document.querySelectorAll('.globalrandom-nav-btn');

    var sources = {
        app: wrapper.dataset.appSrc,
        de: wrapper.dataset.deSrc,
        en: wrapper.dataset.enSrc,
    };

    buttons.forEach(function (button) {
        button.addEventListener('click', function () {
            var target = button.dataset.target;
            if (!sources[target] || frame.src === sources[target]) {
                return;
            }
            frame.src = sources[target];
            buttons.forEach(function (b) { b.classList.remove('active'); });
            button.classList.add('active');
        });
    });
})();
