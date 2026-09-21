var _p = location.pathname.split('/');
var ctxBase = _p.slice(0, _p.indexOf('admin') + 3).join('/');

// Label & warna untuk setiap status antrian
var statusInfo = {
    'MENUNGGU':         { badge: 'badge-warning',   label: 'Menunggu' },
    'DIPANGGIL':        { badge: 'badge-info',      label: 'Dipanggil' },
    'DILEWATI':         { badge: 'badge-secondary', label: 'Dilewati' },
    'SEDANG_DIPERIKSA': { badge: 'badge-primary',   label: 'Sedang Diperiksa' },
    'SELESAI':          { badge: 'badge-success',   label: 'Selesai' },
    'BATAL':            { badge: 'badge-danger',    label: 'Batal' },
};

// Alur status yang diizinkan
var alurStatus = {
    'MENUNGGU':         ['DIPANGGIL', 'BATAL'],
    'DIPANGGIL':        ['SEDANG_DIPERIKSA', 'DILEWATI', 'BATAL'],
    'DILEWATI':         ['DIPANGGIL', 'BATAL'],
    'SEDANG_DIPERIKSA': ['SELESAI'],
    'SELESAI':          [],
    'BATAL':            [],
};

// Kelas tombol per status tujuan
var btnClass = {
    'DIPANGGIL':        'btn-info',
    'SEDANG_DIPERIKSA': 'btn-primary',
    'DILEWATI':         'btn-secondary',
    'SELESAI':          'btn-success',
    'BATAL':            'btn-danger',
};

// Label tombol per status tujuan
var btnLabel = {
    'DIPANGGIL':        '<i class="fas fa-bullhorn"></i> Panggil',
    'SEDANG_DIPERIKSA': '<i class="fas fa-stethoscope"></i> Mulai Periksa',
    'DILEWATI':         '<i class="fas fa-forward"></i> Lewati',
    'SELESAI':          '<i class="fas fa-check"></i> Selesai',
    'BATAL':            '<i class="fas fa-times"></i> Batalkan',
};

var antrianTable = $('#antrian_table').bfDataTable({
    url: ctxBase + '/get_data',
    filterCols: [0, 1, 2, 3, 4],
    sortCols: { id_antrian: 'asc' },
    lengthMenu: [10, 25, 50, 100],
    columns: [
        { data: 'nomor_antrian' },
        { data: 'no_rm' },
        { data: 'nama_pasien' },
        { data: 'nama_poli' },
        {
            data: 'status',
            render: function (data) {
                var info = statusInfo[data] || { badge: 'badge-secondary', label: data };
                return '<span class="badge ' + info.badge + '">' + info.label + '</span>';
            }
        },
        { data: 'waktu_dipanggil', defaultContent: '-' },
        {
            data: null,
            orderable: false,
            render: function (data) {
                var berikut = alurStatus[data.status] || [];
                var html = '<a href="' + ctxBase + '/tiket/' + data.id_antrian + '" class="btn btn-sm btn-default mr-1" title="Cetak Tiket"><i class="fas fa-print"></i></a>';
                if (berikut.length === 0) return html + '<span class="text-muted">-</span>';
                return '<button class="btn btn-sm btn-outline-primary btn-aksi" '
                    + 'data-id="' + data.id_antrian + '" '
                    + 'data-nama="' + (data.nama_pasien || '') + '" '
                    + 'data-nomor="' + data.nomor_antrian + '" '
                    + 'data-status="' + data.status + '">'
                    + '<i class="fas fa-exchange-alt"></i> Ubah Status</button>';
            }
        }
    ]
});

// Buka modal saat klik tombol Ubah Status
$(document).on('click', '.btn-aksi', function () {
    var id     = $(this).data('id');
    var nama   = $(this).data('nama');
    var nomor  = $(this).data('nomor');
    var status = $(this).data('status');
    var berikut = alurStatus[status] || [];

    $('#modal_nama_pasien').text(nama);
    $('#modal_nomor').text(nomor);
    $('#modal_status_lama').text((statusInfo[status] || {}).label || status);

    var $btn = $('#modal_btn_status').empty();
    berikut.forEach(function (st) {
        var cls   = btnClass[st] || 'btn-secondary';
        var label = btnLabel[st] || st;
        var onconfirm = st === 'BATAL' ? 'onsubmit="return confirm(\'Yakin ingin membatalkan antrian ini?\')"' : '';
        $btn.append(
            '<form method="post" action="' + ctxBase + '/ubah_status/' + id + '" style="display:inline" ' + onconfirm + '>'
            + '<input type="hidden" name="status" value="' + st + '">'
            + '<button type="submit" class="btn ' + cls + '">' + label + '</button>'
            + '</form>'
        );
    });

    $('#modal_status').modal('show');
});
