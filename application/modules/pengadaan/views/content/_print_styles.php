<style>
.pengadaan-print {
    display: none;
    color: #111;
    font-family: Arial, sans-serif;
    font-size: 10pt;
}
.pengadaan-print h1,
.pengadaan-print h2,
.pengadaan-print h3,
.pengadaan-print p {
    margin-top: 0;
}
.pengadaan-print .print-kop {
    text-align: center;
    border-bottom: 2px solid #222;
    margin-bottom: 18px;
    padding-bottom: 10px;
}
.pengadaan-print .print-kop h2 {
    margin-bottom: 4px;
    font-size: 18pt;
}
.pengadaan-print .print-title {
    margin-bottom: 16px;
    text-align: center;
    font-size: 14pt;
    font-weight: bold;
    text-transform: uppercase;
}
.pengadaan-print .print-info {
    width: 100%;
    margin-bottom: 16px;
    border-collapse: collapse;
}
.pengadaan-print .print-info th,
.pengadaan-print .print-info td {
    padding: 4px 6px;
    text-align: left;
    vertical-align: top;
}
.pengadaan-print .print-info th {
    width: 150px;
}
.pengadaan-print .print-items {
    width: 100%;
    margin: 10px 0 18px;
    border-collapse: collapse;
}
.pengadaan-print .print-items th,
.pengadaan-print .print-items td {
    border: 1px solid #555;
    padding: 6px;
    vertical-align: top;
}
.pengadaan-print .print-items th {
    background: #eee !important;
    text-align: center;
}
.pengadaan-print .print-right {
    text-align: right;
}
.pengadaan-print .print-center {
    text-align: center;
}
.pengadaan-print .print-signatures {
    display: flex;
    justify-content: space-between;
    margin-top: 36px;
    text-align: center;
}
.pengadaan-print .print-signature {
    width: 42%;
}
.pengadaan-print .print-signature-space {
    height: 68px;
}
.pengadaan-print .print-muted {
    color: #444;
}
@media print {
    @page {
        size: A4 portrait;
        margin: 14mm;
    }
    .main-sidebar,
    .main-header,
    .main-footer,
    .content-header,
    .breadcrumb,
    .pengadaan-screen,
    .nota-print {
        display: none !important;
    }
    .content-wrapper {
        margin: 0 !important;
        background: #fff !important;
    }
    .content {
        padding: 0 !important;
    }
    .pengadaan-print {
        display: block !important;
    }
    .pengadaan-print .print-items tr {
        page-break-inside: avoid;
    }
}
</style>
