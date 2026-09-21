$.fn.dataTableExt.oApi._fnFilterInputGroup = function (oSettings) {
	if (oSettings.oInit.searching) {
		const sFilterId = '#' + oSettings.sTableId + '_filter';
		$(sFilterId).html(`
            <div class="input-group">
                <input type="search" class="form-control" placeholder="Cari..." autocomplete="off">
                <span class="input-group-append">
                    <button class="btn btn-flat btn-primary"><i class="fas fa-search"></i></button>
                </span>
            </div>
        `);

		let searchTimer = null;
		const draw = $.proxy(function () { this.fnDraw(); }, this);
		$('input', sFilterId).on('keyup', function (e) {
			if (e.key == 'Enter') {
				clearTimeout(searchTimer);
				draw();
				return;
			}
			clearTimeout(searchTimer);
			searchTimer = setTimeout(draw, 400);
		});
		$('button', sFilterId).on('mouseup', function () {
			clearTimeout(searchTimer);
			draw();
		});
	}

	return this;
};

$.fn.dataTableExt.oApi._fnLengthInputGroup = function (oSettings) {
	if (oSettings.oInit.lengthChange) {
		let nLengthInputGroup = '<select class="form-control">';
		oSettings.oInit.lengthMenu.forEach(function (i) {
			nLengthInputGroup += '<option value="' + i + '">' + i + '</option>';
		});
		nLengthInputGroup += '</select>';

		const sLengthId = '#' + oSettings.sTableId + '_length';
		$(sLengthId).html(nLengthInputGroup);

		$('select', sLengthId).on('change', $.proxy(function () {
			this.fnDraw();
		}, this));
	}

	return this;
};

$.fn.bfDataTable = function (options) {
	return document.body.contains(this[0]) ? this.dataTable($.extend(true, options, {
		ajax: {
			url: options.ajax?.url ?? options.url,
			type: 'POST',
			data: options.ajax?.data ?? $.proxy(function (data) {
				data.length = $('select', '#' + this.fnSettings().sTableId + '_length').val();
				const sFilterId = '#' + this.fnSettings().sTableId + '_filter';
				data.search.column = $('select', sFilterId).val() || '';
				data.search.value = $('input', sFilterId).val();
				data.sort = options.sortCols ?? false;
				$.extend(data, { params: options.params ?? false });
			}, this),
		},
		autoWidth: false,
		lengthChange: options.lengthMenu?.length > 0 ? true : false,
		ordering: false,
		searching: options.filterCols?.length > 0 ? true : false,
		serverSide: true,
		createdRow: function (row, data) {
			if (options.targetUrl) {
				$('td:first-child', row).html('<a href="' + options.targetUrl + '/' + data.id + '"> ' + $('td:first-child', row).text() + '</a>');
			}
		}
	}))._fnLengthInputGroup()
		._fnFilterInputGroup()
		.wrap('<div class="table-responsive"></div>') : this;
};

$(document).on('wheel', 'input[type="number"]', function (e) {
	$(this).blur();
});
$(document).on('keydown', 'input[type="number"]', function (e) {
	return e.which != 38 && e.which != 40;
});
$(document).on('beforeinput', '.form-numeric', function (e) {
	if (e.originalEvent.inputType != 'deleteContentForward' && e.originalEvent.inputType != 'deleteContentBackward' &&
		(e.originalEvent.data == ' ' || isNaN(e.originalEvent.data))) {
		return false;
	}
});
$(document).on('input', '.form-uppercase', function (e) {
	$(this).val(this.value.toUpperCase());
});

$('.select2').select2();