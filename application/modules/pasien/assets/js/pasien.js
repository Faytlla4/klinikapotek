var _p = location.pathname.split('/');
var pasienTable = $('#pasien_table');
var editBase = pasienTable.data('edit-url');
pasienTable.bfDataTable({
    // Generated server-side so the AJAX endpoint remains correct in every context.
    url: pasienTable.data('endpoint'),
    filterCols: [0, 1, 2],
    sortCols: { id_pasien: 'desc' },
    lengthMenu: [10, 25, 50, 100],
    columns: [
        { data: 'no_rm' }, { data: 'nik', defaultContent: '-' }, { data: 'nama' },
        { data: 'tanggal_lahir', defaultContent: '-' }, { data: 'jenis_kelamin', defaultContent: '-' },
        { data: 'status', render: function(data) { var cls = data === 'AKTIF' ? 'badge-success' : 'badge-danger'; return '<span class="badge ' + cls + '">' + data + '</span>'; } },
        { data: null, orderable: false, searchable: false, render: function(data) { return '<a href="' + editBase + '/' + data.id + '" class="btn btn-sm btn-primary" title="Ubah"><i class="fas fa-edit"></i></a>'; } }
    ]
});
$('.select2').select2();

