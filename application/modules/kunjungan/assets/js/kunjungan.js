$(function () {
var _p = location.pathname.split('/');
var ctxBase = _p.slice(0, _p.indexOf('admin') + 3).join('/');

if ($('#kunjungan_table').length) {
$('#kunjungan_table').bfDataTable({
    url: ctxBase + '/get_data',
    ajax: {
        url: ctxBase + '/get_data',
        data: function (data) {
            data.length = $('select', '#kunjungan_table_length').val();
            data.search = data.search || {};
            data.search.value = $('#kunjungan_table_filter input').val();
            var dari = $('#filter-dari'), sampai = $('#filter-sampai');
            if (dari.length) { data.dari = dari.val(); data.sampai = sampai.val(); }
        }
    },
    filterCols: [0, 1, 2, 3, 4, 5, 6],
    sortCols: { id_kunjungan: 'desc' },
    lengthMenu: [10, 25, 50, 100],
    columns: [
        { data: 'tanggal_kunjungan' }, { data: 'no_rm' }, { data: 'nama_pasien' },
        { data: 'nama_pelayanan' }, { data: 'nama_poli' }, { data: 'nama_dokter' },
        { data: 'nama_ruangan' }, { data: 'status', render: function(data) { return '<span class="badge badge-info">' + data + '</span>'; } },
        { data: null, orderable: false, searchable: false, render: function(data) { return '<a href="' + ctxBase + '/detail/' + data.id + '" class="btn btn-sm btn-primary" title="Lihat Detail"><i class="fas fa-eye"></i></a>'; } }
    ]
});
}

$('.select2').select2();

$('#filter-dari, #filter-sampai').on('change', function () {
    if ($('#kunjungan_table').length) { $('#kunjungan_table').DataTable().ajax.reload(); }
});
$('#filter-reset').on('click', function () {
    $('#filter-dari').val('');
    $('#filter-sampai').val('');
    if ($('#kunjungan_table').length) { $('#kunjungan_table').DataTable().ajax.reload(); }
});

// --- Pasien Quick Lookup ---
var searchTimer = null;
var pasienSearchOffset = 0;
var pasienSearchQuery = '';
var pasienSearchLoading = false;
var pasienSearchRequest = null;
var pasienSearchVersion = 0;

function esc(s) {
    return String(s === null || s === undefined ? '' : s)
        .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

function renderPasienResults(data, append) {
    var $results = $('#pasien-results');
    if (!append) { $results.empty(); }
    if (!data || data.length === 0) {
        if (!append) { $results.append('<div class="list-group-item text-muted">Pasien tidak ditemukan.</div>'); }
        $results.show();
        return;
    }
    $.each(data, function (i, p) {
        var label = '<strong>' + esc(p.nama) + '</strong> <small class="text-muted">(' + esc(p.no_rm) + ')</small>';
        if (p.nik) { label += ' <small>NIK: ' + esc(p.nik) + '</small>'; }
        $results.append(
            '<button type="button" class="list-group-item list-group-item-action pasien-pick" ' +
            'data-id="' + p.id_pasien + '" data-nama="' + esc(p.nama) + '" data-rm="' + esc(p.no_rm) + '" ' +
            'data-nik="' + esc(p.nik) + '" data-hp="' + esc(p.no_hp) + '" data-alamat="' + esc(p.alamat) + '" data-jk="' + esc(p.jenis_kelamin || '') + '">' +
            label + '</button>'
        );
    });
    $results.show();
}

function cariPasien(q, append) {
    var $results = $('#pasien-results');
    if (append && pasienSearchLoading) { return; }
    var requestVersion = append ? pasienSearchVersion : ++pasienSearchVersion;
    if (!append && pasienSearchRequest) { pasienSearchRequest.abort(); }
    pasienSearchLoading = true;
    pasienSearchRequest = $.getJSON(ctxBase + '/cari_pasien', { q: q, offset: append ? pasienSearchOffset : 0 }, function (response) {
        if (requestVersion !== pasienSearchVersion) { return; }
        var data = response && response.data ? response.data : [];
        if (!append) { pasienSearchOffset = 0; }
        pasienSearchOffset += data.length;
        renderPasienResults(data, append);
        $results.find('#pasien-load-more').remove();
        if (response && response.has_more) {
            $results.append('<button type="button" id="pasien-load-more" class="list-group-item list-group-item-action text-center">Muat lebih banyak pasien</button>');
        }
    }).fail(function (xhr) {
        if (xhr.statusText === 'abort') { return; }
        $results.empty().append('<div class="list-group-item text-danger">Gagal mencari (HTTP ' + xhr.status + '). Coba lagi.</div>').show();
    }).always(function () {
        if (requestVersion === pasienSearchVersion) { pasienSearchLoading = false; }
    });
}

$('#pasien-search').on('input', function () {
    var q = $(this).val().trim();
    pasienSearchQuery = q;
    clearTimeout(searchTimer);
    searchTimer = setTimeout(function () { cariPasien(q, false); }, 150);
});

$('#pasien-search').on('focus click', function () {
    var q = $(this).val().trim();
    if (pasienSearchQuery !== q || !$('#pasien-results').children().length) {
        pasienSearchQuery = q;
        cariPasien(q, false);
    }
});

$(document).on('click', '#pasien-load-more', function (e) {
    e.preventDefault();
    cariPasien(pasienSearchQuery, true);
});

// Sembunyikan hasil saat klik di luar / tekan Escape
$(document).on('click', function (e) {
    if (!$(e.target).closest('#pasien-search-box').length) { $('#pasien-results').hide(); }
});
$(document).on('keydown', function (e) {
    if (e.key === 'Escape') { $('#pasien-results').hide(); }
});

$(document).on('click', '.pasien-pick', function (e) {
    e.preventDefault();
    var $btn = $(this);
    $('#id_pasien').val($btn.data('id'));
    $('#pasien-selected-text').val($btn.data('rm') + ' — ' + $btn.data('nama'));
    $('#pasien-search-box').addClass('d-none');
    $('#pasien-selected').removeClass('d-none');
    var info = $btn.data('nama') + ' (' + $btn.data('rm') + ')';
    if ($btn.data('nik')) info += ' | NIK: ' + $btn.data('nik');
    $('#pasien-info-text').text(info);
    $('#pasien-no-hp').val($btn.data('hp') || '-');
    $('#pasien-jk').val($btn.data('jk') || '-');
    $('#pasien-alamat').val($btn.data('alamat') || '-');
    $('#pasien-info').removeClass('d-none');
    $('#pasien-results').hide().empty();
    loadRiwayat($btn.data('id'));
});

$('#pasien-clear-btn').on('click', function () {
    $('#id_pasien').val('');
    $('#pasien-selected').addClass('d-none');
    $('#pasien-search-box').removeClass('d-none');
    $('#pasien-search').val('').focus();
    $('#pasien-info').addClass('d-none');
    $('#pasien-no-hp, #pasien-jk, #pasien-alamat').val('');
    $('#riwayat_container').addClass('d-none');
    $('#riwayat_body').empty();
});

$(document).on('click', '#btn-daftar-baru', function (e) {
    e.preventDefault();
    window.location.href = ctxBase.replace('/kunjungan', '/pasien/create');
});

// --- Riwayat kunjungan ---
function loadRiwayat(id_pasien) {
    var $container = $('#riwayat_container');
    var $tbody = $('#riwayat_body');
    $.getJSON(ctxBase + '/get_riwayat/' + id_pasien, function (res) {
        $tbody.empty();
        if (!res.status || !res.data || res.data.length === 0) {
            $tbody.append('<tr><td colspan="5" class="text-center text-muted">Belum ada riwayat kunjungan.</td></tr>');
        } else {
            $.each(res.data, function (i, row) {
                var badgeClass = {
                    'TERDAFTAR': 'badge-primary',
                    'MENUNGGU' : 'badge-warning',
                    'DIPROSES' : 'badge-info',
                    'SELESAI'  : 'badge-success',
                    'BATAL'    : 'badge-danger'
                }[row.status] || 'badge-secondary';
                $tbody.append(
                    '<tr><td>' + (row.tanggal_kunjungan || '-') + '</td><td>' + (row.nama_pelayanan || '-') + '</td><td>' + (row.nama_poli || '-') + '</td><td>' + (row.nama_dokter || '-') + '</td><td><span class="badge ' + badgeClass + '">' + (row.status || '-') + '</span></td></tr>'
                );
            });
        }
        $container.removeClass('d-none');
    }).fail(function () { $container.addClass('d-none'); });
}

// Jika sudah ada id_pasien (validation error), load riwayat
if ($('#id_pasien').val()) {
    loadRiwayat($('#id_pasien').val());
}
});
