$('#poli_table').bfDataTable({
    url: site_url + 'admin/master/poli/get_data',
    targetUrl: site_url + 'admin/master/poli/edit',
    filterCols: [0, 1],
    sortCols: { id_poli: 'desc' },
    lengthMenu: [10, 25, 50, 100],
    columns: [
        { data: 'id_poli' },
        { data: 'nama_poli' },
        { 
            data: 'status',
            render: function(data) {
                var cls = data === 'AKTIF' ? 'badge-success' : 'badge-danger';
                return '<span class="badge ' + cls + '">' + data + '</span>';
            }
        },
        { data: null, orderable: false, searchable: false, render: function(data) {
            var editUrl = site_url + 'admin/master/poli/edit/' + data.id;
            var deleteUrl = site_url + 'admin/master/poli/delete/' + data.id;
            return '<a href="' + editUrl + '" class="btn btn-xs btn-primary mr-1"><i class="fas fa-edit"></i></a>' +
                '<button class="btn btn-xs btn-danger btn-hapus" data-url="' + deleteUrl + '" data-nama="' + data.nama_poli + '"><i class="fas fa-trash"></i></button>';
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
        title: 'Hapus Poli?',
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
                    $('#poli_table').DataTable().ajax.reload(null, false);
                } else {
                    Swal.fire('Gagal', res.message, 'error');
                }
            }, 'json').fail(function() {
                Swal.fire('Error', 'Terjadi kesalahan server.', 'error');
            });
        }
    });
});
