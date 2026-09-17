$('#sk_tidak_mampu_table').bfDataTable({
    url: site_url + 'admin/content/sk_tidak_mampu/get_data',
    targetUrl: site_url + 'admin/content/sk_tidak_mampu/edit',
    filterCols: [0, 1, 2],
    sortCols: { id: 'desc' },
    lengthMenu: [10, 100, 1000],
    columns: [
        { data: 'nama' },
        { data: 'alamat' },
        { data: 'jenis_surat' },
        { data: 'no_telepon' },
        { data: 'tanggal' },
    ],
});

$('#tanggal').dateDropper({
    modal: true,
    large: true
});

$('#user_lookup_table').bfDataTable({
    url: site_url + 'admin/content/sk_tidak_mampu/user_lookup',
    filterCols: [0, 1, 2],
    sortCols: 'id',
    lengthMenu: [5, 10, 20],
    select: { style: 'single' },
    params: {
        'test': 'saja',
        'aku': 'here'
    },
    columns: [
        { data: 'nama' },
        { data: 'alamat' },
        { data: 'jenis_surat' },
    ],
});

$('.lookup_modal').on('mouseup', function () {
    $('#' + $(this).data('target') + '_lookup_modal').modal('show');
});

$('.modal .save_lookup').on('mouseup', function () {
    const target = $(this).data('target');
    if ($(`#${target}_lookup_table tbody tr.selected`).length == 0) {
        Swal.fire('Peringatan', 'Mohon Pilih Data Terlebih Dahulu', 'warning');
        return false;
    } else {
        const data = $(`#${target}_lookup_table`).DataTable().ajax.json().data[
            $(`#${target}_lookup_table tbody tr.selected`).index()
        ];
        $(`#${target}_text`).val(data.nama);
        $(`#${target}`).val(data.id);
        $(`#${target}_lookup_modal`).modal('hide');
    }
});