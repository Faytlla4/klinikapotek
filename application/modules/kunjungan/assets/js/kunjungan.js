var _p = location.pathname.split('/');
var ctxBase = _p.slice(0, _p.indexOf('admin') + 3).join('/');

$('#kunjungan_table').bfDataTable({
    url: ctxBase + '/get_data',
    targetUrl: ctxBase + '/detail',
    filterCols: [0, 1, 2, 3, 4, 5, 6],
    sortCols: { id_kunjungan: 'desc' },
    lengthMenu: [10, 25, 50, 100],
    columns: [
        { data: 'tanggal_kunjungan' }, { data: 'no_rm' }, { data: 'nama_pasien' },
        { data: 'nama_pelayanan' }, { data: 'nama_poli' }, { data: 'nama_dokter' },
        { data: 'nama_ruangan' }, { data: 'status', render: function(data) { return '<span class="badge badge-info">' + data + '</span>'; } }
    ]
});

$('.select2').select2({ theme: 'bootstrap4' });

// Riwayat kunjungan pasien saat pilih pasien di form tambah
$('#id_pasien').on('change', function () {
    var id_pasien = $(this).val();
    var $container = $('#riwayat_container');
    var $tbody = $('#riwayat_body');

    if (!id_pasien) {
        $container.addClass('d-none');
        $tbody.empty();
        return;
    }

    $.getJSON(ctxBase + '/get_riwayat/' + id_pasien, function (res) {
        $tbody.empty();
        if (!res.status || !res.data || res.data.length === 0) {
            $tbody.append('<tr><td colspan="5" class="text-center text-muted">Belum ada riwayat kunjungan.</td></tr>');
        } else {
            $.each(res.data, function (i, row) {
                var badgeClass = {
                    'TERDAFTAR': 'badge-primary',
                    'MENUNGGU' : 'badge-warning',
                    'DIPROSES' : 'badge-info',
                    'SELESAI'  : 'badge-success',
                    'BATAL'    : 'badge-danger'
                }[row.status] || 'badge-secondary';
                $tbody.append(
                    '<tr>' +
                    '<td>' + (row.tanggal_kunjungan || '-') + '</td>' +
                    '<td>' + (row.nama_pelayanan || '-') + '</td>' +
                    '<td>' + (row.nama_poli || '-') + '</td>' +
                    '<td>' + (row.nama_dokter || '-') + '</td>' +
                    '<td><span class="badge ' + badgeClass + '">' + (row.status || '-') + '</span></td>' +
                    '</tr>'
                );
            });
        }
        $container.removeClass('d-none');
    }).fail(function () {
        $container.addClass('d-none');
    });
});
