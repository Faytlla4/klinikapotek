$(document).ready(function () {
    var _p = location.pathname.split('/');
    var ctxBase = _p.slice(0, _p.indexOf('admin') + 3).join('/');

    $('#transaksi_table').bfDataTable({
        url: ctxBase + '/get_data',
        targetUrl: ctxBase + '/detail',
        filterCols: [0, 1, 2, 3],
        sortCols: { id_transaksi: 'desc' },
        lengthMenu: [10, 25, 50],
        columns: [
            { data: 'nomor_transaksi' },
            { data: 'tanggal_transaksi' },
            {
                data: 'total',
                render: function (data) {
                    return 'Rp ' + Number(data).toLocaleString('id-ID');
                }
            },
            {
                data: 'status',
                render: function (data) {
                    var cls = data === 'LUNAS' ? 'badge-success' : (data === 'BATAL' ? 'badge-danger' : 'badge-warning');
                    return '<span class="badge ' + cls + '">' + data + '</span>';
                }
            }
        ]
    });
});
