<?php defined('BASEPATH') || exit('No direct script access allowed');

/** Permission menu context LAPORAN CETAK (terpisah dari LAPORAN). */
class Migration_laporan_cetak extends Migration
{
    public function up()
    {
        $this->db->query("INSERT INTO permissions (nama_permission, modul) VALUES
            ('Laporan.Cetak.View', 'LAPORAN'),
            ('Site.Cetak.View', 'MANAJEMEN_SISTEM')
            ON CONFLICT (nama_permission) DO NOTHING");
        // Samakan dengan pemilik Laporan.Laporan.View (dinamis, bukan hardcode id role).
        $this->db->query("INSERT INTO role_permissions (id_role, id_permission)
            SELECT rp.id_role, p.id_permission
            FROM role_permissions rp
            JOIN permissions pl ON pl.id_permission = rp.id_permission AND pl.nama_permission = 'Laporan.Laporan.View'
            JOIN permissions p ON p.nama_permission IN ('Laporan.Cetak.View', 'Site.Cetak.View')
            ON CONFLICT (id_role, id_permission) DO NOTHING");
    }

    public function down()
    {
        $this->db->query("DELETE FROM role_permissions WHERE id_permission IN
            (SELECT id_permission FROM permissions WHERE nama_permission IN ('Laporan.Cetak.View', 'Site.Cetak.View'))");
        $this->db->query("DELETE FROM permissions WHERE nama_permission IN ('Laporan.Cetak.View', 'Site.Cetak.View')");
    }
}
