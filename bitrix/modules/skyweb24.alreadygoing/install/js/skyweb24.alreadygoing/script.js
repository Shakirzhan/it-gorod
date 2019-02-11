var cchecker={
	cookieName: "alreadygoing",
	createCookie: function (name, value, days) {
		if (days) {
			var date = new Date();
			date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
			var expires = "; expires=" + date.toGMTString();
		}
		else var expires = "";
		document.cookie = name + "=" + value + expires + "; path=/";
	},
	readCookie: function (name) {
		var nameEQ = name + "=";
		var ca = document.cookie.split(';');
		for (var i = 0; i < ca.length; i++) {
			var c = ca[i];
			while (c.charAt(0) == ' ') c = c.substring(1, c.length);
			if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
		}
		return null;
	},
	checkSubmitted: function () {
		return this.readCookie(this.cookieName) == "1";
	},
	trackSubmit: function (days) {
		this.createCookie(this.cookieName, "1", days);
	}
};

$(window).load(function(){
	$(document).mouseout(function(e){
		if(!cchecker.checkSubmitted() && e.pageY - $(document).scrollTop() <= 5 && window.alreadyGoingObj!='undefined'){
			var content=''+
			'<div class="alreadygoing '+alreadyGoingObj.skin+' '+alreadyGoingObj.skin_color+'">'+
				'<img id="img_going" src="'+alreadyGoingObj.img_path+'" alt="'+alreadyGoingObj.link_name+'">'+
				'<div class="text">'+
					'<h2>'+alreadyGoingObj.header+'</h2>'+
					'<div class="info">'+alreadyGoingObj.content+'</div>'+
					'<h3>'+alreadyGoingObj.header_ext+'</h3>'+
					'<a href="'+alreadyGoingObj.link_value+'" class="going_link">'+alreadyGoingObj.link_name+'</a>'+
				'</div>'+
			'</div>';
			$.fancybox({'content':content, padding:'0', beforeClose:function(){send_stat('closebanner');}});
			cchecker.trackSubmit(alreadyGoingObj.cookie);
			send_stat('showbanner');
		}
	});
	
	$(document).on('click', 'a.going_link', function(e){
		send_stat('gotolink');
	});
});

function send_stat(typeEvent){
	$.ajax({
		url: '.',
		type: 'POST',
		dataType: "html",
		data:{STAT_SW24_AG:'stat_'+typeEvent},
		success: function(data){
			console.log(data);
			console.log('success');
		},
		error: function(xr, status, error) {
			console.log(xr);
			console.log('error');
		},
	});
}