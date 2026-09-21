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
                box.append(rowObat(d.id_obat, d.jumlah, d.nama_obat));
            });
            if ($.fn.select2) {
                box.find('.select2').select2({ width: '100%' });
            }
        });
    });

    $(document).on('click', '#btn_add_row', function () {
        var newRow = $(rowObat(0, '', ''));
        $('#item_rows').append(newRow);
        if ($.fn.select2) {
            newRow.find('.select2').select2({ width: '100%' });
        }
    });

    $(document).on('click', '.btn-del-row', function () {
        if ($('.item-row').length > 1) { $(this).closest('.item-row').remove(); }
    });

});

function rowObat(idObat, jumlah, namaObat) {
    var opt;
    if (idObat) {
        opt = '<option value="' + idObat + '" selected>' + (namaObat || idObat) + '</option>';
    } else {
        opt = '<option value="">-- Pilih Obat --</option>';
        if (typeof daftarObat !== 'undefined') {
            $.each(daftarObat, function (i, o) {
                opt += '<option value="' + o.id + '">' + o.nama + '</option>';
            });
        }
    }
    return '<div class="row item-row mb-2">'
        + '<div class="col-md-6"><select name="id_obat[]" class="form-control select2" required>' + opt + '</select></div>'
        + '<div class="col-md-4"><input type="number" min="1" name="jumlah[]" class="form-control" placeholder="Jumlah" value="' + (jumlah || '') + '" required></div>'
        + '<div class="col-md-2"><button type="button" class="btn btn-danger btn-block btn-del-row">Hapus</button></div>'
        + '</div>';
}
