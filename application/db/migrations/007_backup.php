<?php defined('BASEPATH') || exit('No direct script access allowed');

/** Permission menu BACKUP DATABASE (context MANAJEMEN SISTEM). */
class Migration_backup extends Migration
{
    public function up()
    {
        $this->db->query("INSERT INTO permissions (nama_permission, modul) VALUES
            ('Backup.Settings.View', 'MANAJEMEN_SISTEM')
            ON CONFLICT (nama_permission) DO NOTHING");
        // Samakan dengan pemilik Site.Settings.View (dinamis, bukan hardcode id role).
        $this->db->query("INSERT INTO role_permissions (id_role, id_permission)
            SELECT rp.id_role, p.id_permission
            FROM role_permissions rp
            JOIN permissions pl ON pl.id_permission = rp.id_permission AND pl.nama_permission = 'Site.Settings.View'
            JOIN permissions p ON p.nama_permission = 'Backup.Settings.View'
            ON CONFLICT (id_role, id_permission) DO NOTHING");
    }

    public function down()
    {
        $this->db->query("DELETE FROM role_permissions WHERE id_permission IN
            (SELECT id_permission FROM permissions WHERE nama_permission = 'Backup.Settings.View')");
        $this->db->query("DELETE FROM permissions WHERE nama_permission = 'Backup.Settings.View'");
    }
}
