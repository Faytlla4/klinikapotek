$('#antrian_table').bfDataTable({
    url: site_url + 'admin/content/antrian/get_data',
    filterCols: [0, 1, 2, 3, 4],
    sortCols: { id_antrian: 'asc' },
    lengthMenu: [10, 25, 50, 100],
    columns: [
        { data: 'nomor_antrian' }, { data: 'no_rm' }, { data: 'nama_pasien' }, { data: 'nama_poli' },
        { data: 'status', render: function(data) { return '<span class="badge badge-info">' + data + '</span>'; } },
        { data: 'waktu_dipanggil', defaultContent: '-' }
    ]
});
