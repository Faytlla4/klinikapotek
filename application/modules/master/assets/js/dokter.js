$('#dokter_table').bfDataTable({
    url: site_url + 'admin/master/dokter/get_data',
    targetUrl: site_url + 'admin/master/dokter/edit',
    filterCols: [0, 1, 2, 3],
    sortCols: { id_dokter: 'desc' },
    lengthMenu: [10, 25, 50, 100],
    columns: [
        { data: 'id_dokter' },
        { data: 'nama_dokter' },
        { data: 'nama_spesialis', defaultContent: '-' },
        { data: 'no_sip', defaultContent: '-' },
        { data: 'tarif', render: function(data) { return 'Rp ' + Number(data).toLocaleString('id-ID'); } },
        { data: 'status', render: function(data) {
            var cls = data === 'AKTIF' ? 'badge-success' : 'badge-danger';
            return '<span class="badge ' + cls + '">' + data + '</span>';
        } }
    ]
});
$('.select2').select2({ theme: 'bootstrap4' });
