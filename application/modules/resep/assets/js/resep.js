var _p = location.pathname.split('/');
var ctxBase = _p.slice(0, _p.indexOf('admin') + 3).join('/');
$('#resep_table').bfDataTable({url: ctxBase + '/get_data', targetUrl: ctxBase + '/detail', filterCols: [0, 1, 2, 3, 4], sortCols: {id_resep: 'asc'}, lengthMenu: [10, 25, 50], columns: [{data: 'nomor_resep'}, {data: 'tanggal_resep'}, {data: 'nama_pasien'}, {data: 'nama_dokter'}, {data: 'status'}]});

