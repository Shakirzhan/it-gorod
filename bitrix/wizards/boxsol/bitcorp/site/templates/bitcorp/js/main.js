//TODO: 
/*
    1. Set SITE_DIR in $.validator.addMethod('captcha');
*/
/* Main Slider */
$(document).ready(function(){
    var bigSlider = $bigSlider = $('.owl-slider'),
        bigSliderAutoplay = bigSlider.attr('data-autoplay'),
        bigSliderAutoplayTimeout = bigSlider.attr('data-autoplay-timeout'),
        bigSliderAnimationIn = bigSlider.attr('data-animationin'),
        bigSliderAnimationOut = bigSlider.attr('data-animationout');

    if( bigSliderAutoplay == "true" ) { bigSliderAutoplay = true; } else { bigSliderAutoplay = false; }        

    bigSlider.owlCarousel({
        nav : true,
        navText : ["",""],
        loop : true,
        mouseDrag : false,
        margin:30,
        autoHeight:true,
        autoplay : bigSliderAutoplay,
        autoplayTimeout : bigSliderAutoplayTimeout,
        autoplayHoverPause : true,
        animateIn : bigSliderAnimationIn,
        animateOut : bigSliderAnimationOut,
        responsive:{
            0:{
                items:1,
                nav:false
            },
            769:{
                items:1,
                nav:true
            },
            1000:{
                items:1,
                nav:true,
                loop:true
            }
        }
    });
});
/* Main Slider */

/* Rewiew Slider */
$(document).ready(function(){
    var reviewSlider = $reviewSlider = $('.owl-slider-rewiew'),
        reviewSliderAutoplay = reviewSlider.attr('data-autoplay'),
        reviewSliderAutoplayTimeout = reviewSlider.attr('data-autoplay-timeout');

        if( reviewSliderAutoplay == "true" ) { reviewSliderAutoplay = true; } else { reviewSliderAutoplay = false; } 
    $('.owl-slider-rewiew').owlCarousel({
        margin:31,
        nav : true,
        navText : ["",""],
        responsiveClass:true,
        navContainerClass : "owl-nav owl-nav_rewiews",
        dotsClass : "owl-dots owl-dots_rewiews",
        loop : false,
        autoHeight : true,
        rewind : true,
        autoplay : reviewSliderAutoplay,
        autoplayTimeout : reviewSliderAutoplayTimeout,
        responsive:{
            0:{
                items:1,                
                nav:false,
                autoHeight: true
            },
            769:{
                items:1,
                nav:true,
                autoHeight: true
            },
            1000:{
                items:2,
                nav:true,               
            },
            1300:{               
                autoplayHoverPause : true,
                items:3,
                nav:true,                
            }
        }
    })
});
/* Rewiew Slider */

/* Partners Slider */
$(document).ready(function(){
    var partnersSlider = $partnersSlider = $('.owl-slider-partners'),
        partnersSliderAutoplay = partnersSlider.attr('data-autoplay'),
        partnersSliderAutoplayTimeout = partnersSlider.attr('data-autoplay-timeout');

        if( partnersSliderAutoplay == "true" ) { partnersSliderAutoplay = true; } else { partnersSliderAutoplay = false; }
    $('.owl-slider-partners').owlCarousel({
        autoplay : partnersSliderAutoplay,
        autoplayTimeout : partnersSliderAutoplayTimeout,
        margin:-1,
        nav : true,
        navText : ["",""],
        responsiveClass:true,
        navContainerClass : "owl-nav owl-nav_rewiews",
        dotsClass : "owl-dots owl-dots_rewiews",
        loop : false,
        autoHeight : true,
        rewind : true,
        responsive:{
            0:{
                items:2,                
                nav:false,
                autoHeight: false
            },
            769:{
                items:3,
                nav:true,
                autoHeight: false
            },
            1000:{
                items:5,
                nav:true,               
            },
            1300:{                
                autoplayHoverPause : true,
                items:5,
                nav:true,                
            }
        }
    })
});
/* Partners Slider */

/* Catalog Slider */
$(document).ready(function(){
       
    $('.owl-slider-main-catalog').owlCarousel({
        margin:29,
        nav : true,
        navText : ["",""],
        responsiveClass:true,
        navContainerClass : "owl-nav owl-nav_rewiews",
        dotsClass : "owl-dots owl-dots_rewiews",
        loop : false,
        autoHeight : true,
        rewind : true,
        stageClass : "row-equal",
        responsive:{
            0:{
                items:1,
                nav:false,
                autoHeight: true
            },
            540:{
                items:2,
                nav:true,
                autoHeight: true
            },
            769:{
                items:2,
                nav:true,
                autoHeight: true
            },
            1000:{
                items:3,
                nav:true,
            },
            1300:{
                autoplayHoverPause : true,
                items:4,
                nav:true,
            }
        }
    })
});
/* Catalog Slider */

/* Catalog Inner Slider */
$(document).ready(function(){
       
    $('.owl-slider-product-page').owlCarousel({
        margin:30,
        nav : true,
        navText : ["",""],
        responsiveClass:true,
        navContainerClass : "owl-nav owl-nav_rewiews",
        dotsClass : "owl-dots owl-dots_rewiews",
        loop : false,
        autoHeight : true,
        rewind : true,
        stageClass : "row-equal",
        responsive:{
            0:{
                items:1,                
                nav:false,
                autoHeight: true
            },
            769:{
                items:2,
                nav:false,
                autoHeight: true
            },
            1000:{
                items:2,
                nav:true,               
            },
            1300:{
                autoplayHoverPause : true,
                items:3,
                nav:true,                
            }
        }
    })
});
/* Catalog Slider */

/*Tabs*/
$(document).ready(function(){
    var n;
    $(".nav-tabs").on("click", "li:not(.active)", function() {
        n = $(this).parents(".tabs"), $(this).dmtabs(n)
    }), $.fn.dmtabs = function(n) {
        $(this).addClass("active").siblings().removeClass("active"), n.find(".tab-pane").eq($(this).index()).show(1, function() {
            $(this).addClass("open_tab")
        }).siblings(".tab-pane").hide(1, function() {
            $(this).removeClass("open_tab")
        })
    }
});
/*Tabs*/

/* Fancybox */
$(document).ready(function(){
    $().fancybox({
        selector : '[data-fancybox="gallery"]',
        protect: true,
        toolbar  : true,
        buttons : ['close'],                   
    });
});
/* Fancybox */

/* Flex menu */
function calcNavWidth() {
    var navwidth = 0;
    var morewidth = $('.main-menu.flex .more').outerWidth(true);

    $('.main-menu.flex > li:not(.more)').each(function() {
        navwidth += $(this).outerWidth( true );
    });

    //var availablespace = $('nav').outerWidth(true) - morewidth;
    var availablespace = $('.nav-main').width() - morewidth;

    if (navwidth > availablespace) {
        var lastItem = $('.main-menu.flex > li:not(.more)').last();
        lastItem.attr('data-width', lastItem.outerWidth(true));
        lastItem.prependTo($('.main-menu.flex .more ul.more-dropdown-menu'));
        if($(lastItem).hasClass('main-menu--item_drop')){            
            $(lastItem).find('ul').removeClass('dropdown-menu').addClass('dropdown-menu-lev2');
        }
        calcNavWidth();
    } else {

        var firstMoreElement = $('.main-menu.flex li.more li').first();
        if (navwidth + firstMoreElement.data('width') < availablespace) {
            firstMoreElement.insertBefore($('.main-menu.flex .more'));
        }
    }

    if ($('.more li').length > 0) {
        $('.more').css('display','block');
    } else {
        $('.more').css('display','none');
    }
}
$(document).ready(function() {
    calcNavWidth();
});
$(window).resize(function() {
    calcNavWidth();
});
/* Flex menu */


/* Mobile menu */
$( document ).ready(function() {
    $('.mobile-menu--btn').click(function(){
        $(this).toggleClass('mobile-menu--btn_opened');
        $('.mobile-menu--block').slideToggle(100);
        $('body').toggleClass('m-menu-opened');
    });
});
/* Mobile menu */

/* Mobile menu - show 2 level menu */
$( document ).ready(function(i) {
    var o, n;
    i(".js-show-m-dropdown-menu").on("click", function() {
        o = i(this).parents(".with-dropdown").first(),
        n = o.find(".m-dropdown-menu").first(),
        o.hasClass("mobile-menu--item_opened") ? (o.removeClass("mobile-menu--item_opened"),
        n.slideUp()) : (o.addClass("mobile-menu--item_opened"), n.stop(!0, !0).slideDown(),
        o.siblings(".mobile-menu--item_opened").removeClass("mobile-menu--item_opened").children(".m-dropdown-menu").stop(!0, !0).slideUp())
    })
});
/* / Mobile menu - show 2 level menu */




/*close info row*/
$( document ).ready(function() {
    $('.info-row--close').click(function(){
        $('.info-row').slideUp(300);
        $('.info-row').addClass('info-row_closed');
    });
});
/*/close info row*/

/*  контейнеры одной высоты
    https://github.com/liabru/jquery-match-height
*/
$(document).ready(function(){    
    $('.main-services .row-equal > div, .services .row-equal > div').matchHeight({
        byRow: false,
    });
    $('.main-catalog .row-equal > div').matchHeight({
        byRow: false,
    });
    $('.inner-catalog .row-equal > div').matchHeight({
        byRow: false,
    });
    $('.main-categories .row-equal > div').matchHeight({
        byRow: false,
    }); 
    $('.catalog-sections .row-equal > div').matchHeight({
        byRow: false,
    });
    $('.projects-2 .row-equal > div').matchHeight({
        byRow: false,
    });
    $('.product-items.row-equal > div').matchHeight({
        byRow: false,
    });  
    $('.teasers_line .row-equal > div').matchHeight({
        byRow: false,
    });    
     $('.row-equal.peoples > div, .row-equal.inner-team > div').matchHeight({
        byRow: false,
    });
    
});
/*контейнеры одной высоты end*/


/* Аккордеон */
$(document).ready(function(){
      var o, n;
      $(".accordion-item--title").on("click", function() {
        o = $(this).parents(".accordion-item"), n = o.find(".accordion-item--content"),
          o.hasClass("accordion-item_opened") ? (o.removeClass("accordion-item_opened"),
            n.slideUp()) : (o.addClass("accordion-item_opened"), n.stop(!0, !0).slideDown(),
            o.siblings(".accordion-item_opened").removeClass("accordion-item_opened").children(
              ".accordion-item--content").stop(!0, !0).slideUp())
      });
});

/* Аккордеон end*/

/*jqModal */
$(document).ready(function(){
    $('*[data-event="jqm"]').jqmEx();
})

function onLoadjqm( hash ){
    var name = hash.t.className.split(/[\s\!,\.\?]+/g).pop();
    var formName = $(hash.t).data('name')
    var top = (($(window).height() > hash.w.height()) ? Math.floor(($(window).height() - hash.w.height()) / 2) : 0) + 'px';   
    $.each( $(hash.t).get(0).attributes, function( index, attr ){
        if( /^data\-autoload\-(.+)$/.test( attr.nodeName ) ){           
            var key = attr.nodeName.match(/^data\-autoload\-(.+)$/)[1];
            if( $('.jqmWindow [name="'+key+'"]').is('select') ){
                $('select[name="'+key+'"]').find('#'+$(hash.t).data('autoload-'+key)).attr('selected', 'selected');
            }else if( $('.jqmWindow [name="'+key+'"]').is('input') ){
                $('input[name="'+key+'"]').val( $(hash.t).data('autoload-'+key) ).attr('readonly', 'readonly');
            }else{
                console.log('onLoadjqm: warning, not defined type');
            }
        }
    });   

    //set input value received from link params
    //catalog item order
    if(formName == 'catalog'){       
        if($(hash.t).data('param-item')) {           
            $('input[name="PRODUCT"]').val($(hash.t).data('param-item')).attr('readonly', 'readonly').addClass( "input-field" );
        }
    }

    //catalog item question
    if(formName == 'catalog-question'){       
        if($(hash.t).data('param-item')) {           
            $('input[name="PRODUCT"]').val($(hash.t).data('param-item')).attr('readonly', 'readonly');            
        }
    }
    
    if( $(hash.t).data('autohide') ){
        $(hash.w).data('autohide', $(hash.t).data('autohide'));
    }
        
    
    hash.w.addClass('show').css({ 'margin-left': '-' + Math.floor(hash.w.width() / 2) +'px', 'top': top, 'opacity': 1 });
    setTimeout( function(){ $('body').scrollTop(); }, 300 );
}

function onHide( hash ){
    if( $(hash.w).data('autohide') ){
        eval( $(hash.w).data('autohide') );
    }    
    
    hash.w.css('opacity', 0).hide();
    hash.w.empty();
    hash.o.remove();
    hash.w.removeClass('show');
}

$.fn.jqmEx = function( options ){
    if( !$(this).length ){
        return this;
    }
    
    $(this).each(function(){
        var _this = $(this);
        var name = _this.data('name');
        var script = _this.data('ajax');
        var paramsStr = '';       
        var arTriggerAttrs = {};
        //var script = "ajax/form.php";
        $.each( _this.get(0).attributes, function( index, attr ){
            var attrName = attr.nodeName;
            var attrValue = _this.attr(attrName);
            if( /^data\-param\-(.+)$/.test( attrName ) ){ 
                arTriggerAttrs[attrName] = attrValue;
                var key = attrName.match(/^data\-param\-(.+)$/)[1];
                paramsStr += key + '=' + attrValue + '&';                           
            }
        });

        //script += '?' + encodeURIComponent(paramsStr);
        var triggerAttrs = JSON.stringify(arTriggerAttrs);
        var encTriggerAttrs = encodeURIComponent(triggerAttrs);
        script += '?ajax-data=' + encTriggerAttrs;
       
        
        if( $('.'+name+'_frame').length > 0 ){          
            return;
        }
        if( _this.attr('disabled') != 'disabled' ){
            $('body').find('.'+name+'_frame').remove();
            $('body').append('<div class="'+name+'_frame jqmWindow" style="width: 500px"></div>');            
            $('.'+name+'_frame').jqm({trigger: '*[data-name="'+name+'"]', onLoad: function( hash ){ onLoadjqm( hash ); }, onHide: function(hash){ onHide( hash ); }, ajax: script });
        }
    });
    
    return this;
}
CheckModalPosition = function(){
    var modal = $('.jqmWindow.show');
    if(modal.length){
        var documentScollTop = $(document).scrollTop();
        var windowHeight = $(window).height();
        var modalTop = parseInt(modal.css('top'));
        var modalHeight = modal.height();

        if(windowHeight >= modalHeight){
            // center
            modalTop = (windowHeight - modalHeight) / 2;
        }
        else{
            if(documentScollTop > documentScrollTopLast){
                // up
                modalTop -= documentScollTop - documentScrollTopLast;
            }
            else if(documentScollTop < documentScrollTopLast){
                // down
                modalTop += documentScrollTopLast - documentScollTop;
            }

            if(modalTop + modalHeight < windowHeight){
                // bottom
                modalTop = windowHeight - modalHeight;
            }
            else if(modalTop > 0){
                // top
                modalTop = 0;
            }
        }
        modal.css('top', modalTop + 'px');
    }
}

documentScrollTopLast = $(document).scrollTop();
$(window).scroll(function(){
    CheckModalPosition();
    documentScrollTopLast = $(document).scrollTop();    
});

$(window).resize(function(){
    CheckModalPosition();   
    documentScrollTopLast = $(document).scrollTop();
});
/*jqModal end*/




/*Form input*/
$(document).ready(function () {
    $('.fluid-label').fluidLabel({
        focusClass: 'focused'
    });
});
/*Form input*/


/*content wrapper min height*/
function setCWrapperMinHeight(){
    var headerHeight = $('.header').outerHeight(),
        footerHeight = $('.footer').outerHeight(),
        pageHeadHeight = $('.page-head').outerHeight(),
        bodyHeight = $('body').outerHeight(),
        pageHeight = $(document).outerHeight(true),       
        cwHeight = pageHeight - footerHeight - headerHeight - pageHeadHeight;
    if(cwHeight > 0){
        if(bodyHeight < pageHeight){
            $('.content-wrapper').css('min-height', cwHeight);
        }  
    }
}

$(document).ready(function () {
    setCWrapperMinHeight();    
});
$(window).resize(function () {
    setCWrapperMinHeight();    
});
/*/content wrapper min height*/


/*Show and hide header on scrolling*/
$(document).ready(function(){    
    if(arJsFrontParametrs['FIX_TOP_MENU'] == 'Y'){
        if($('.main-menu-wrapper').length){
            $('.main-menu-wrapper').sticky({topSpacing:0, zIndex : '999',className: 'header_fix'});
        } else if($('.second_header').length) {
            $('.second_header').sticky({topSpacing:0, zIndex : '999', className: 'header_fix'});    
        }        
    }   
});
/*Show and hide header on scrolling*/




/* скрипт для меню, чтобы исправить выход меню трретьего уровня за пределы страницы
$(document).ready(function(){
    var dropdownMenuItem = $('.main-menu--item_drop').eq(-2);
    var dropdownMenuOffsetRight = ($(window).width() - (dropdownMenuItem.offset().left + dropdownMenuItem.outerWidth()));

    if(dropdownMenuOffsetRight < 500){
        dropdownMenuItem.find('.dropdown-menu').addClass('dropdown-menu_pull-right');
    }
});
*/
$(document).ready(function () {   
    $('.order-button-anchor').click(function(event) {
        event.preventDefault();
        var link = this;        
        if(link.hash != ''){           
            $.smoothScroll({
                scrollTarget: link.hash
            });
        }
    });    
});

$(document).ready(function () {
    $.extend( $.validator.messages, {
        required: BX.message('JS_REQUIRED'),
        email: BX.message('JS_FORMAT'),
        equalTo: BX.message('JS_PASSWORD_COPY'),
        minlength: BX.message('JS_PASSWORD_LENGTH'),
        remote: BX.message('JS_ERROR')
       
    });

    $.validator.addMethod(
        'regexp', function( value, element, regexp ){
            var re = new RegExp( regexp );
            return this.optional( element ) || re.test( value );
        },
        BX.message('JS_FORMAT')
    );

    $.validator.addClassRules({
        'phone':{
            regexp: arJsFrontParametrs['FORMS_VALIDATE_PHONE_MASK'],
        },
        'captcha':{
            captcha: ''
        }       
    });    
    
    $.validator.addMethod(
        'captcha', function( value, element, params ){
            return $.validator.methods.remote.call(this, value, element,{
                url: arJsFrontParametrs['SITE_DIR'] + 'ajax/check-captcha.php',               
                type: 'post',
                data:{
                    CAPTCHA_WORD: value,
                    CAPTCHA_SID: function(){                        
                        return $(element).closest('form').find('input[name="CAPTCHA_SID"]').val();
                    }
                }
            });
        },
        BX.message('JS_REQUIRED')
    );

    $.validator.setDefaults({
       highlight: function( element ){
            $(element).parent().addClass('error');
        },
        unhighlight: function( element ){
            $(element).parent().removeClass('error');
        },
        errorPlacement: function( error, element ){
            error.insertAfter(element);
        },
        errorElement: "div",
        errorClass: "input-error",
    });
});