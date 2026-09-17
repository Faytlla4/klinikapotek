$('#poli_table').bfDataTable({
    url: site_url + 'admin/master/poli/get_data',
    targetUrl: site_url + 'admin/master/poli/edit',
    filterCols: [0, 1],
    sortCols: { id_poli: 'desc' },
    lengthMenu: [10, 25, 50, 100],
    columns: [
        { data: 'id_poli' },
        { data: 'nama_poli' },
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
