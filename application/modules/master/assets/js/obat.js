$('#obat_table').bfDataTable({
    url: site_url + 'admin/master/obat/get_data',
    targetUrl: site_url + 'admin/master/obat/edit',
    filterCols: [0, 1, 2, 3],
    sortCols: { id_obat: 'desc' },
    lengthMenu: [10, 25, 50, 100],
    columns: [
        { data: 'id_obat' },
        { data: 'kode_obat', defaultContent: '-' },
        { data: 'nama_obat' },
        { data: 'jenis_obat', defaultContent: '-' },
        { data: 'satuan', defaultContent: '-' },
        {
            data: 'harga',
            render: function (data) {
                return 'Rp ' + Number(data).toLocaleString('id-ID');
            }
        },
        {
            data: 'stok',
            render: function (data, type, row) {
                var stok = parseInt(data) || 0;
                var min  = parseInt(row.stok_minimum) || 0;
                var cls  = stok <= min ? 'text-danger font-weight-bold' : '';
                return '<span class="' + cls + '">' + stok + '</span>';
            }
        },
        { data: 'stok_minimum', defaultContent: '0' },
        {
            data: 'wajib_resep',
            render: function (data) {
                var ya = (data === true || data === 't' || data === 'true' || data === '1' || data === 1);
                return ya
                    ? '<span class="badge badge-warning">YA</span>'
                    : '<span class="badge badge-secondary">TIDAK</span>';
            }
        },
        {
            data: 'status',
            render: function (data) {
                var cls = data === 'AKTIF' ? 'badge-success' : 'badge-danger';
                return '<span class="badge ' + cls + '">' + data + '</span>';
            }
        }
    ]
});

$('.select2').select2({ theme: 'bootstrap4' });
