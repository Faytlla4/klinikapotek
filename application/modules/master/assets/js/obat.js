$('#obat_table').bfDataTable({
    url: site_url + 'admin/master/obat/get_data',
    targetUrl: site_url + 'admin/master/obat/edit',
    filterCols: [0, 1, 2, 3],
    sortCols: { id_obat: 'desc' },
    lengthMenu: [10, 25, 50, 100],
    columns: [
        { data: 'id_obat' },
        { data: 'kode_obat', defaultContent: '-' },
        { data: 'nama_obat' },
        { data: 'jenis_obat', defaultContent: '-' },
        { data: 'satuan', defaultContent: '-' },
        {
            data: 'harga',
            render: function (data) {
                return 'Rp ' + Number(data).toLocaleString('id-ID');
            }
        },
        {
            data: 'stok',
            render: function (data, type, row) {
                var stok = parseInt(data) || 0;
                var min  = parseInt(row.stok_minimum) || 0;
                var cls  = stok <= min ? 'text-danger font-weight-bold' : '';
                return '<span class="' + cls + '">' + stok + '</span>';
            }
        },
        { data: 'stok_minimum', defaultContent: '0' },
        {
            data: 'wajib_resep',
            render: function (data) {
                var ya = (data === true || data === 't' || data === 'true' || data === '1' || data === 1);
                return ya
                    ? '<span class="badge badge-warning">YA</span>'
                    : '<span class="badge badge-secondary">TIDAK</span>';
            }
        },
        {
            data: 'status',
            render: function (data) {
                var cls = data === 'AKTIF' ? 'badge-success' : 'badge-danger';
                return '<span class="badge ' + cls + '">' + data + '</span>';
            }
        },
        { data: null, orderable: false, searchable: false, render: function(data) {
            var editUrl = site_url + 'admin/master/obat/edit/' + data.id;
            var deleteUrl = site_url + 'admin/master/obat/delete/' + data.id;
            return '<a href="' + editUrl + '" class="btn btn-xs btn-primary mr-1"><i class="fas fa-edit"></i></a>' +
                '<button class="btn btn-xs btn-danger btn-hapus" data-url="' + deleteUrl + '" data-nama="' + data.nama_obat + '"><i class="fas fa-trash"></i></button>';
        } }
    ]
});

$('.select2').select2();

$(document).on('click', '.btn-hapus', function(e) {
    e.preventDefault();
    var btn = $(this);
    var url = btn.data('url');
    var nama = btn.data('nama');
    Swal.fire({
        title: 'Hapus Obat?',
        html: 'Data <strong>' + nama + '</strong> akan dihapus permanen.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then(function(result) {
        if (result.isConfirmed) {
            $.post(url, function(res) {
                if (res.success) {
                    Swal.fire('Terhapus!', res.message, 'success');
                    $('#obat_table').DataTable().ajax.reload(null, false);
                } else {
                    Swal.fire('Gagal', res.message, 'error');
                }
            }, 'json').fail(function() {
                Swal.fire('Error', 'Terjadi kesalahan server.', 'error');
            });
        }
    });
});
