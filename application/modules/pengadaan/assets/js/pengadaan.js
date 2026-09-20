var _p = location.pathname.split('/');
var ctxBase = _p.slice(0, _p.indexOf('admin') + 3).join('/');
$('#pengadaan_table').bfDataTable({
    url: ctxBase + '/get_data',
    filterCols: [0, 1, 2, 3, 4],
    sortCols: {id_pengadaan: 'desc'},
    lengthMenu: [10, 25, 50],
    columns: [
        {data: 'nomor_pengadaan'},
        {data: 'tanggal_pesanan'},
        {data: 'nama_supplier'},
        {data: 'total'},
        {data: 'status'},
        {
            data: null,
            orderable: false,
            searchable: false,
            render: function (data) {
                var html = '<a href="' + ctxBase + '/detail/' + data.id + '" class="btn btn-sm btn-primary mr-1"><i class="fas fa-eye"></i></a>';
                if (data.status === 'Dipesan') {
                    html += ' <button class="btn btn-sm btn-danger btn-hapus" data-id="' + data.id + '" data-nama="' + data.nomor_pengadaan + '"><i class="fas fa-trash"></i></button>';
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
        title: 'Hapus Pengadaan?',
        text: 'Pengadaan ' + nama + ' akan dihapus.',
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
                    $('#pengadaan_table').DataTable().ajax.reload();
                } else {
                    Swal.fire('Gagal', res.message, 'error');
                }
            }, 'json');
        }
    });
});
$('.select2').select2({theme: 'bootstrap4'});
