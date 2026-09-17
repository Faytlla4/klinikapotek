var _p = location.pathname.split('/');
var ctxBase = _p.slice(0, _p.indexOf('admin') + 3).join('/');
$('#tagihan_table').bfDataTable({url: ctxBase + '/get_data', targetUrl: ctxBase + '/detail', filterCols: [0, 1, 2, 3, 4, 5], sortCols: {id_tagihan: 'desc'}, lengthMenu: [10, 25, 50], columns: [{data: 'nomor_tagihan'}, {data: 'tanggal_tagihan'}, {data: 'no_rm'}, {data: 'nama_pasien'}, {data: 'total'}, {data: 'status'}]});

