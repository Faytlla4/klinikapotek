<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">Antrian Hari Ini</h3>
            </div>
            <div class="card-body table-responsive">
                <table id="antrian_table" class="table table-bordered table-hover table-striped">
                    <thead>
                        <tr>
                            <th>Nomor</th>
                            <th>No. RM</th>
                            <th>Pasien</th>
                            <th>Poli</th>
                            <th>Status</th>
                            <th>Waktu Dipanggil</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ubah Status -->
<div class="modal fade" id="modal_status" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white"><i class="fas fa-exchange-alt"></i> Ubah Status Antrian</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p>Pasien: <strong id="modal_nama_pasien"></strong></p>
                <p>Nomor Antrian: <strong id="modal_nomor"></strong></p>
                <p>Status Saat Ini: <strong id="modal_status_lama"></strong></p>
                <hr>
                <p class="mb-2"><strong>Ubah ke:</strong></p>
                <div id="modal_btn_status" class="d-flex flex-wrap" style="gap:8px"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
