var _p = location.pathname.split('/');
var ctxBase = _p.slice(0, _p.indexOf('admin') + 3).join('/');

$(document).ready(function () {

    // Inisialisasi Select2 pada elemen yang sudah ada di DOM
    if ($.fn.select2) {
        $('.select2').select2({ width: '100%' });
    }

    $(document).on('change', '#jenis_penjualan', function () {
        var isResep = $(this).val() === 'RESEP';
        $('#wrap_resep').toggle(isResep);
        if (!isResep) { $('#id_resep').val('').trigger('change'); }
        checkAllStock();
    });

    $(document).on('change', '#id_resep', function () {
        var idResep = $(this).val();
        var idPasien = $(this).find('option:selected').data('pasien');
        if (idPasien) { $('#id_pasien').val(idPasien).trigger('change'); }
        if (!idResep) { return; }
        $.getJSON(site_url + 'resep/api/detail/' + idResep, function (res) {
            if (!res.success) { return; }
            var box = $('#item_rows');
            box.empty();
            $.each(res.data.detail, function (i, d) {
                box.append(rowObat(d.id_obat, d.jumlah, d.nama_obat, d.stok));
            });
            if ($.fn.select2) {
                box.find('.select2').select2({ width: '100%' });
            }
            checkAllStock();
        });
    });

    $(document).on('click', '#btn_add_row', function () {
        var newRow = $(rowObat(0, '', '', 0));
        $('#item_rows').append(newRow);
        if ($.fn.select2) {
            newRow.find('.select2').select2({ width: '100%' });
        }
        checkAllStock();
    });

    $(document).on('click', '.btn-del-row', function () {
        if ($('.item-row').length > 1) {
            $(this).closest('.item-row').remove();
            checkAllStock();
        }
    });

    $(document).on('change input', 'select[name="id_obat[]"], input[name="jumlah[]"]', function () {
        checkAllStock();
    });

    // Run initial stock check on load
    checkAllStock();

});

function checkAllStock() {
    var hasError = false;
    var stockMap = {};
    if (typeof daftarObat !== 'undefined') {
        $.each(daftarObat, function (i, o) {
            stockMap[o.id] = parseInt(o.stok) || 0;
        });
    }

    $('.item-row').each(function () {
        var row = $(this);
        var select = row.find('select[name="id_obat[]"]');
        var inputJumlah = row.find('input[name="jumlah[]"]');
        var errText = row.find('.err-stok-text');
        var infoText = row.find('.info-stok-text');

        if (errText.length === 0) {
            inputJumlah.after('<small class="text-danger err-stok-text" style="display:none;"></small>');
            errText = row.find('.err-stok-text');
        }
        if (infoText.length === 0) {
            select.parent().append('<small class="form-text text-muted info-stok-text"></small>');
            infoText = row.find('.info-stok-text');
        }

        var idObat = parseInt(select.val()) || 0;
        var jumlah = parseInt(inputJumlah.val()) || 0;
        
        var availableStok = 0;
        if (idObat > 0) {
            var selectedOpt = select.find('option:selected');
            if (selectedOpt.length && typeof selectedOpt.data('stok') !== 'undefined') {
                availableStok = parseInt(selectedOpt.data('stok')) || 0;
            } else if (typeof stockMap[idObat] !== 'undefined') {
                availableStok = stockMap[idObat];
            }
            infoText.text('Stok tersedia: ' + availableStok);
        } else {
            infoText.text('');
        }

        if (idObat > 0 && jumlah > 0) {
            if (jumlah > availableStok) {
                hasError = true;
                errText.text('Stok tidak cukup (tersedia ' + availableStok + ', diminta ' + jumlah + ')').show();
                inputJumlah.addClass('is-invalid');
            } else {
                errText.hide().text('');
                inputJumlah.removeClass('is-invalid');
            }
        } else {
            errText.hide().text('');
            inputJumlah.removeClass('is-invalid');
        }
    });

    if (hasError) {
        $('#alert_stok_cukup').show();
        $('#btn_submit').prop('disabled', true).addClass('disabled');
    } else {
        $('#alert_stok_cukup').hide();
        $('#btn_submit').prop('disabled', false).removeClass('disabled');
    }
}

function rowObat(idObat, jumlah, namaObat, stok) {
    var opt;
    var stokVal = (typeof stok !== 'undefined') ? parseInt(stok) : 0;
    if (idObat) {
        opt = '<option value="' + idObat + '" data-stok="' + stokVal + '" selected>' + (namaObat || idObat) + ' (Stok: ' + stokVal + ')</option>';
    } else {
        opt = '<option value="">-- Pilih Obat --</option>';
        if (typeof daftarObat !== 'undefined') {
            $.each(daftarObat, function (i, o) {
                opt += '<option value="' + o.id + '" data-stok="' + o.stok + '">' + o.nama + ' (Stok: ' + o.stok + ')</option>';
            });
        }
    }
    return '<div class="row item-row mb-2">'
        + '<div class="col-md-6"><select name="id_obat[]" class="form-control select2 input-id-obat" required>' + opt + '</select><small class="form-text text-muted info-stok-text"></small></div>'
        + '<div class="col-md-4"><input type="number" min="1" name="jumlah[]" class="form-control input-jumlah" placeholder="Jumlah" value="' + (jumlah || '') + '" required><small class="text-danger err-stok-text" style="display:none;"></small></div>'
        + '<div class="col-md-2"><button type="button" class="btn btn-danger btn-block btn-del-row">Hapus</button></div>'
        + '</div>';
}
