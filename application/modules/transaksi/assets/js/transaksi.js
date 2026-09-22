$(document).ready(function () {
    var _p = location.pathname.split('/');
    var ctxBase = _p.slice(0, _p.indexOf('admin') + 3).join('/');

    $('#transaksi_table').bfDataTable({
        url: ctxBase + '/get_data',
        ajax: {
            url: ctxBase + '/get_data',
            data: function (data) {
                data.length = $('select', '#transaksi_table_length').val();
                data.search = data.search || {};
                data.search.value = $('#transaksi_table_filter input').val();
                data.dari = $('#filter-dari').val();
                data.sampai = $('#filter-sampai').val();
            }
        },
        filterCols: [0, 1, 2, 3],
        sortCols: { id_transaksi: 'desc' },
        lengthMenu: [10, 25, 50],
        columns: [
            { data: 'nomor_transaksi' },
            { data: 'tanggal_transaksi' },
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
                        html += ' <button class="btn btn-sm btn-danger btn-hapus" data-id="' + data.id + '" data-nama="' + data.nomor_transaksi + '"><i class="fas fa-trash"></i></button>';
                    }
                    return html;
                }
            }
        ]
    });

    $('#filter-dari, #filter-sampai').on('change', function () {
        $('#transaksi_table').DataTable().ajax.reload();
    });
    $('#filter-reset').on('click', function () {
        $('#filter-dari').val('');
        $('#filter-sampai').val('');
        $('#transaksi_table').DataTable().ajax.reload();
    });

    $(document).on('click', '.btn-hapus', function () {
        var id = $(this).data('id');
        var nama = $(this).data('nama');
        Swal.fire({
            title: 'Hapus Transaksi?',
            text: 'Transaksi ' + nama + ' akan dihapus.',
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
                        $('#transaksi_table').DataTable().ajax.reload();
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
