$('#spesialis_table').bfDataTable({
    url: site_url + 'admin/master/spesialis/get_data',
    targetUrl: site_url + 'admin/master/spesialis/edit',
    filterCols: [0, 1],
    sortCols: { id_spesialis: 'desc' },
    lengthMenu: [10, 25, 50, 100],
    columns: [
        { data: 'id_spesialis' },
        { data: 'nama_spesialis' },
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
