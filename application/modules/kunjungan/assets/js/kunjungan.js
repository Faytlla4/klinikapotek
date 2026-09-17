$('#kunjungan_table').bfDataTable({
    url: site_url + 'admin/content/kunjungan/get_data',
    targetUrl: site_url + 'admin/content/kunjungan/detail',
    filterCols: [0, 1, 2, 3, 4, 5, 6],
    sortCols: { id_kunjungan: 'desc' },
    lengthMenu: [10, 25, 50, 100],
    columns: [
        { data: 'tanggal_kunjungan' }, { data: 'no_rm' }, { data: 'nama_pasien' },
        { data: 'nama_pelayanan' }, { data: 'nama_poli' }, { data: 'nama_dokter' },
        { data: 'nama_ruangan' }, { data: 'status', render: function(data) { return '<span class="badge badge-info">' + data + '</span>'; } }
    ]
});
$('.select2').select2({ theme: 'bootstrap4' });
