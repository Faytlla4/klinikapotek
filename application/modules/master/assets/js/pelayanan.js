$('#pelayanan_table').bfDataTable({
    url: site_url + 'admin/master/pelayanan/get_data',
    targetUrl: site_url + 'admin/master/pelayanan/edit',
    filterCols: [0, 1, 2],
    sortCols: { id_pelayanan: 'desc' },
    lengthMenu: [10, 25, 50, 100],
    columns: [
        { data: 'id_pelayanan' },
        { data: 'nama_pelayanan' },
        { data: 'jenis_pelayanan' },
        { 
            data: 'tarif',
            render: function(data) {
                return 'Rp ' + Number(data).toLocaleString('id-ID');
            }
        },
        { 
            data: 'status',
            render: function(data) {
                var cls = data === 'AKTIF' ? 'badge-success' : 'badge-danger';
                return '<span class="badge ' + cls + '">' + data + '</span>';
            }
        }
    ]
});

$('.select2').select2({
    theme: 'bootstrap4'
});
