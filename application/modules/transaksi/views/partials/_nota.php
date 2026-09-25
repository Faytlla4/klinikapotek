<?php /* Bahasa nota terpadu untuk semua dokumen cetak. Muat sekali di atas halaman. */ ?>
<style>
.nota { max-width: 460px; margin: 0 auto; background: #fff; }
.nota-head { text-align: center; border-bottom: 2px dashed #94a3b8; padding-bottom: 10px; margin-bottom: 10px; }
.nota-head h4 { margin: 0; color: #064e3b; font-weight: 800; }
.nota-head p { margin: 0; font-size: .8rem; color: #64748b; }
.nota-row { display: flex; justify-content: space-between; font-size: .9rem; padding: 1px 0; }
.nota-sep { border-top: 1px dashed #94a3b8; margin: 8px 0; }
.nota-total { font-size: 1.1rem; font-weight: 800; }
.nota-foot { text-align: center; font-size: .8rem; color: #64748b; border-top: 2px dashed #94a3b8; margin-top: 10px; padding-top: 10px; }
.nota-print { display: none; }
@media print {
    .main-sidebar, .main-header, .main-footer, .content-header,
    .card-footer, .card-tools, .breadcrumb, .nota-aksi { display: none !important; }
    .content-wrapper { margin: 0 !important; background: #fff !important; }
    .content { padding: 0 !important; }
    .card { border: none !important; box-shadow: none !important; }
    .nota { max-width: 100%; }
    .nota-screen { display: none !important; }
    .nota-print { display: block !important; }
}
</style>
