$('#pemeriksaan_table').bfDataTable({
    url: site_url + 'admin/content/pemeriksaan/get_data',
    targetUrl: site_url + 'admin/content/pemeriksaan/detail',
    filterCols: [0, 1, 2, 3, 4, 5], sortCols: { id_pemeriksaan: 'desc' }, lengthMenu: [10, 25, 50, 100],
    columns: [
        { data: 'tanggal_pemeriksaan' }, { data: 'no_rm' }, { data: 'nama_pasien' }, { data: 'nama_dokter' }, { data: 'nama_poli' },
        { data: 'status', render: function(data) { return '<span class="badge badge-info">' + data + '</span>'; } }
    ]
});
$('.select2').select2({ theme: 'bootstrap4' });
