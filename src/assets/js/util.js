Number.prototype.formatBase = function(n, x, s, c) {
	var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\D' : '$') + ')',
		num = this.toFixed(Math.max(0, ~~n));

	return (c ? num.replace('.', c) : num).replace(new RegExp(re, 'g'), '$&' + (s || ','));
};

String.prototype.toLabel = function () {
	var label = $('[name*='+this+']').attr('label');
	if (label == undefined || label == '') label = this.replaceAll("_", " ").capitalizeFirstLetter();
	return label;
};

String.prototype.capitalizeFirstLetter = function () {
	return this.charAt(0).toUpperCase() + this.slice(1);
};

getValue = function (object, attributeName) {
	if (!object) return '';

	var fields = attributeName.split('.');
	for(var i = 0; i<fields.length; i++){
		if (object[fields[i]]) object = object[fields[i]];
		else return '';
	}
	return object;
};

String.prototype.replaceAll = function (search, replacement) {
	var target = this;
	return target.split(search).join(replacement);
};
window.randomScalingFactor = function() {
	return (Math.random() > 0.5 ? 1.0 : -1.0) * Math.round(Math.random() * 100);
};

function format(number){
	number = Number(number);
	return number.formatBase(0, 3, '.', ',');;
}

function sort(a,b){
	a = a.text.toLowerCase();
	b = b.text.toLowerCase();
	if(a > b) {
		return 1;
	} else if (a < b) {
		return -1;
	}
	return 0;
}

function validateStringNotEmpty(word) {
	if(!word || word == undefined || word.trim() == '') return false;
	return true;
}

function validateEmail(email) {
	var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	return re.test(email);
}

function scrollToTop() {
	$("html, body").animate({ scrollTop: 0 }, "fast");
}

$.fn.addHiddenInputData = function(data) {
	var keys = {};
	var addData = function(data, prefix) {
		for(var key in data) {
			if (typeof vueExcludeSubmit !== 'undefined' && vueExcludeSubmit.includes(key)) continue;
			var value = data[key];
			if($('input[type=file][name="'+key+'"]').length > 0) continue;
			if(!prefix) {
				var nprefix = key;
			}else{
				var nprefix = prefix + '['+key+']';
			}
			if(typeof(value) == 'object') {
				addData(value, nprefix);
				continue;
			}
			keys[nprefix] = value;
		}
	}
	addData(data);
	var $form = $(this);
	for(var k in keys) {
		$form.addHiddenInput(k, keys[k]);
	}

}
$.fn.addHiddenInput = function(key, value) {
	var $input = $('<input type="hidden" name="'+key+'" />')
	$input.val(value);
	$(this).append($input);

}
var delay = (function(){
	var timer = 0;
	return function(callback, ms){
		clearTimeout (timer);
		timer = setTimeout(callback, ms);
	};
})();
function empty(object){
	if (object == undefined || object == '') return true;
	return false;
}
function transparentize(color, opacity) {
	var alpha = opacity === undefined ? 0.5 : 1 - opacity;
	return Color(color).alpha(alpha).rgbString();
}