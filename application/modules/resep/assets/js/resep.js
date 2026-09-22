var _p = location.pathname.split('/');
var ctxBase = _p.slice(0, _p.indexOf('admin') + 3).join('/');
$('#resep_table').bfDataTable({
    url: ctxBase + '/get_data',
    filterCols: [0, 1, 2, 3, 4],
    sortCols: {id_resep: 'asc'},
    lengthMenu: [10, 25, 50],
    columns: [
        {data: 'nomor_resep'},
        {data: 'tanggal_resep'},
        {data: 'nama_pasien'},
        {data: 'nama_dokter'},
        {data: 'status'},
        {
            data: null,
            orderable: false,
            searchable: false,
            render: function (data) {
                var html = '<a href="' + ctxBase + '/detail/' + data.id + '" class="btn btn-sm btn-primary mr-1"><i class="fas fa-eye"></i></a>';
                if (data.status === 'DIBUAT') {
                    html += ' <button class="btn btn-sm btn-danger btn-hapus" data-id="' + data.id + '" data-nama="' + data.nomor_resep + '"><i class="fas fa-trash"></i></button>';
                }
                return html;
            }
        }
    ]
});

$(document).on('click', '.btn-hapus', function () {
    var id = $(this).data('id');
    var nama = $(this).data('nama');
    Swal.fire({
        title: 'Hapus Resep?',
        text: 'Resep ' + nama + ' akan dihapus.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!'
    }).then(function (result) {
        if (result.isConfirmed) {
            $.post(ctxBase + '/delete/' + id, {csrf_token: $('input[name=csrf_token]').val()}, function (res) {
                if (res.success) {
                    Swal.fire('Terhapus!', res.message, 'success');
                        $('#resep_table').DataTable().ajax.reload();
                    } else {
                        Swal.fire('Gagal', res.message, 'error');
                    }
                }, 'json').fail(function () {
                    Swal.fire('Error', 'Terjadi kesalahan server. Coba lagi atau muat ulang halaman.', 'error');
                });
        }
    });
});
