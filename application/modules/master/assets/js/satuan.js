$('#satuan_table').bfDataTable({
    url: site_url + 'admin/master/satuan/get_data',
    filterCols: [0, 1],
    sortCols: { id_satuan: 'desc' },
    lengthMenu: [10, 25, 50, 100],
    columns: [
        { data: 'id_satuan' },
        { data: 'nama_satuan' },
        {
            data: 'status',
            render: function (data) {
                var cls = data === 'AKTIF' ? 'badge-success' : 'badge-danger';
                return '<span class="badge ' + cls + '">' + data + '</span>';
            }
        },
        { data: null, orderable: false, searchable: false, render: function(data) {
            var editUrl = site_url + 'admin/master/satuan/edit/' + data.id;
            var deleteUrl = site_url + 'admin/master/satuan/delete/' + data.id;
            return '<a href="' + editUrl + '" class="btn btn-xs btn-primary mr-1"><i class="fas fa-edit"></i></a>' +
                '<button class="btn btn-xs btn-danger btn-hapus" data-url="' + deleteUrl + '" data-nama="' + data.nama_satuan + '"><i class="fas fa-trash"></i></button>';
        } }
    ]
});

$(document).on('click', '.btn-hapus', function(e) {
    e.preventDefault();
    var btn = $(this);
    var url = btn.data('url');
    var nama = btn.data('nama');
    Swal.fire({
        title: 'Hapus Satuan?',
        html: 'Data <strong>' + nama + '</strong> akan dihapus permanen. Obat yang sudah memakai satuan ini tidak akan terpengaruh. Namun satuan ini tidak bisa dihapus jika masih terkait.',
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
                    $('#satuan_table').DataTable().ajax.reload(null, false);
                } else {
                    Swal.fire('Gagal', res.message, 'error');
                }
            }, 'json').fail(function() {
                Swal.fire('Error', 'Terjadi kesalahan server.', 'error');
            });
        }
    });
});
