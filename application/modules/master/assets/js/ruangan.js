$('#ruangan_table').bfDataTable({
    url: site_url + 'admin/master/ruangan/get_data',
    targetUrl: site_url + 'admin/master/ruangan/edit',
    filterCols: [0, 1, 2],
    sortCols: { id_ruangan: 'desc' },
    lengthMenu: [10, 25, 50, 100],
    columns: [
        { data: 'id_ruangan' },
        { data: 'nama_ruangan' },
        { 
            data: 'nama_poli',
            render: function(data) {
                return data ? data : '<span class="text-muted">-</span>';
            }
        },
        { 
            data: 'status',
            render: function(data) {
                var cls = data === 'AKTIF' ? 'badge-success' : 'badge-danger';
                return '<span class="badge ' + cls + '">' + data + '</span>';
            }
        },
        { data: null, orderable: false, searchable: false, render: function(data) {
            var editUrl = site_url + 'admin/master/ruangan/edit/' + data.id;
            var deleteUrl = site_url + 'admin/master/ruangan/delete/' + data.id;
            return '<a href="' + editUrl + '" class="btn btn-xs btn-primary mr-1"><i class="fas fa-edit"></i></a>' +
                '<button class="btn btn-xs btn-danger btn-hapus" data-url="' + deleteUrl + '" data-nama="' + data.nama_ruangan + '"><i class="fas fa-trash"></i></button>';
        } }
    ]
});

$('.select2').select2({ theme: 'bootstrap4' });

$(document).on('click', '.btn-hapus', function(e) {
    e.preventDefault();
    var btn = $(this);
    var url = btn.data('url');
    var nama = btn.data('nama');
    Swal.fire({
        title: 'Hapus Ruangan?',
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
                    $('#ruangan_table').DataTable().ajax.reload(null, false);
                } else {
                    Swal.fire('Gagal', res.message, 'error');
                }
            }, 'json').fail(function() {
                Swal.fire('Error', 'Terjadi kesalahan server.', 'error');
            });
        }
    });
});
