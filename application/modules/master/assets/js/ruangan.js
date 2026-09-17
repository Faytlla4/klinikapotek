$('#ruangan_table').bfDataTable({
    url: site_url + 'admin/master/ruangan/get_data',
    targetUrl: site_url + 'admin/master/ruangan/edit',
    filterCols: [0, 1, 2],
    sortCols: { id_ruangan: 'desc' },
    lengthMenu: [10, 25, 50, 100],
    columns: [
        { data: 'id_ruangan' },
        { data: 'nama_ruangan' },
        { 
            data: 'nama_poli',
            render: function(data) {
                return data ? data : '<span class="text-muted">-</span>';
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
