$(document).ready(function () {
    var _p = location.pathname.split('/');
    var ctxBase = _p.slice(0, _p.indexOf('admin') + 3).join('/');

    $('#tagihan_table').bfDataTable({
        url: ctxBase + '/get_data',
        targetUrl: ctxBase + '/detail',
        filterCols: [0, 1, 2, 3, 4, 5],
        sortCols: { id_tagihan: 'desc' },
        lengthMenu: [10, 25, 50],
        columns: [
            { data: 'nomor_tagihan' },
            { data: 'tanggal_tagihan' },
            { data: 'no_rm', defaultContent: '-' },
            { data: 'nama_pasien', defaultContent: 'Umum' },
            {
                data: 'total',
                render: function (data) {
                    return 'Rp ' + Number(data).toLocaleString('id-ID');
                }
            },
            {
                data: 'status',
                render: function (data) {
                    var cls = data === 'LUNAS' ? 'badge-success' : (data === 'BATAL' ? 'badge-danger' : 'badge-warning');
                    return '<span class="badge ' + cls + '">' + data + '</span>';
                }
            },
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function (data) {
                    var html = '<a href="' + ctxBase + '/detail/' + data.id + '" class="btn btn-sm btn-primary mr-1"><i class="fas fa-eye"></i></a>';
                    if (data.status === 'BELUM_DIBAYAR') {
                        html += ' <button class="btn btn-sm btn-danger btn-hapus" data-id="' + data.id + '" data-nama="' + data.nomor_tagihan + '"><i class="fas fa-trash"></i></button>';
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
            title: 'Hapus Tagihan?',
            text: 'Tagihan ' + nama + ' akan dihapus.',
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
                        $('#tagihan_table').DataTable().ajax.reload();
                    } else {
                        Swal.fire('Gagal', res.message, 'error');
                    }
                }, 'json').fail(function () {
                    Swal.fire('Error', 'Terjadi kesalahan server. Coba lagi atau muat ulang halaman.', 'error');
                });
            }
        });
    });
});
