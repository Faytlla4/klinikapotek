$('#pengadaan_table').bfDataTable({url: site_url + 'admin/content/pengadaan/get_data', filterCols: [0, 1, 2, 3, 4], sortCols: {id_pengadaan: 'desc'}, lengthMenu: [10, 25, 50], columns: [{data: 'nomor_pengadaan'}, {data: 'tanggal_pesanan'}, {data: 'nama_supplier'}, {data: 'total'}, {data: 'status'}]});
$('.select2').select2({theme: 'bootstrap4'});
