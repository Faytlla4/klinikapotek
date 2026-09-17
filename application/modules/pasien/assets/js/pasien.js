var _p = location.pathname.split('/');
var ctxBase = _p.slice(0, _p.indexOf('admin') + 3).join('/');
$('#pasien_table').bfDataTable({
    url: ctxBase + '/get_data',
    targetUrl: ctxBase + '/edit',
    filterCols: [0, 1, 2],
    sortCols: { id_pasien: 'desc' },
    lengthMenu: [10, 25, 50, 100],
    columns: [
        { data: 'no_rm' }, { data: 'nik', defaultContent: '-' }, { data: 'nama' },
        { data: 'tanggal_lahir', defaultContent: '-' }, { data: 'jenis_kelamin', defaultContent: '-' },
        { data: 'status', render: function(data) { var cls = data === 'AKTIF' ? 'badge-success' : 'badge-danger'; return '<span class="badge ' + cls + '">' + data + '</span>'; } }
    ]
});
$('.select2').select2({ theme: 'bootstrap4' });

