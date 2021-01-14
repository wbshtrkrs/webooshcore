function convertDate(stringDate, format) {
    if (!stringDate) {
        return '-';
    }

    return moment(stringDate).format(format);
}

function convertNumber(price, danger = '', abbr = null) {
    price = parseFloat(price);
    if (isNaN(price)) {
        price = 0;
    }

    let isDanger = false;
    if (price < 0) {
        if (danger.toLowerCase() === 'danger') {
            price = price * -1;
        }

        isDanger = true;
    }

    price += '';
    let x = price.split('.');
    let x1 = x[0];
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + '.' + '$2');
    }

    let html = '';
    if (danger && isDanger) {
        html += `<span class="text-danger">`;
        html += danger.toLowerCase() === 'danger' ? `(${abbr ? abbr + ' ' : ''}${x1})` : `${abbr ? abbr + ' ' : ''}${x1}`;
        html += `</span>`;
    } else {
        html = `${abbr ? abbr + ' ' : ''}${x1}`;
    }

    return html;
}

function removeURLParamDatatable(parameter) {
    var url = window.location.href;

    var urlparts= url.split('?');
    if (urlparts.length>=2) {

        var prefix= encodeURIComponent(parameter)+'=';
        var pars= urlparts[1].split(/[&;]/g);

        //reverse iteration as may be destructive
        for (var i= pars.length; i-- > 0;) {
            //idiom for string.startsWith
            if (pars[i].lastIndexOf(prefix, 0) !== -1) {
                pars.splice(i, 1);
            }
        }

        url = urlparts[0]+'?'+pars.join('&');
    }

    window.history.pushState({path: url}, '', url);
}

function insertUrlParamDatatable(key, value) {
    if (history.pushState) {
        let searchParams = new URLSearchParams(window.location.search);
        searchParams.set(key, value);
        let newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?' + searchParams.toString();
        window.history.pushState({path: newurl}, '', newurl);
    }
}

function getUrl(id = null, isDeleteUrl = false) {
    let url = window.location.href.toString();
    let urlSplit = url.split('/');
    let lastUrl = urlSplit[urlSplit.length-1];
    let lastUrlSplit = lastUrl.split('?');

    if (lastUrlSplit.length > 0) {
        urlSplit[urlSplit.length-1] = `${pluralize.singular(lastUrlSplit[0])}`;
    } else {
        urlSplit[urlSplit.length-1] = `${pluralize.singular(urlSplit[urlSplit.length-1])}`;
    }

    return urlSplit.join('/') + (isDeleteUrl ? '/delete' : '') + (id ? '/' + id : '');
}

$(document).ready(function () {
    $('.datatable').each(function() {
        let datatable = $(this);
        let idDatatable = datatable.attr('id');
        let isAjax = datatable.data('use-ajax');
        let isClickable = datatable.data('clickable');
        let stateSave = datatable.data('state-save');
        let option = {
            responsive: true,
            aaSorting: [],
            stateSave: !!stateSave,
        };

        if (isClickable) {
            let createdRowFunction = function( row, data) {
                $(row).addClass('clickable-row');

                let idData = data.id || null;
                if (idData) {
                    $(row).data('href', getUrl(idData));
                }
            };

            if (typeof createdRow === 'function') {
                createdRowFunction = createdRow;
            }

            option.createdRow = createdRowFunction;
        }

        if (typeof drawCallback === 'function') {
            option.drawCallback = drawCallback;
        }

        if (isAjax) {
            option.processing = true;
            option.serverSide = true;
            option.searchDelay = 500;
            option.ajax = {
                type: 'post',
                url: datatable.data('ajax-url'),
                data: function (filters) {
                    let additionalFilters = {};
                    let filterElements = datatable.parents('body').find(`[data-table="${idDatatable}"]`);
                    filterElements.each(function (i, el) {
                        let filterName = $(el).attr('name');
                        let filterValue = $(el).val();
                        if (filterValue) {
                            additionalFilters[filterName] = filterValue;
                        }

                        if (datatable.data('generate-url')) {
                            if (Array.isArray(filterValue) || filterValue.length === 0 || !filterValue) {
                                removeURLParamDatatable(filterName);
                            } else {
                                insertUrlParamDatatable(filterName, Array.isArray(filterValue) ? filterValue.join(',') : filterValue);
                            }
                        }
                    });

                    return $.extend(filters, additionalFilters);
                }
            };

            let columns = [];
            datatable.find('thead tr th').each(function (i, el) {
                let column = $(el);

                let render = null;
                if (typeof customColumns === 'function') {
                    let myCustomColumn = customColumns();
                    if (myCustomColumn[i] !== 'undefined') {
                        render = myCustomColumn[i];
                    }
                }

                let title = column.html();
                if (title.toLowerCase() === 'action') {
                    columns.push({
                        data: 'all',
                        name: 'all',
                        sortable: false,
                        searchable: false,
                        class: 'text-center',
                        render: render ? render : function(data, type, row) {
                            let idData = row.id || null;

                            let html = `<div class="action-wrapper">`;

                            if(idData) {
                                html += `<a href="${getUrl(idData)}" class="btn btn-outline-primary">View</a> `;
                                html += `<button type="button" data-href="${getUrl(idData, true)}" class="btn btn-outline-danger btn-default-confirmation">Delete</button>`;
                            }

                            html += `</div>`;
                            return html;
                        }
                    });

                    return;
                }

                let columnName = column.data('column-name');
                let columnData = column.data('column-data');

                let tempColumn = {
                    data: columnData ? columnData : columnName,
                    name: columnName
                };

                let customClass = column.data('column-class');
                if (typeof customClass !== 'undefined') {
                    tempColumn.className = customClass;
                }

                let sortable = column.data('column-sortable');
                if (typeof sortable !== 'undefined') {
                    tempColumn.sortable = sortable;
                }

                let searchable = column.data('column-searchable');
                if (typeof searchable !== 'undefined') {
                    tempColumn.searchable = searchable;
                }

                let dataType = column.data('type');
                if (!render && typeof dataType !== 'undefined') {
                    if (dataType.toLowerCase() === 'number') {
                        render = function(data) {
                            const dangerMinus = column.data('danger-minus');
                            const danger = column.data('danger');

                            return convertNumber(data, dangerMinus ? 'danger-minus' : (danger ? 'danger' : ''), column.data('abbr'));
                        };
                    } else if (['date', 'datetime'].includes(dataType.toLowerCase())) {
                        let format = column.data('format');
                        if (!format) {
                            if (dataType.toLowerCase() === 'date') {
                                format = 'DD-MM-YYYY';
                            } else if (dataType.toLowerCase() === 'datetime') {
                                format = 'DD-MM-YYYY HH:mm:ss';
                            }
                        }

                        render = function(data) {
                            return convertDate(data, format);
                        };
                    }
                }

                if (render) {
                    tempColumn.render = render;
                }

                columns.push(tempColumn);
            });

            option.columns = columns;
        }

        let initDatatable = $(this).DataTable(option);

        if (isAjax) {
            datatable.parents('body').find(`[data-table="${idDatatable}"]`).change(function() {
                initDatatable.ajax.reload();
            });
        }
    });
});
