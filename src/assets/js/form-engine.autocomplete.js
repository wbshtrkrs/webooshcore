$(document).ready(function(){
	initAutoComplete();
});

function initAutoComplete(){
	$("[select2ajax]").each(function(){
		var self = $(this);

		var getValue = $(this).attr('getvalue');
		if (!getValue) getValue = 'name';

		var key = $(this).attr('key');
		if (!key) key = 'id';

		var exceptionIds = [];
		var exceptionName = $(this).data('exception');

		var dataCustom = [];

		if ($(this).data('custom') != undefined) {
			dataCustom = JSON.stringify($(this).data('custom'));
			dataCustom = JSON.parse(dataCustom);
		}

		var url = $(this).data('url');
		var limit = $(this).data('limit');
		if (!limit){
			limit = 10;
		}

		var options = {
			ajax: {
				url: function () {
					url = self.attr('data-url');
					return url;
				},
				dataType: 'json',
				data: function (params) {
					var query = {
						keyword: params.term,
						limit: limit,
						page: params.page || 1,
						exception: function () {
							if (exceptionName) {
								exceptionIds = $(`[name=${exceptionName}]`).val()
							}

							return exceptionIds;
						},
					};

					$.each(dataCustom, function (key, item) {
						query[item] = $(`[name=${item}]`).val()
					});

					return query;
				},
				processResults: function (data) {
					params.page = params.page || 1;
					serverLimit = data.limit || limit;

					var processedData = [];
					$.each(data.results, function(index, item) {
						processedData.push({
							id: item[key],
							text: item[getValue],
							data: item
						});
					});

					return {
						results: processedData,
						pagination: {
							more: (params.page * serverLimit) < data.totalCount
						}
					};
				}
			}
		};

		if($(this).attr('placeholder')) {
			options.placeholder = $(this).attr('placeholder');
		}

		var onSelectFunction = $(this).data('select2click');
		$(this).select2(options).on('select2:select', function(){
			var self = $(this);
			window[onSelectFunction]($(this).select2('data')[0], self);
			$(this).val(0).trigger('change');
		});
	});
}
