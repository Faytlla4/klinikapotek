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