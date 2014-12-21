/**
 * Carousel module
 *
 * @namespace APP.page
 * @class carousel
 */

APP.namespace.create('page.carousel');
APP.page.carousel = (function (mywindow) {

    var $ = mywindow.jQuery,
        _ = mywindow._,
        Modernizr = mywindow.Modernizr,

        settings = {
            trigger  : '[data-trigger=carousel]',
            autoplay : 'carousel-autoplay',
            fade     : 'carousel-fade',
            crsl_id  : 'carousel-id',
            order    : 'carousel-order',
            delay    : 3500,
            speed    : 750,
            easing   : 'swing',
            cover    : 'fade',
            stage    : 'cr-stage',
            nav      : 'cr-nav',
            nav_now  : 'active',
            prev     : 'cr-prev',
            next     : 'cr-next'
        },

        tmpl = {
            cover : _.template('<li class="' + settings.cover + '"></li>'),
            nav   : _.template('<ul class="clearfix"><% _.each(list, function(num) { %><li><a href="#<%= num %>"<% if (num === 1) { %> class="' + settings.nav_now + '"<% } %>></a></li><% }); %></ul>')
        },

        internal = {
            touch : false,
            instance : []
        };


    function _setup($obj) {

        var lis  = $obj.find('ul.' + settings.stage).children('li'),
            auto = parseInt($obj.data(settings.autoplay)),
            fade = parseInt($obj.data(settings.fade)),
            new_carousel = {
                id : _.uniqueId('crsl_'),
                interval : false,
                current  : 1,
                total    : 0,
                inprogress : false
            };

        if (lis.length > 1) {

            if (!_.isNaN(auto) ) {
                new_carousel.delay = auto;
            } else {
                new_carousel.delay = settings.delay;
            }

            if (!_.isNaN(fade) ) {
                new_carousel.speed = fade;
            } else {
                new_carousel.speed = settings.speed;
            }

            $obj.attr('data-' + settings.crsl_id, new_carousel.id);
            new_carousel.total = lis.length;

            internal.instance.push(new_carousel);
            _create($obj, new_carousel.id);
            _autoPlay(new_carousel.id);
        }
    }

    function _create($obj, id) {

        var $stage = $obj.find('ul.' + settings.stage),
            $nav   = $obj.find('.' + settings.nav),
            $items = $stage.children('li'),
            order  = 0,
            nums   = [],
            $ul;

        $items.each(function () {
            order++;
            nums.push(order);
            $(this).attr('data-' + settings.order, order);
        });

        $ul = _bindNavAction($(tmpl.nav({list : nums})));

        $nav.append($ul);
        $stage.append(tmpl.cover());

        _bindArrows($obj, id);
    }

    function _autoPlay(cr_id) {

        var data = _getById(cr_id);

        if (data.delay !== 0) {

            clearTimeout(data.interval);
            data.interval = setTimeout(function () {
                next(cr_id);
                _autoPlay(cr_id);
            }, data.delay);
        }
    }

    function _bindArrows($obj, id) {

        $obj.find('.' + settings.prev).click(function (ev) {

            ev.preventDefault();
            _autoPlay(id);
            prev(id);
        });

        $obj.find('.' + settings.next).click(function (ev) {

            ev.preventDefault();
            _autoPlay(id);
            next(id);
        });

        if (internal.touch) {

            $obj.swipe({
                swipeLeft: function () {
                    _autoPlay(id);
                    next(id);
                },
                swipeRight: function () {
                    _autoPlay(id);
                    prev(id);
                }
            });
        }
    }

    function _bindNavAction($obj) {

        $obj.find('a').click(function (ev) {

            var $this   = $(this),
                crsl_id = $this.parents(settings.trigger).data(settings.crsl_id),
                href    = $this.attr('href').split('#'),
                slide   = parseInt(href[1]);

            ev.preventDefault();
            _autoPlay(crsl_id);
            goTo(slide, crsl_id);
        });

        return $obj;
    }

    function goTo(slide, cr_id) {

        var data = _getById(cr_id),
            $carousel;

        if (!data.inprogress && data.current !== slide) {

            if (slide < 1) {
                slide = data.total;
            } else if (slide > data.total) {
                slide = 1;
            }

            data.inprogress = true;
            $carousel = $('[data-' + settings.crsl_id + '="' + cr_id + '"]');

            _doCrossFade($carousel, slide, data);
        }
    }

    function next(cr_id) {

        var data  = _getById(cr_id),
            slide = data.current + 1;

        goTo(slide, cr_id);
    }

    function prev(cr_id) {

        var data  = _getById(cr_id),
            slide = data.current - 1;

        goTo(slide, cr_id);
    }

    function _doCrossFade($obj, slide, data) {

        var $slide   = $obj.find('[data-' + settings.order + '=' + slide + ']'),
            $cover   = $obj.find('.' + settings.cover),
            $current = $obj.find('[data-' + settings.order + '=' + data.current + ']'),
            half     = Math.round(data.speed / 2),
            _new;

        $cover.fadeIn(half, settings.easing, function () {

            _new = $slide.detach();
            $current.before(_new);
            _updateNav($obj, slide);

            $cover.fadeOut(half, settings.easing, function () {

                data.current = slide;
                data.inprogress = false;
            });

        });
    }

    function _updateNav($obj, slide) {

        var $nav = $obj.find('.' + settings.nav),
            $all = $nav.find('a'),
            $new = $nav.find('a[href$="#' + slide + '"]');

        $all.removeClass(settings.nav_now);
        $new.addClass(settings.nav_now);
    }

    function _getById(id) {

        return _.first(_.where(internal.instance, {id: id}));
    }

    function initialise() {

        if (Modernizr.touch) {
            internal.touch = true;
        }

        $(settings.trigger).each(function () {
            _setup($(this));
        });
    }

    return {
        'initialise' : initialise,
        'goTo' : goTo,
        'next' : next,
        'prev' : prev
    };

}(window));


