var _p = location.pathname.split('/');
var ctxBase = _p.slice(0, _p.indexOf('admin') + 3).join('/');
$('#stok_table').bfDataTable({url: ctxBase + '/get_data', filterCols: [0, 1, 2, 3, 4], sortCols: {id_obat: 'desc'}, lengthMenu: [10, 25, 50], columns: [{data: 'kode_obat'}, {data: 'nama_obat'}, {data: 'satuan'}, {data: 'stok'}, {data: 'stok_minimum'}, {data: 'status'}]});

