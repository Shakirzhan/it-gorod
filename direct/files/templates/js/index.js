 if (!CONFIG)
	var CONFIG={'http_dir': ''};
//Запоминаем скроллинг//
if (document.getElementById('scrolled')) {
	document.getElementById('scrolled').addEventListener("scroll", function() {
	  lib.setCookie('framework_scrolltop', document.getElementById('scrolled').scrollTop);
	});
	document.getElementById('scrolled').scrollTop=lib.getCookie('framework_scrolltop');
//lib.deleteCookie('framework_scrolltop');
}
//\Запоминаем скроллинг//

 $(document).ready(function () {
	 
	//Управление видимостью пользовательских параметров//
	$('.strategy_param_show').click(function () {
		if ($('#'+$(this).attr('id')+'_block').css('display')=='none') {
			$(this).html('-3');
			$('#'+$(this).attr('id')+'_block').show();
		} else {
			$(this).html('+3');
			$('#'+$(this).attr('id')+'_block').hide();
		}
	});
	//\Управление видимостью пользовательских параметров//
	
	//Массовое назначение стратегий//
	$('#strategy_button').click(function () {
		$(".strategy_id option[value='"+$('#strategy_id :selected').val()+"']").attr("selected", "selected");
		$('.strategy_percent').val($('#strategy_percent').val());
		$('.strategy_add').val($('#strategy_add').val());
		$('.strategy_maximum').val($('#strategy_maximum').val());
		$('.strategy_fixed').val($('#strategy_fixed').val());
		$('.strategy_budget').val($('#strategy_budget').val());
		
		if ($('#strategy_type').parent().attr('aria-checked')=='true') 
			$('.strategy_type').attr("checked", "checked").parent().addClass('checked').attr('aria-checked', 'true');
		else
			$('.strategy_type').removeAttr("checked").parent().removeClass('checked').attr('aria-checked', 'false');
		
		if ($('#context').parent().attr('aria-checked')=='true') 
			$('.context').attr("checked", "checked").parent().addClass('checked').attr('aria-checked', 'true');
		else
			$('.context').removeAttr("checked").parent().removeClass('checked').attr('aria-checked', 'false');
		
		$('.context_percent').val($('#context_percent').val());
		$('.context_maximum').val($('#context_maximum').val());
		$('.context_fixed').val($('#context_fixed').val());
		$('.context_minimum').val($('#context_minimum').val());
		
		if ($('#position').parent().attr('aria-checked')=='true') 
			$('.position').attr("checked", "checked").parent().addClass('checked').attr('aria-checked', 'true');
		else
			$('.position').removeAttr("checked").parent().removeClass('checked').attr('aria-checked', 'false');
	});
	//\Массовое назначение стратегий//
	
	//Задания//
	$('.cron_account, .cron_user').change(function () {
		
		Form=$(this).parent().parent().parent();
		Element=this;
		$.ajax({
			   type: "POST",
			   url: CONFIG.http_dir+"/direct/index/cron_edit_company/",
			   data: $(this).parent().parent().parent().serializeArray(),
			   dataType: "json",
			   async: false,
			   success: function(DATA){
					
					if (DATA.DATA.CRON.USER.ELEMENT) {
						if (Element!=$('.cron_user').get(0)) {
							Form.find('.cron_user :not(:first)').remove();
							$.each(DATA.DATA.CRON.USER.ELEMENT, function(key, value) {
								Form.find('.cron_user').append($('<option value="'+value.id+'">'+value.login+' - '+value.name+'</option>'));
							});
						}
					} else
						Form.find('.cron_user :not(:first)').remove();
					
					if (DATA.DATA.CRON.COMPANY.ELEMENT) {
						Form.find('.cron_company :not(:first)').remove();
						$.each(DATA.DATA.CRON.COMPANY.ELEMENT, function(key, value) {
							Form.find('.cron_company').append($('<option value="'+value.id+'">'+value.id+' - '+value.name+' ('+value.count+')</option>'));
						});
					}
			   },
			   error: function(e){
					alert('Неизвестная ошибка');
			   }
			 });
	});
	//\Задания//
	
	//Фильтры в статистике//
	$('.filter_user').change(function () {
		
		Form=$(this).parent().parent().parent();
		$.ajax({
			   type: "POST",
			   url: CONFIG.http_dir+"/direct/statistic/filters/",
			   data: $(this).parent().parent().parent().serializeArray(),
			   dataType: "json",
			   async: false,
			   success: function(DATA){
					
					if (DATA.DATA.FILTER.COMPANY.ELEMENT) {
						Form.find('.filter_company :not(:first)').remove();
						Form.find('.filter_group :not(:first)').remove();
						Form.find('.filter_phrase :not(:first)').remove();
						$.each(DATA.DATA.FILTER.COMPANY.ELEMENT, function(key, value) {
							Form.find('.filter_company').append($('<option value="'+value.id+'">'+value.id+' - '+value.name+'</option>'));
						});
					}
			   },
			   error: function(e){
					alert('Неизвестная ошибка');
			   }
			 });
	});
	
	$('.filter_company').change(function () {
		
		Form=$(this).parent().parent().parent();
		$.ajax({
			   type: "POST",
			   url: CONFIG.http_dir+"/direct/statistic/filters/",
			   data: $(this).parent().parent().parent().serializeArray(),
			   dataType: "json",
			   async: false,
			   success: function(DATA){
					
					if (DATA.DATA.FILTER.GROUP.ELEMENT) {
						Form.find('.filter_group :not(:first)').remove();
						Form.find('.filter_phrase :not(:first)').remove();
						$.each(DATA.DATA.FILTER.GROUP.ELEMENT, function(key, value) {
							Form.find('.filter_group').append($('<option value="'+value.id+'">'+value.id+' - '+value.name+'</option>'));
						});
					}
			   },
			   error: function(e){
					alert('Неизвестная ошибка');
			   }
			 });
	});
	
	$('.filter_group').change(function () {
		
		Form=$(this).parent().parent().parent();
		$.ajax({
			   type: "POST",
			   url: CONFIG.http_dir+"/direct/statistic/filters/",
			   data: $(this).parent().parent().parent().serializeArray(),
			   dataType: "json",
			   async: false,
			   success: function(DATA){
					
					if (DATA.DATA.FILTER.PHRASE.ELEMENT) {
						Form.find('.filter_phrase :not(:first)').remove();
						$.each(DATA.DATA.FILTER.PHRASE.ELEMENT, function(key, value) {
							Form.find('.filter_phrase').append($('<option value="'+value.id+'">'+value.id+' - '+value.name+'</option>'));
						});
					}
			   },
			   error: function(e){
					alert('Неизвестная ошибка');
			   }
			 });
	});
	//\Фильтры в статистике//
	
	//Посылка Ajax формы логина//
	$('form.login').submit(function() {
		var Form=this;
		if ($(this).find("input[name='login']").val() && $(this).find("input[name='password']").val()) {
			
			 $.ajax({
			   type: "POST",
			   url: CONFIG.http_dir+"/user/controller/authorize/",
			   data: $(this).serializeArray(),
			   dataType: "json",
			   async: false,
			   success: function(message){
					
					if (message && message.debug)
						lib.log(message.debug);
					if (message && message.status && message.status==1) {
						location.href=lib.url().uri;
					} else {
						$(Form).find("input[name='login']").css('border-color', 'red');
						$(Form).find("input[name='password']").css('border-color', 'red');
						//$('#captcha', Form).attr('src', '/user/controller/captcha/?'+$('#session', Form).attr('name')+'='+$('#session', Form).val()+'&sid=' + Math.random());
					}
			   },
			   error: function(e){
					alert('Неизвестная ошибка');
			   }
			 });
		 } else {
			$(Form).find("input[name='login']").css('border-color', 'red');
			$(Form).find("input[name='password']").css('border-color', 'red');
		 }
	return false;
	});//\submit
	//\Посылка Ajax формы логина//
	
	//Посылка Ajax формы регистрации//
	$('form.register .require').bind('keyup change', function() {
		Form=$('form.register');
		require(Form, this);
	});
	
	$('form.register').submit(function() {
		var Form=this;
		//$(this).get(0).tagName.toLowerCase()
		error=false;
		$(this).find(".require").each(function (index) {
			if (require(Form, this)) 
				error=true;
		});	
		if (!error)
			$.ajax({
			   type: "POST",
			   url: CONFIG.http_dir+"/user/controller/reg/",
			   data: $(this).serializeArray(),
			   dataType: "json",
			   async: false,
			   success: function(message){
					DATA=message.DATA;
					
					if (DATA && DATA.debug)
						lib.log(DATA.debug);
					if (DATA && DATA.status && DATA.status==1) {
						location.href=CONFIG.http_dir+'/profile';
					} else if (DATA && DATA.ERRORS) {
						html='<font color="red">';
						for( value in DATA.ERRORS) {
							$(Form).find('.error.'+value).html(DATA.ERRORS[value]).show();
						}

						//$('#captcha', Form).attr('src', '/user/controller/captcha/?'+$('#session', Form).attr('name')+'='+$('#session', Form).val()+'&sid=' + Math.random());
						
					} else {
						alert('Неизвестная ошибка');
					}
			   },
			   error: function(e){	
					alert('Истекло время ожидания ответа страницы');
			   }
			 });
		
		return false;
	});//\submit
	
	function require(Form, Object) {
		var error=false;
		if (($(Object).is('input') && $(Object).attr('type')!='checkbox' && $(Object).attr('type')!='radio') || $(Object).is('textarea')) {
			if (!$(Object).val()) {
				error=true;
				$(Form).find('.error.'+$(Object).attr('name')).show();
			} else
				$(Form).find('.error.'+$(Object).attr('name')).hide();
				
		} else if ( $(Object).is('input') && ($(Object).attr('type')=='checkbox' || $(Object).attr('type')=='radio')) {
			error=true;
			$(Form).find("input[name='"+$(Object).attr('name')+"']").each(function (index) {
				if ($(this).prop('checked')) {
					error=false;
					return;
				}
			});
			if (error) 
				$(Form).find('.error.'+$(Object).attr('id')).show();
			else
				$(Form).find('.error.'+$(Object).attr('id')).hide();
		} else {
			if (!$(Object).val()) {
				error=true;
				$(Form).find('.error.'+$(Object).attr('id')).show();
			} else
				$(Form).find('.error.'+$(Object).attr('id')).hide();
		}
		return error;
	}
	
	//\Посылка Ajax формы регистрации//
	
	//Сохранение формы Ajax//
	$('form.save').submit(function() {
		var Form=this;
			 $.ajax({
			   type: "POST",
			   url: CONFIG.http_dir+$(this).attr('action'),
			   data: $(this).serializeArray(),
			   dataType: "json",
			   async: false,
			   success: function(DATA){
					if (DATA.DATA) 
						DATA=DATA.DATA;
					else
						DATA=false;
					
					if (DATA && DATA.ERROR)
						lib.log(DATA.ERROR);
					if (DATA && DATA.status) {
						if ($(Form).attr('title')) {
							$('#save-modal .md-title').html('Сохранение');
							message=$(Form).attr('title');
						}
						else if (DATA.element&&$(Form).find("input, select").length!=DATA.element) {
							$('#save-modal .md-title').html('Ошибка');
							message='Не все записи сохранены';
						} else {
							$('#save-modal .md-title').html('Сохранение');
							message='Сохранено'+(DATA.count?' ('+DATA.count+' шт.)':'');
						}
						
						if (DATA.id) {
							$(Form).find("input[name='id']").val(DATA.id);							
							if ($(".masked").length>0) {
								$(".masked").parent().html($(".masked").parent().html().replace(new RegExp("%id%", "g"), DATA.id));
								//history.pushState(null, null, location.pathname.replace(new RegExp("\/$", "g"), '')+'/id/'+DATA.id);
								$(".masked").show();
							}
						}
						$('#save-modal').modal().addClass('md-show');
						$('#save-modal .md-message').html(message);
						$('#save-modal .md-close').click(function () {$('#save-modal').modal('hide').removeClass('md-show');});
						//alert(message);
					} else {
						message='Ошибка';
						if (DATA && DATA.ERROR) 
							message=lib.print_r(DATA.ERROR);
						if (DATA.id) 
							$(Form).find("input[name='id']").val(DATA.id);
							
						$('#save-modal').modal().addClass('md-show');
						$('#save-modal .md-title').html('Ошибка');
						$('#save-modal .md-message').html(message);
						$('#save-modal .md-close').click(function () {$('#save-modal').modal('hide').removeClass('md-show');});
						//alert('Ошибка\n'+lib.print_r(DATA));						
					}
			   },
			   error: function(e){
					alert('Неизвестная ошибка '+lib.strip_tags(e.responseText));
			   }
			 });
	return false;
	});//\submit
	//\Сохранение формы Ajax//
		
		//Профайл//
	$('form.profile_save .button').click(function() {
		var Form=$('form.profile_save');
		var Button=this;
			
			 $.ajax({
			   type: "POST",
			   url: CONFIG.http_dir+"/user/controller/profile_save/",
			   data: $(Form).serializeArray(),
			   dataType: "json",
			   async: false,
			   success: function(message){
					
					if (message && message.DATA.status && message.DATA.status==1) {
						$(Form).find('.success').html('Данные сохранены');
						$(Button).html('Сохранено');
						$(Button).addClass('hover1');
						$(Form).find('.success').show();
					} else if (message && message.DATA.ERRORS) {
						html='<font color="red">';
						for( value in message.DATA.ERRORS) 
							html+=''+message.DATA.ERRORS[value]+'<br>';
						html+='</font>';
						$(Form).find('.success').html(html);
						$(Form).find('.success').show();
						
					} else {
						$(Form).find('.success').html('<font color="red">Неизвестная ошибка</font>');
						$(Form).find('.success').show();
					}
			   },
			   error: function(e){
					
					
					alert('Истекло время ожидания ответа страницы.');
			   }
			 });
	return false;
	});//\submit
	//\Профайл//
	
	//Платежные реквизиты//
	$('form.bank .button').click(function() {
		var Form=$('form.bank');
		var Button=this;
			
			 $.ajax({
			   type: "POST",
			   url: CONFIG.http_dir+"/user/controller/bank_save/",
			   data: $(Form).serializeArray(),
			   dataType: "json",
			   async: false,
			   success: function(message){
					
					if (message && message.DATA.status && message.DATA.status==1) {
						$(Form).find('.success').html('Данные сохранены');
						$(Button).html('Сохранено');
						$(Button).addClass('hover1');
						$(Form).find('.success').show();
					} else if (message && message.DATA.ERRORS) {
						html='<font color="red">';
						for( value in message.DATA.ERRORS) 
							html+=''+message.DATA.ERRORS[value]+'<br>';
						html+='</font>';
						$(Form).find('.success').html(html);
						$(Form).find('.success').show();
						
					} else {
						$(Form).find('.success').html('<font color="red">Неизвестная ошибка</font>');
						$(Form).find('.success').show();
					}
			   },
			   error: function(e){
					
					
					alert('Истекло время ожидания ответа страницы.');
			   }
			 });
	return false;
	});//\submit
	//\Платежные реквизиты//
	
	//Контакты//
	$('form.contact').submit(function() {
		var Form=this;
		var Button=$('form.contact #submit');
		if ($(this).find("input[name='name']").val() &&  
		$(this).find("input[name='email']").val() && 
		$(this).find("input[name='phone']").val() && 
		$(this).find("textarea[name='body']").val()) {	
			
			 $.ajax({
			   type: "POST",
			   url: CONFIG.http_dir+"/user/mail/contact/",
			   data: $(Form).serializeArray(),
			   dataType: "json",
			   async: false,
			   success: function(message){
					
					if (message && message.DATA.status && message.DATA.status==1) {
						$(Form).find('.success').html('Ваше письмо отправлено');
						$(Button).html('Отправлено');
						$(Button).addClass('hover1');
						$(Form).find('.success').show();
					} else if (message && message.DATA.ERRORS) {
						html='<font color="red">';
						for( value in message.DATA.ERRORS) 
							html+=''+message.DATA.ERRORS[value]+'<br>';
						html+='</font>';
						$(Form).find('.success').html(html);
						$(Form).find('.success').show();
						
					} else {
						$(Form).find('.success').html('<font color="red">Неизвестная ошибка</font>');
						$(Form).find('.success').show();
					}
			   },
			   error: function(e){
					
					
					alert('Истекло время ожидания ответа страницы.');
			   }
			 });
		}
	return false;
	});//\submit
	//\Контакты//
	
	//Отзывы//
	$('form.otzivy').submit(function() {
		var Form=this;
		var Button=$('form.otzivy #submit');
		if ($(this).find("input[name='name']").val() &&  
		$(this).find("textarea[name='body']").val()) {	
			
			 $.ajax({
			   type: "POST",
			   url: CONFIG.http_dir+"/user/mail/otzivy/",
			   data: $(Form).serializeArray(),
			   dataType: "json",
			   async: false,
			   success: function(message){
					
					if (message && message.DATA.status && message.DATA.status==1) {
						$(Form).find('.success').html('Ваш отзыв отправлен и появится после проверки модератором');
						$(Button).html('Отправлено');
						$(Button).addClass('hover1');
						$(Form).find('.success').show();
					} else if (message && message.DATA.ERRORS) {
						html='<font color="red">';
						for( value in message.DATA.ERRORS) 
							html+=''+message.DATA.ERRORS[value]+'<br>';
						html+='</font>';
						$(Form).find('.success').html(html);
						$(Form).find('.success').show();
						
					} else {
						$(Form).find('.success').html('<font color="red">Неизвестная ошибка</font>');
						$(Form).find('.success').show();
					}
			   },
			   error: function(e){
					
					
					alert('Истекло время ожидания ответа страницы.');
			   }
			 });
		}
	return false;
	});//\submit
	//\Отзывы//
	
	//Вывод средств//
	$('form.minus .button').click(function() {
		var Form=$('form.minus');
		var Button=this;
			
			 $.ajax({
			   type: "POST",
			   url: CONFIG.http_dir+"/user/controller/minus_save/",
			   data: $(Form).serializeArray(),
			   dataType: "json",
			   async: false,
			   success: function(message){
					
					if (message && message.DATA.status && message.DATA.status==1) {
						$(Form).find('.success').html('Запрос на вывод средств отправлен');
						$(Button).html('Выведено');
						$(Button).addClass('hover1');
						$(Form).find('.success').show();
					} else if (message && message.DATA.ERRORS) {
						html='<font color="red">';
						for( value in message.DATA.ERRORS) 
							html+=''+message.DATA.ERRORS[value]+'<br>';
						html+='</font>';
						$(Form).find('.success').html(html);
						$(Form).find('.success').show();
						
					} else {
						$(Form).find('.success').html('<font color="red">Неизвестная ошибка</font>');
						$(Form).find('.success').show();
					}
			   },
			   error: function(e){
					
					
					alert('Истекло время ожидания ответа страницы.');
			   }
			 });
	return false;
	});//\submit
	//\Вывод средств//
	
	//Напоминание пароля//
	$('form.remember .button').click(function() {
		var Form=$('form.remember');
		var Button=this;
			
			 $.ajax({
			   type: "POST",
			   url: CONFIG.http_dir+"/user/controller/remember_mail/",
			   data: $(Form).serializeArray(),
			   dataType: "json",
			   async: false,
			   success: function(message){
					
					if (message && message.DATA.status && message.DATA.status==1) {
						$(Form).find('.success').html('Пароль выслан Вам на Email');
						$(Button).html('Отправлено');
						$(Button).addClass('hover1');
						$(Form).find('.success').show();
					} else if (message && message.DATA.ERRORS) {
						html='<font color="red">';
						for( value in message.DATA.ERRORS) 
							html+=''+message.DATA.ERRORS[value]+'<br>';
						html+='</font>';
						$(Form).find('.success').html(html);
						$(Form).find('.success').show();
						$('#captcha', Form).attr('src', '/user/controller/captcha/?'+$('#session', Form).attr('name')+'='+$('#session', Form).val()+'&sid=' + Math.random());
					} else {
						$(Form).find('.success').html('<font color="red">Неизвестная ошибка</font>');
						$(Form).find('.success').show();
						$('#captcha', Form).attr('src', '/user/controller/captcha/?'+$('#session', Form).attr('name')+'='+$('#session', Form).val()+'&sid=' + Math.random());
					}
			   },
			   error: function(e){
					
					
					alert('Истекло время ожидания ответа страницы.');
			   }
			 });
	return false;
	});//\submit
	//\Напоминание пароля//	
 });//\ready

