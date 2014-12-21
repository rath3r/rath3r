/**
 * Main site module
 *
 * @namespace APP.page
 * @class report
 */

APP.namespace.create('page.report');
APP.page.report = (function (mywindow) {

    var $    = mywindow.jQuery,
        _    = mywindow._,

        settings = {
            fix_class: 'fixed',
            arrow_tgl: 'sprite-main-arrow-down',
            close    : 'close',
            pdf_pages: 'pdf-page'
        },

        $el = {
            bgs   : $('.bg-fix'),
            slides: $('.slide-fix'),
            f_link: $('#feedback-toggle'),
            f_box : $('#feedback'),
            f_form: $('#feedback-from'),
            f_msgs: $('#feedback-message'),
            pdf_links : $('a.open-pdf'),
            pdf_anchor: $('#pdf-page')
        },

        internal = {
            pdf_page_offset: -1,
            pdf_window: false
        };


    function _fixBgs() {

        $el.bgs.waypoint(function (direction) {

           var $this = $(this),
               $title = $this.find('.title');

            if (direction === 'down') {

                $this.addClass(settings.fix_class);
                $title.attr('data-stellar-ratio', '0.475');
                $.stellar('refresh');

            } else if (direction === 'up') {

                $this.removeClass(settings.fix_class);
                $title.removeAttr('data-stellar-ratio');
                $.stellar('refresh');
            }
        });

        $el.slides.waypoint(function (direction) {

            var $this  = $(this),
                $next  = $this.next(),
                $after = $this.nextAll(),
                height = $this.height();

            if (direction === 'down') {

                $this.css({'position': 'fixed', 'top': 0, 'left': 0});
                $next.before('<div style="display: block; height: ' + height + 'px"></div>'); // IE7 fix
                $after.css('z-index', 50); // IE8 fix

            } else if (direction === 'up') {

                $this.removeAttr('style');
                $next.remove(); // IE7 fix
                $after.removeAttr('style'); // IE8 fix
            }
        });
    }

    function _feedbackToggle() {

        var $arrow = $el.f_link.find('.icon');

        if ($el.f_box.is(':visible')) {

            $el.f_box.hide();
            _feedbackReset();
            $el.f_msgs.empty();
            $arrow.removeClass(settings.arrow_tgl);

        } else {

            $el.f_box.show();
            $arrow.addClass(settings.arrow_tgl);
        }
    }

    function _feedbackReset() {

        $el.f_form[0].reset();
    }

    function _feedbackActions() {

        $el.f_link.click(function (ev) {

            ev.preventDefault();
            _feedbackToggle();
        });

        $(document).on('click', '.' + settings.close, function () {
            $(this).parent().remove();
        });

        $el.f_form.submit(function (ev) {

            var $this = $(this),
                url   = $this.attr('action'),
                data  = $this.serialize(),
                btn   = $this.find('button[type="submit"]');

            ev.preventDefault();

            $.ajax({
                url     : url,
                data    : data,
                type    : 'POST',
                cache   : false,
                dataType: 'json',
                beforeSend: function () {

                    btn.attr('disabled', 'disabled');
                    btn.text('Sending...');
                },
                success: function (response) {

                    if (response.status === 'success') {
                        _feedbackReset();
                    }

                    mywindow.Recaptcha.reload();
                    $el.f_msgs.html(response.message);
                },
                error: function () {
                    $el.f_msgs.html('<div class="alert alert-error">Request error. Please try again or later.</div>');
                },
                complete: function () {
                    btn.removeAttr('disabled');
                    btn.text('Send');
                }
            });
        });
    }

    function _watchPDFpages() {

        $('[data-' + settings.pdf_pages + ']').waypoint(function (direction) {

            if (direction === 'down') {

                _changePDFpage($(this));

            } else if (direction === 'up') {

                _changePDFpage($(this));
            }
        });
    }

    function _changePDFpage($obj) {

        var page = $obj.data(settings.pdf_pages),
            current = $el.pdf_anchor.attr('href'),
            q = current.split('#'),
            string = 'page=';

        $el.pdf_anchor.attr('href', q[0] + '#' + string + page);
    }

    function _openPDFaction() {

        $el.pdf_links.click(function (ev) {

            var $this = $(this),
                href  = $this.attr('href'),
                arr   = href.split('='),
                page  = parseInt(arr[1]) + internal.pdf_page_offset,
                url   = arr[0] + '=' + page;

            ev.preventDefault();

            internal.pdf_window = window.open('about:blank', 'pdf');

            setTimeout(function () {
                internal.pdf_window = window.open(url, 'pdf');
                internal.pdf_window.focus();
            }, 1);
        });
    }

    function _setupPDF() {

        if (APP.browser.win) {
            internal.pdf_page_offset = 0;
        }
    }

    function _parallax() {

        $.stellar({
            horizontalScrolling: false,
            responsive: true
        });
    }

    function _fixMobileTitle() {
        $('#home-content').find('div.title').css('position', 'absolute');
    }

    function _init() {

        _setupPDF();

        $(document).ready(function () {

            if (!APP.browser.handheld) {
                _fixBgs();
                _parallax();
            } else {
                _fixMobileTitle();
            }

            _openPDFaction();
            _feedbackActions();
            _watchPDFpages();
            $el.f_box.hide();

            APP.page.carousel.initialise();
        });

        $(mywindow).load(function () {


        });
    }

    // Initialize application
    _init();

    return {

    };

}(window));


