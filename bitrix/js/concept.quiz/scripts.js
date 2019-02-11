function scrollToTop(e, i, t) {
	jQuery(t).parents(i).animate({
		scrollTop: e
	}, 300)
}

function wqecResizeVideo() {
	var e = jQuery(window).height(),
		i = 505;
	jQuery(window).width() < 767 && (i = 300), e > i ? jQuery("div.wqec-modal.wqec-active div.wqec-dialog").addClass("wqec-absolute") : jQuery("div.wqec-modal.wqec-active div.wqec-dialog").removeClass("wqec-absolute")
}

function wqecResizeAgModal() {
	jQuery("div.wqec-agreement.wqec-active div.wqec-dialog").height() < jQuery(window).height() ? jQuery("div.wqec-agreement.wqec-active div.wqec-dialog").addClass("wqec-absolute") : jQuery("div.wqec-agreement.wqec-active div.wqec-dialog").removeClass("wqec-absolute")
}

function wqecPercent(e) {
	e.find("div.weqc-tab-percent").css("right", e.find("div.wqec-perc-info").outerWidth() + "px")
}

function openWiz(e, i) {
	"call" == i && (jQuery('div.wqec-big-wrap[data-wqec-section-id="' + e + '"]').remove(), jQuery("div.wqec-xLoader").addClass("wqec-active"), jQuery.post("/bitrix/js/concept.quiz/component.php", {
		wqecSectionCode: e
	}, function(i) {
		jQuery(".call-wqec[data-wqec-section-id=" + e + "]").addClass("open-wqec").removeClass("call-wqec"), jQuery("div.wqec-xLoader").removeClass("wqec-active"), jQuery("div.areaForWqec").append('<div class="wqec-big-wrap" data-wqec-section-id="' + e + '"></div>'), jQuery('div.wqec-big-wrap[data-wqec-section-id="' + e + '"]').html(i), jQuery('div.wqec-big-wrap[data-wqec-section-id="' + e + '"]').find("div.wqec").attr("data-wqec-id", e);
		var t = jQuery("div.wqec[data-wqec-id='" + e + "']");
		jQuery("body").addClass("wqec-on"), jQuery("div.wqec-shadow").addClass("wqec-active"), setTimeout(function() {
			t.find("form.wqec-form input.cwiz-url").val(decodeURIComponent(location.href)), t.addClass("wqec-active"), wqecPercent(t), jQuery("div.wqec").hasClass("wizard-quest-edition-concept") && (setTimeout(function() {
				t.find("div.wqec-left-side").addClass("wqec-ready")
			}, 1e3), setTimeout(function() {
				t.find("div.wqec-right-side").addClass("wqec-ready")
			}, 1500), setTimeout(function() {
				t.find("div.wqec-vertical-side").addClass("wqec-ready")
			}, 2e3), setTimeout(function() {
				t.find("div.wqec-content").addClass("wqec-ready"), t.find("a.wqec-mainclose").addClass("wqec-ready")
			}, 2700), setTimeout(function() {
				var e = t.find(".quiz-head-part").height();
				jQuery(window).width() <= 991 && (e = t.find(".wqec-left-side").outerHeight() + e), t.find(".quiz-body-part").css("top", e + "px"), t.find(".quiz-body-part").find(".inner-quiz-body-part").css("padding-bottom", t.find(".wqec-footer").height() + 30 + "px")
			}, 200))
		}, 500), cur_pos = $(document).scrollTop(), device.ios() && $("body").addClass("modal-ios")
	})), "quiz_block" == i && (e = e.replace("cquiz_", ""), jQuery.post("/bitrix/js/concept.quiz/component.php", {
		wqecSectionCode: e,
		type: "quiz_block"
	}, function(i) {
		jQuery("div.areaBlockquiz#cquiz_" + e).html(i), wqecPercent(jQuery("div.areaBlockquiz#cquiz_" + e)), jQuery("div.areaBlockquiz#cquiz_" + e).find("form.wqec-form input.cwiz-url").val(decodeURIComponent(location.href))
	})), "open" == i && (jQuery("div.wqec[data-wqec-id='" + e + "']").addClass("wqec-active"), jQuery("body").addClass("wqec-on"), jQuery("div.wqec-shadow").addClass("wqec-active"), cur_pos = $(document).scrollTop(), device.ios() && $("body").addClass("modal-ios"))
}
function QdelPrm(Url,Prm) {
    var a=Url.split('?');
    var re = new RegExp('(\\?|&)'+Prm+'=[^&]+','g');
    Url=('?'+a[1]).replace(re,'');
    Url=Url.replace(/^&|\?/,'');
    var dlm=(Url=='')? '': '?';
    return a[0]+dlm+Url;

};

function popupWindow(e, i, t, a) {
	var c = screen.width / 2 - t / 2,
		n = screen.height / 2 - a / 2;
	return window.open(e, i, "toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width=" + t + ", height=" + a + ", top=" + n + ", left=" + c)
}(function() {
	var e, i, t, a, c, n, s, o;
	window.device = {}, i = window.document.documentElement, o = window.navigator.userAgent.toLowerCase(), device.ios = function() {
		return device.iphone() || device.ipod() || device.ipad()
	}, device.iphone = function() {
		return t("iphone")
	}, device.ipod = function() {
		return t("ipod")
	}, device.ipad = function() {
		return t("ipad")
	}, device.android = function() {
		return t("android")
	}, device.androidPhone = function() {
		return device.android() && t("mobile")
	}, device.androidTablet = function() {
		return device.android() && !t("mobile")
	}, device.blackberry = function() {
		return t("blackberry") || t("bb10") || t("rim")
	}, device.blackberryPhone = function() {
		return device.blackberry() && !t("tablet")
	}, device.blackberryTablet = function() {
		return device.blackberry() && t("tablet")
	}, device.windows = function() {
		return t("windows")
	}, device.windowsPhone = function() {
		return device.windows() && t("phone")
	}, device.windowsTablet = function() {
		return device.windows() && t("touch")
	}, device.fxos = function() {
		return t("(mobile; rv:") || t("(tablet; rv:")
	}, device.fxosPhone = function() {
		return device.fxos() && t("mobile")
	}, device.fxosTablet = function() {
		return device.fxos() && t("tablet")
	}, device.mobile = function() {
		return device.androidPhone() || device.iphone() || device.ipod() || device.windowsPhone() || device.blackberryPhone() || device.fxosPhone()
	}, device.tablet = function() {
		return device.ipad() || device.androidTablet() || device.blackberryTablet() || device.windowsTablet() || device.fxosTablet()
	}, device.portrait = function() {
		return 90 !== Math.abs(window.orientation)
	}, device.landscape = function() {
		return 90 === Math.abs(window.orientation)
	}, t = function(e) {
		return -1 !== o.indexOf(e)
	}, c = function(e) {
		var t;
		return t = new RegExp(e, "i"), i.className.match(t)
	}, e = function(e) {
		return c(e) ? void 0 : i.className += " " + e
	}, s = function(e) {
		return c(e) ? i.className = i.className.replace(e, "") : void 0
	}, device.ios() ? device.ipad() ? e("ios ipad tablet") : device.iphone() ? e("ios iphone mobile") : device.ipod() && e("ios ipod mobile") : device.android() ? device.androidTablet() ? e("android tablet") : e("android mobile") : device.blackberry() ? device.blackberryTablet() ? e("blackberry tablet") : e("blackberry mobile") : device.windows() ? device.windowsTablet() ? e("windows tablet") : device.windowsPhone() ? e("windows mobile") : e("desktop") : device.fxos() ? device.fxosTablet() ? e("fxos tablet") : e("fxos mobile") : e("desktop"), a = function() {
		return device.landscape() ? (s("portrait"), e("landscape")) : (s("landscape"), e("portrait"))
	}, n = "onorientationchange" in window ? "orientationchange" : "resize", window.addEventListener ? window.addEventListener(n, a, !1) : window.attachEvent ? window.attachEvent(n, a) : window[n] = a, a()
}).call(this), cur_pos = 0, jQuery(document).on("click", "form.wqec-form input[type='file']", function() {
	var e = jQuery(this),
		i = e.parent(),
		t = i.find("span.area-file"),
		a = !!(window.File && window.FileReader && window.FileList && window.Blob);
	e.change(function() {
		var c;
		(c = a && e[0].files[0] ? e[0].files[0].name : e.val().replace("C:\\fakepath\\", "")).length && (t.is(":visible") ? (t.text(c), t.removeClass("file-none"), t.closest(".load-file").removeClass("has-error")) : i.text(c))
	}).change()
}), 

jQuery(document).ready(function() {

	var urlquiz = QdelPrm(window.location.href, "quiz");
	cur_pos = $(document).scrollTop();
	var e = window.location.toString(),
		i = /(quiz=)([0-9]*)/.exec(e);
	i && openWiz(i[2], "call"), jQuery("body").append('<input type="hidden" name="wqec-url" value="'+urlquiz+'"><div class="areaForWqec"></div><div class="wqec-shadow"></div><div class="wqec-shadow-menu"></div><div class="wqec-modal" id="wqec-video"><div class="wqec-dialog"><a class="wqec-video-close"></a><div id="wqec-player"></div></div></div><div class="wqec-xLoader"><div class="google-spin-wrapper"><div class="google-spin"></div></div></div>');
	var t = new Clipboard("a.wqec-copy");
	t.on("success", function(e) {
		var i;
		e.clearSelection(), console.log(e), i = e.trigger, $(i).parents(".quiz_copy_parent").find(".quiz_copy_complited").addClass("wqec-active"), setTimeout(function() {
			$(i).parents(".quiz_copy_parent").find(".quiz_copy_complited").removeClass("wqec-active")
		}, 3e3)
	}), t.on("error", function(e) {})
}


), jQuery(document).ready(function() {
	jQuery("div").is(".areaBlockquiz") > 0 && jQuery(".areaBlockquiz").each(function(e) {
		openWiz($(this).attr("id"), "quiz_block")
	})
}), jQuery(document).on("click", ".wqec-share", function() {
	var e = jQuery(this).attr("data-wqec-name"),
		i = window.location.href,
		t = jQuery('input[name~="wqec-url"]').val(),
		a = jQuery(this).parents("div.wqec").attr("data-wqec-id"),
		c = "",
		n = "",
		s = "";
	jQuery(this).parents("div.areaBlockquiz").hasClass("quiz_block") ? t += "#cquiz_" + a : (void 0 !== (c = i.split("?"))[1] ? c[1].length > 0 && (t += "&quiz=" + a) : t += "?quiz=" + a, void 0 !== (n = i.split("#"))[1] && n[1].length > 0 && (t += "#" + n[1])), "vk" == e && (s = "http://vk.com/share.php?url=" + encodeURIComponent(t), void 0 !== jQuery(this).attr("data-wqec-title") && (s += "&title=" + encodeURIComponent(jQuery(this).attr("data-wqec-title"))), void 0 !== jQuery(this).attr("data-wqec-image") && (s += "&image=" + jQuery(this).attr("data-wqec-image"))), "tw" == e && (s = "https://twitter.com/share?url=" + encodeURIComponent(t), void 0 !== jQuery(this).attr("data-wqec-title") && (s += "&title=" + jQuery(this).attr("data-wqec-title"))), "fb" == e && (s = "https://www.facebook.com/sharer/sharer.php?u=" + encodeURIComponent(t), void 0 !== jQuery(this).attr("data-wqec-image") && (s += "&picture=" + jQuery(this).attr("data-wqec-image"))), "ok" == e && (s = "https://connect.ok.ru/offer?url=" + encodeURIComponent(t)), "gp" == e && (s = "https://plus.google.com/share?url=" + encodeURIComponent(t)), popupWindow(s, "", 626, 500)
}), jQuery(document).on("click", ".call-wqec", function() {
	openWiz(jQuery(this).attr("data-wqec-section-id"), "call")
}), jQuery(document).on("click", ".call-wqec-menu", function() {
	openWiz(jQuery(this).attr("data-wqec-section-id"), "call")
}), jQuery(document).on("click", ".open-wqec", function() {
	openWiz(jQuery(this).attr("data-wqec-section-id"), "open")
}), jQuery(window).resize(function() {
	wqecResizeVideo(), wqecResizeAgModal()
});


function cquiz_goals( iblock_id, quiz_id, type, res_id)
{

	$.post( "/bitrix/js/concept.quiz/yacount.php", {
		iblock_id: iblock_id,
		quiz_id: quiz_id,
		type: type,
		res_id: res_id
	}, function( e ) {
		setTimeout( function() {
			jQuery( "body" ).append( e )
		}, 1e3 )
	} )
}


var chooseOn = !1;

function cquiz_step(e, i, c) {
	var n = ".wqec-right-side",
		s = 0;

	if (jQuery(window).width() <= 991 && !e.parents("div.areaBlockquiz").hasClass("quiz_block") && e.parents("div.wqec").hasClass("wizard-quest-edition-concept") && (s = e.parents(".wqec-content-wrap").offset().top - e.parents("div.wqec-right-side-static-part").offset().top, n = ".wizard-quest-edition-concept"), e.hasClass("wqec-first") && cquiz_goals( e.parents("form.wqec-form").find("input[name='cwizIblockId']").val(), e.parents("form.wqec-form").find("input[name='cwizSectionId']").val(), "begin", ""), c.find("div.wqec-vertical-side-inner").hasClass("wqec-tab-num")) c.find("div.wqec-tab").removeClass("wqec-active"), prevVar = parseInt(e.attr("data-step")) - 1, c.find("div.wqec-tab[data-step='" + prevVar + "']").addClass("wqec-complited itwas"), c.find("div.wqec-tab[data-step='" + prevVar + "'].itwas").addClass("wqec-complited"), c.find("div.wqec-tab[data-step='" + e.attr("data-step") + "']").removeClass("wqec-complited"), c.find("div.wqec-tab[data-step='" + e.attr("data-step") + "']").addClass("wqec-active");
	else {
		var th = e;
		var o = c.find("input.wqec-one-percent-step").val(),
			r = c.find("input.totalWqecStepPercent").val();
		o = parseFloat(o), r = parseFloat(r), e.hasClass("wqec-next") && (r += o), e.hasClass("wqec-prev") && (r -= o), r <= 100 && (c.find("input.totalWqecStepPercent").val(r), c.find("div.wqec-tab-per").width(r + "%")), wqecPercent(c)
	}(e.hasClass("stepEnd") || (c.find("span.wqec-per-in-count").removeClass("wqec-active"), c.find("span.wqec-per-in-count[data-step='" + e.attr("data-step") + "']").addClass("wqec-active")), c.find("div.wqec-content-wrap").removeClass("wqec-active"), c.find("div.wqec-content-wrap[data-step='" + e.attr("data-step") + "']").addClass("wqec-active"), e.parents("div.areaBlockquiz").hasClass("quiz_block") || (e.parents("div.wqec").hasClass("wizard-quest-edition-concept2") ? scrollToTop(0, ".wqec", e) : scrollToTop(s, n, e)), e.parents("div.areaBlockquiz").hasClass("quiz_block")) && scrollToTop(c.offset().top - 100, "html:not(:animated),body:not(:animated)", e);
	jQuery("div").is(".wrap-cquiz-cur-result"), e.hasClass("stepEnd") && (c.find("span.wqec-per-count").hide(), c.find("span.wqec-finish").removeClass("wqec-hide"), c.find("div.wqec-tab-result[data-step='stepEnd']").addClass("wqec-active"), setTimeout(function() {
		wqecPercent(c)
	}, 200), jQuery("div.wqec-xLoader").addClass("wqec-active"), c.hasClass("wizard-quest-edition-concept2") && c.hide(), c.find("div.wqec-content-wrap[data-step='stepEnd']").addClass("wqec-active").html(), jQuery.ajax({
		type: "POST",
		url: "/bitrix/js/concept.quiz/result.php",
		data: i.serialize(),
		success: function(e) {
			jQuery("div.wqec-xLoader").removeClass("wqec-active"), c.hasClass("wizard-quest-edition-concept2") && c.show(), c.find("div.wqec-content-wrap[data-step='stepEnd']").html(e), c.find("div.wqec-restart").removeClass("wqec-hide"), setTimeout(function() {
				cquiz_goals( th.parents("form.wqec-form").find("input[name='cwizIblockId']").val(), th.parents("form.wqec-form").find("input[name='cwizSectionId']").val(), "result", i.find("input.cwiz-res-id").val() )
			}, 500), "send_res" == c.find("input.send_res").val() && jQuery.ajax({
				url: "/",
				type: "post",
				success: function(e) {}
			})
		}
	}), c.find("div.wqec-quests").removeClass("wqec-active"))
}

function size_set_main() {
	var e = jQuery(window).height(),
		i = jQuery(".wqec-setting.main-set .wqec-head-wrap").outerHeight() + jQuery(".wqec-setting.main-set .wqec-setting-content").outerHeight() + jQuery(".wqec-setting.main-set .foot-wrap").outerHeight();
	e > i ? jQuery(".wqec-setting.main-set .wqec-inner").css({
		"min-height": e + "px"
	}) : jQuery(".wqec-setting.main-set .wqec-inner").css({
		"min-height": i + "px"
	})
}
jQuery(document).on("click", "form.wqec-calc input.wqec-check", function() {
	chooseOn = !1;
	var e = jQuery(this),
		i = e.parents("form.wqec-calc"),
		t = jQuery(this).parents("div.wqec-content-wrap").find("div.wqec-button-wrap"),
		a = ".wqec-right-side";
	if (jQuery(window).width() <= 991 && !jQuery(this).parents("div.areaBlockquiz").hasClass("quiz_block") && (a = ".wizard-quest-edition-concept"), jQuery(this).parents("div.wqec").hasClass("wizard-quest-edition-concept2")) var c = t.offset().top - jQuery(this).parents(".wizard-quest-edition-concept-inner").offset().top - 5;
	else c = t.offset().top - jQuery(this).parents("div.wqec-right-side-static-part").offset().top;
	if (jQuery(this).parents("div.wqec-type").hasClass("wqec-check")) {
		var n = !1;
		jQuery(this).parents("div.wqec-type").find('input[type="checkbox"]').each(function(e, i) {
			jQuery(this).prop("checked") ? (n = !0, jQuery(this).parents("div.wizard-quest-edition-concept2") && jQuery(this).parents("div.wqec-element").addClass("wqec-active").removeClass("wqec-noactive")) : jQuery(this).parents("div.wizard-quest-edition-concept2") && jQuery(this).parents("div.wqec-element").removeClass("wqec-active").addClass("wqec-noactive")
		}), n ? (t.removeClass("wqec-hide-relative"), chooseOn = !0) : "" != jQuery(this).parents("div.wqec-elements").find("input.wqec-text").val() && void 0 !== jQuery(this).parents("div.wqec-elements").find("input.wqec-text").val() ? (jQuery(this).parents(".wqec-content-wrap").hasClass("one_click_on") || jQuery(this).parents("div.areaBlockquiz").hasClass("quiz_block") || (jQuery(this).parents("div.wqec").hasClass("wizard-quest-edition-concept2") ? scrollToTop(c, ".wqec", jQuery(this)) : scrollToTop(c, a, jQuery(this))), t.removeClass("wqec-hide-relative"), jQuery(this).parents("div.wizard-quest-edition-concept2") && jQuery(this).parents("div.wqec-elements").find("div.wqec-element").removeClass("wqec-active").removeClass("wqec-noactive")) : (t.addClass("wqec-hide-relative"), chooseOn = !1, jQuery(this).parents("div.wizard-quest-edition-concept2") && jQuery(this).parents("div.wqec-elements").find("div.wqec-element").removeClass("wqec-active").removeClass("wqec-noactive"))
	} else jQuery(this).parents("div.wqec-elements").find("div.wqec-element").removeClass("wqec-active").addClass("wqec-noactive"), jQuery(this).parents("div.wqec-element").addClass("wqec-active").removeClass("wqec-noactive"), t.removeClass("wqec-hide-relative"), chooseOn = !0, jQuery(this).parents(".wqec-content-wrap").hasClass("one_click_on") || (jQuery(this).parents("div.areaBlockquiz").hasClass("quiz_block") || (jQuery(this).parents("div.wqec").hasClass("wizard-quest-edition-concept2") ? scrollToTop(c, ".wqec", jQuery(this)) : scrollToTop(c, a, jQuery(this))), jQuery(this).parents("div.areaBlockquiz").hasClass("quiz_block") && jQuery(window).width() <= 991 && !jQuery(this).parents(".wqec-content-wrap").hasClass("one_click_on") && scrollToTop(c = jQuery(this).parents(".wqec-content-wrap").find(".wqec-button-wrap").offset().top - 150, "html:not(:animated),body:not(:animated)", jQuery(this)));
	e.parents(".wqec-admin-on").hasClass("show-points") && jQuery.ajax({
		type: "POST",
		url: "/bitrix/js/concept.quiz/total_count.php",
		data: i.serialize(),
		success: function(i) {
			e.parents("div.wqec").find(".area-for-total").html(i)
		}
	})
}), jQuery(document).on("click", ".wqec-prev-next", function() {
	chooseOn = !1;
	var e = jQuery(this),
		i = jQuery(this).parents("form.wqec-calc"),
		c = jQuery(this).parents("div.wqec.wqec-active");
	e.parents("div.areaBlockquiz").hasClass("quiz_block") && (c = jQuery(this).parents("div.areaBlockquiz")), cquiz_step(e, i, c), e.parents(".wqec-admin-on").hasClass("show-points") && jQuery.ajax({
		type: "POST",
		url: "/bitrix/js/concept.quiz/total_count.php",
		data: i.serialize(),
		success: function(e) {
			c.find(".area-for-total").html(e)
		}
	})
}), jQuery(document).on("change", ".one_click_on label.quiz_wrap_element", function() {
	chooseOn = !1;
	var e = jQuery(this).parents(".one_click_on").find(".wqec-next"),
		i = jQuery(this).parents("form.wqec-calc"),
		c = jQuery(this).parents(".one_click_on").find(".wqec-next").parents("div.wqec.wqec-active");
	e.parents("div.areaBlockquiz").hasClass("quiz_block") && (c = jQuery(this).parents(".one_click_on").find(".wqec-next").parents("div.areaBlockquiz")), cquiz_step(e, i, c), e.parents(".wqec-admin-on").hasClass("show-points") && jQuery.ajax({
		type: "POST",
		url: "/bitrix/js/concept.quiz/total_count.php",
		data: i.serialize(),
		success: function(e) {
			c.find(".area-for-total").html(e)
		}
	})
}), jQuery(document).on("click", "div.wqec-restart", function() {
	var e = jQuery(this).parents("div.wqec");
	e.find(".wqec-complited").removeClass("wqec-complited"), e.find(".wqec-active").removeClass("wqec-active"), e.find("form.wqec-calc").trigger("reset"), e.find("div.wqec-button-wrap").addClass("wqec-hide-relative"), e.find(".wqec-first").addClass("wqec-active"), e.find("li.wqec-first").addClass("wqec-active"), e.find("input.totalWqecStepPercent").val(0), e.find("div.wqec-tab-per").width(0), e.find("div.wqec-quests").addClass("wqec-active"), e.find("div.wqec-element").removeClass("wqec-active").removeClass("wqec-noactive"), e.find("input.wqec-in-focus-anim").parent().removeClass("wqec-in-focus"), jQuery(this).addClass("wqec-hide")
}), 

jQuery(document).on("click", "a.wqec-agree", function() {
	jQuery("div.wqec-modal[data-agreemodal='" + jQuery(this).attr("data-agreemodal") + "']").addClass("wqec-active"), jQuery(this).hasClass("from-air") && (jQuery("div.wqec-modal[data-agreemodal='" + jQuery(this).attr("data-agreemodal") + "']").find("a.wqec-close").addClass("from-air"), jQuery("body").addClass("wqec-on")), jQuery("div.wqec-agree-shadow").addClass("wqec-active"); wqecResizeAgModal();
	jQuery(this).parents('div.block').css("z-index", "9999999");
}), 

jQuery(document).on("click", "a.wqec-close", function() {
	jQuery(this).parents("div.wqec-modal").removeClass("wqec-active"), jQuery("div.wqec-agree-shadow").removeClass("wqec-active"), jQuery(this).hasClass("from-air") && (jQuery(this).removeClass("from-air"), jQuery("body").removeClass("wqec-on"))
	jQuery(this).parents('div.block').css("z-index", "auto");
	
}), 

jQuery(document).on("focus", "form.wqec-form input.input-text", function() {
	jQuery(this).parent().removeClass("has-error")
}), jQuery("input.input-text", "form.wqec-form").focus(function() {
	jQuery(this).parent().removeClass("has-error")
}), jQuery(document).on("click", "form.wqec-form input[type='checkbox']", function() {
	jQuery(this).parents("div.wrap-agree").removeClass("has-error")
}), jQuery(document).on("focus", "input.wqec-in-focus-anim", function() {
	"" == jQuery(this).val() && jQuery(this).parent().addClass("wqec-in-focus")
}), jQuery(document).on("blur", "input.wqec-in-focus-anim", function() {
	"" == jQuery(this).val() && jQuery(this).parent().removeClass("wqec-in-focus")
}), jQuery(document).on("keyup", "input.wqec-text", function() {
	void 0 !== jQuery(this).val() && (jQuery(this).val().length > 0 || 1 == chooseOn ? jQuery(this).parents("div.wqec-content-wrap").find("div.wqec-button-wrap").removeClass("wqec-hide-relative") : jQuery(this).parents("div.wqec-content-wrap").find("div.wqec-button-wrap").addClass("wqec-hide-relative"))
}), jQuery(document).on("click", "form.form input[type='checkbox']", function() {
	jQuery(this).parents("div.wrap-agree").removeClass("has-error")
}), jQuery(document).on("submit", "form.wqec-form", function() {
	var e = jQuery(this);
	var th = jQuery(this);
	e.find("table.wqec-wrap-act").css({
		height: e.find("table.wqec-wrap-act").outerHeight() + "px"
	});
	var i = new FormData(e.get(0));
	i.append("wqec-send", "Y");
	var t = jQuery("input.wqec-name", e),
		a = jQuery("input.wqec-phone", e),
		c = jQuery("input.wqec-email", e),
		n = jQuery("button.wqec-form-submit", e),
		s = jQuery("input#agreecheck", e),
		o = jQuery("div.wqec-questions", e),
		r = jQuery("div.wqec-load", e),
		d = jQuery("div.wqec-thank", e),
		u = jQuery("button[type='submit']", e).attr("data-link"),
		w = jQuery("input[type='file']", e),
		q = 0;
	return void 0 !== s.val() && (s.prop("checked") || (s.parents("div.wrap-agree").addClass("has-error"), q = 1)), void 0 !== w.val() && w.hasClass("require") && w.parents("div.wqec-input").find(".area-file").hasClass("file-none") && (w.parents(".load-file").addClass("has-error"), q = 1), void 0 !== t.val() && t.hasClass("require") && t.val().length < 1 && (t.parent("div.wqec-input").addClass("has-error"), q = 1), void 0 !== c.val() && (c.hasClass("require") || c.val().length > 0) && (/^([a-z0-9_\-]+\.)*[a-z0-9_\-]+@([a-z0-9][a-z0-9\-]*[a-z0-9]\.)+[a-z]{2,4}$/i.test(c.val()) || (c.parent("div.wqec-input").addClass("has-error"), q = 1)), void 0 !== a.val() && a.hasClass("require") && a.val().length <= 0 && (a.parent("div.wqec-input").addClass("has-error"), q = 1), 0 == q && (n.removeClass("wqec-active"), r.addClass("wqec-active"), setTimeout(function() {
		jQuery.ajax({
			url: "/bitrix/js/concept.quiz/form.php",
			type: "post",
			contentType: !1,
			processData: !1,
			data: i,
			dataType: "json",
			success: function(i) {

				"N" == i.OK && (n.addClass("wqec-active"), r.removeClass("wqec-active")), "Y" == i.OK && (o.removeClass("wqec-active"), d.addClass("wqec-active"), jQuery.ajax({
					url: "/",
					type: "post",
					success: function(e) {}
				}), void 0 !== u && u.length > 0 && setTimeout(function() {
					location.href = u
				}, 2e3), setTimeout(function() {					
					cquiz_goals( th.find("input[name='cwizIblockId']").val(), th.find("input[name='cwizSectionId']").val(), "send", th.find("input.cwiz-res-id").val() )
				}, 500))
			}
		})
	}, 1e3)), !1
}), jQuery(document).on("click", "a.wqec-link-video", function() {
	btn = jQuery(this), jQuery("div#wqec-video").addClass("wqec-active"), jQuery("div.wqec-xLoader").addClass("wqec-active"), jQuery("div.wqec-shadow").addClass("wqec-double"), jQuery.post("/bitrix/js/concept.quiz/videoIframe.php", {
		wqecVideoCode: jQuery(this).attr("data-wqec-video")
	}, function(e) {
		jQuery("div#wqec-player").html(e), jQuery("div.wqec-xLoader").removeClass("wqec-active"), btn.parents("div.areaBlockquiz").hasClass("quiz_block") && (jQuery("div.wqec-shadow").addClass("wqec-block-quiz-on"), jQuery("body").addClass("wqec-block-quiz-on")), setTimeout(function() {
			wqecResizeVideo()
		}, 500)
	})
}), jQuery(document).on("click", "a.wqec-mainclose", function() {
	jQuery(this).parents("div.wqec-big-wrap").find('.wqec-content-wrap[data-step="stepEnd"]').hasClass("wqec-active") && (jQuery('.open-wqec[data-wqec-section-id="' + jQuery(this).parents("div.wqec").attr("data-wqec-id") + '"]').removeClass("open-wqec").addClass("call-wqec"), jQuery(this).parents("div.wqec-big-wrap").remove()), jQuery(this).parents("div.wqec").removeClass("wqec-active"), jQuery("body").removeClass("wqec-on"), jQuery("div.wqec-shadow").removeClass("wqec-active"), device.ios() && (window.scrollTo(0, cur_pos), $("body").removeClass("modal-ios"))
}), jQuery(document).on("click", "a.wqec-video-close", function() {
	jQuery("div.wqec-shadow").removeClass("wqec-double"), jQuery("div#wqec-video").removeClass("wqec-active"), jQuery("div#wqec-video").find("iframe").remove(), jQuery("div.wqec-shadow.wqec-block-quiz-on").removeClass("wqec-block-quiz-on"), jQuery("body").removeClass("wqec-block-quiz-on")
}), jQuery(document).on("click", "a.wqec-show-inputs", function() {
	var e = jQuery(this).parents("form.wqec-form"),
		i = jQuery("input.wqec-name", e),
		t = jQuery("input.wqec-phone", e),
		a = jQuery("input.wqec-email", e),
		c = 0;
	void 0 !== i.val() && i.hasClass("require") && !i.parent().hasClass("wqec-hide") && i.val().length < 1 && (i.parent("div.wqec-input").addClass("has-error"), c = 1), void 0 !== a.val() && !a.parent().hasClass("wqec-hide") && (a.hasClass("require") || a.val().length > 0) && (/^([a-z0-9_\-]+\.)*[a-z0-9_\-]+@([a-z0-9][a-z0-9\-]*[a-z0-9]\.)+[a-z]{2,4}$/i.test(a.val()) || (a.parent("div.wqec-input").addClass("has-error"), c = 1)), void 0 !== t.val() && t.hasClass("require") && !t.parent().hasClass("wqec-hide") && t.val().length <= 0 && (t.parent("div.wqec-input").addClass("has-error"), c = 1), 0 == c && (jQuery(this).parent().hide(), jQuery(this).parents("form.wqec-form").find("div.wqec-input.wqec-hide").removeClass("wqec-hide"), jQuery(this).parents("form.wqec-form").find("div.wrap-btn-submit.wqec-hide").removeClass("wqec-hide"))
}), jQuery(document).ready(function() {
	size_set_main()
}), jQuery(window).resize(function() {
	size_set_main()
}), jQuery(document).on("click", "div.wqec-btn", function() {
	jQuery("div.wqec-setting.main-set").addClass("wqec-open"), size_set_main(), setTimeout(function() {
		jQuery("div.wqec-setting.main-set").addClass("wqec-on")
	}, 100), jQuery("body").addClass("wqec-on-menu"), jQuery("div.wqec-shadow-menu").addClass("wqec-active")
}), jQuery(document).on("click", "a[data-button='main-close-list']", function() {
	var e = jQuery(this);
	e.closest(".wqec-setting").removeClass("wqec-on"), jQuery("div.wqec-shadow-menu").removeClass("wqec-active"), jQuery("body").removeClass("wqec-on-menu"), setTimeout(function() {
		e.closest(".wqec-setting").removeClass("wqec-open")
	}, 1e3)
}), jQuery(document).on("click", ".list-name", function() {
	var e = jQuery(this).parents(".wqec-parent");
	e.hasClass("wqec-active") ? jQuery("div.wqec-options-wrap", e).slideUp(200, function() {
		e.removeClass("wqec-active")
	}) : (jQuery(this).parents(".wqec-list").find("li").removeClass("wqec-active"), jQuery("div.wqec-options-wrap").slideUp(200), jQuery("div.wqec-options-wrap", e).slideDown(200, function() {
		e.addClass("wqec-active")
	}))
}), jQuery(document).on("click", ".wqec-choose-list", function() {
	jQuery(this).parents(".wqec-select-wrap").addClass("wqec-open")
}), jQuery(document).on("click", ".wqec-ar-down", function() {
	jQuery(this).parents(".wqec-select-wrap").addClass("wqec-open")
}), jQuery(document).on("click", ".wqec-select-wrap .wqec-name", function() {
	jQuery(this).parents(".wqec-select-wrap").find(".wqec-choose-list").removeClass("wqec-first").addClass("bold").text(jQuery(this).text()), jQuery(this).parents(".wqec-select-wrap").removeClass("wqec-open")
});