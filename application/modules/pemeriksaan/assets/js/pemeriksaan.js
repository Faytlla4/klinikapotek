var _p = location.pathname.split('/');
var ctxBase = _p.slice(0, _p.indexOf('admin') + 3).join('/');
$('#pemeriksaan_table').bfDataTable({
    url: ctxBase + '/get_data',
    targetUrl: ctxBase + '/detail',
    filterCols: [0, 1, 2, 3, 4, 5], sortCols: { id_pemeriksaan: 'desc' }, lengthMenu: [10, 25, 50, 100],
    columns: [
        { data: 'tanggal_pemeriksaan' }, { data: 'no_rm' }, { data: 'nama_pasien' }, { data: 'nama_dokter' }, { data: 'nama_poli' },
        { data: 'status', render: function(data) { return '<span class="badge badge-info">' + data + '</span>'; } }
    ]
});
$('.select2').select2({ theme: 'bootstrap4' });

