////////////////////////////////////////////////////////////
/// Name: Класс lib - библиотека методов javascript      ///
/// Version: 1.0                                         ///
/// Author: Кононенко Станислав Александрович            ///
/// Email: info@direct-automate.ru                       ///
/// Url: http://direct-automate.ru                       ///
/// Requirements: JavaScript >=1.8                       ///
/// Charset: UTF-8                                       ///
////////////////////////////////////////////////////////////
function lib () {
	
	//Метод подсчета количества элементов массива или объекта//
	this.count=function (ARRAY) {
		if (typeof ARRAY != 'object')
			return 1;
		var count=0;
		for (index in ARRAY) {
			if (typeof ARRAY[index] == 'object')
				count=count+this.count(ARRAY[index]);
			else
				count++;
		}
		return count;
	}//\function
	//\Метод подсчета количества элементов массива или объекта//	
	
	//Метод размножения строки//
	this.duplicate=function (str, number) {
		if (!number)
			number=1;
		var string='';
		if ((typeof str != 'string' && typeof str != 'number') || !number>0)
			return string;
		for (var i=0; i<number; i++)
			string=string+str;
		return string;
	}//\function
	//\Метод размножения строки//
	
	//Метод распечатки объекта или массива//
	this.print_r=function (Object, type, level) {
		if (!type)
			type=false;
		if (!level)
			level=0;
		var string='';
		if (typeof Object != 'object')
			return Object+(type?' ('+typeof(Object)+')':'');
		
		for (index in Object) {//alert(index+'='+Object[index]+ '=' +typeof(index)+typeof(Object[index]));
			if (typeof Object[index] == 'object')
				string=string+index+(type?' ('+typeof(Object[index])+')':'')+": \r\n"+this.print_r(Object[index], type, level+1);
			else
				string=string+this.duplicate("\t", level)+index+(type?' ('+typeof(Object[index])+')':'')+': '+Object[index]+"\r\n";
		}
		return string;
	}//\function
	//\Метод распечатки объекта или массива//	
	
	//Метод логирования в консоль//
	this.log=function (string) {
		if (typeof string == 'object')
			string=this.print_r(string);
		if (string)
			console.log(string);
		return true;
	}
	//\Метод логирования в консоль//
	
	//Метод нахождения позиции подстроки не чувствительный к регистру//
	this.strpos=function (string, find, offset, register) {
		if (!string || !find)
			return -1;
		if (!offset)
			offset=0;
		if (!register) {
			string=string.toLowerCase();
			find=find.toLowerCase();
		}
		return string.indexOf(find, offset);
	}
	//\Метод нахождения позиции подстроки не чувствительный к регистру//
	
	//Метод конкатенации элементов массива в строку используя заданный разделитель//
	this.implode=function (string, ARRAY) {	
		return ARRAY.join( string );
	}
	//\Метод конкатенации элементов массива в строку используя заданный разделитель//
	
	//Метод разбиения строки на массив используя заданный разделитель//
	this.explode=function (find, string) {
		if (!find || !string)
			return null;
		return string.split(find);
		
	}
	//\Метод разбиения строки на массив используя заданный разделитель//
	
	//Метод проверки определена ли переменная//
	this.isset=function (mixed) {
		if (typeof (mixed)=='undefined')
			return false;
		return true;
	}
	//\Метод проверки определена ли переменная//
	
	//Метод проверки определена ли переменная//
	this.empty=function (mixed) {
		if (typeof (mixed)=='undefined')
			return true;
		else if (typeof (mixed.length)=='undefined')
			return true;
		else if (mixed.length==0)
			return true;
		return false;
	}
	//\Метод проверки определена ли переменная//
	
	//Метод получение информации из URL//
	this.url=function (url) {
		if (!url)
			url=location.href;
		var VARS=null;
		var query=null;
		var hash=null;
		var URL=url.split('/');
		var str='';
		for(var i=3; i<URL.length; i++) {
			str+='/'+URL[i];
		}
		var uri='';
		if (str)
			uri=str;
		var PATH=str.split('?');
		var path=PATH[0];
		if (!PATH[1]) {
			HASH=path.split('#');
			path=HASH[0];
			if (HASH[1])
				hash=HASH[1];
		}
		if (PATH[1]) {
			var QUERY=PATH[1].split('#');
			query=QUERY[0];
			if (QUERY[1])
				hash=QUERY[1];
			var VAR=query.split('&');
			VARS=new Array();
			for(i=0; i<VAR.length; i++) {
				VARI=VAR[i].split('=');
				if (VARI[1])
					VARS[VARI[0]]=VARI[1];
				else
					VARS[VARI[0]]=null;
			}
		}
		return {'host': URL[2], 'path': path, 'uri': uri, 'query': query, 'hash': hash, 'GET': VARS}
	}
	//\Метод получение информации из URL//
	
	//Метод удаляет HTML из строки//
	this.strip_tags=function (string) {
		string=string.replace(new RegExp("<br ?/?>", "i"), '\n'); 
		string=string.replace(new RegExp('<script[^>]*?>.*?</script>', "gi"), ''); 
		string=string.replace(new RegExp('<[\/\!]*?[^<>]*?>', "gi"), ''); 
		return string;
	}
	//\Метод удаляет HTML из строки//
	
	//Метод работы с объектами как с ассоциативными массивами//
	this.object=function (Object) {
		if (!Object)
			Object={};
			var Element=Object;
		for (var i=1; i<arguments.length; i++) {
			if (!Element[arguments[i]]) {
				Element[arguments[i]]={};
			}
			Element=Element[arguments[i]];
		}
		return Object;
	}
	//\Метод работы с объектами как с ассоциативными массивами//
	
	//Работа с Куки//
	// возвращает cookie с именем name, если есть, если нет, то undefined
	this.getCookie=function (name) {
	  var matches = document.cookie.match(new RegExp(
		"(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
	  ));
	  return matches ? decodeURIComponent(matches[1]) : undefined;
	}
	
	/*
	name
	название cookie

	value
	значение cookie (строка)
	
	expires
	Время истечения cookie. Интерпретируется по-разному, в зависимости от типа:

	Число – количество секунд до истечения. Например, expires: 3600 – кука на час.
	Объект типа Date – дата истечения.
	Если expires в прошлом, то cookie будет удалено.
	Если expires отсутствует или 0, то cookie будет установлено как сессионное и исчезнет при закрытии браузера.
	path
	Путь для cookie.

	domain
	Домен для cookie.

	secure
	Если true, то пересылать cookie только по защищенному соединению.
	*/
	this.setCookie=function (name, value, options) {
	  options = options || {};

	  var expires = options.expires;

	  if (typeof expires == "number" && expires) {
		var d = new Date();
		d.setTime(d.getTime() + expires * 1000);
		expires = options.expires = d;
	  }
	  if (expires && expires.toUTCString) {
		options.expires = expires.toUTCString();
	  }

	  value = encodeURIComponent(value);

	  var updatedCookie = name + "=" + value;

	  for (var propName in options) {
		updatedCookie += "; " + propName;
		var propValue = options[propName];
		if (propValue !== true) {
		  updatedCookie += "=" + propValue;
		}
	  }

	  document.cookie = updatedCookie;
	}
	
	this.deleteCookie=function (name) {
	  this.setCookie(name, "", {
		expires: -1
	  })
	}
	//\Работа с Куки//
	
	//Метод подгрузки иконки ajax-loader для аякс запросов//
	this.loader=function (stop, img, id) // - функция запуска анимации
	{
		if (!stop)
			stop=false;
		if (!img)
			img='files/js/images/ajax-loader.gif';
		if (!id)
			id='ajax-loader';
		if (typeof($)=='object' || typeof($)=='function') {
			if (!stop) {
				imgObj=$('body').append('<img id="'+id+'" src="'+img+'" style="position:absolute; z-index:1000; display:none;" />');
				// найдем элемент с изображением загрузки и уберем невидимость:
				var imgObj = $('#'+id);
				if (typeof(imgObj)=='object') {
					imgObj.show();
					// вычислим в какие координаты нужно поместить изображение загрузки,
					// чтобы оно оказалось в серидине страницы:
					var centerY = $(window).scrollTop() + ($(window).height() + imgObj.height())/2;
					var centerX = $(window).scrollLeft() + ($(window).width() + imgObj.width())/2;

					// поменяем координаты изображения на нужные:
					imgObj.offset({top:centerY, left:centerX});
				}
			} 
			else {// - функция останавливающая анимацию
			  $('#'+id).hide();
			}
		}
	}
	//\Метод подгрузки иконки ajax-loader для аякс запросов//

}//\class

//Создаем объект Lib и псевдоним lib//
var Lib=new lib();
var lib=Lib;