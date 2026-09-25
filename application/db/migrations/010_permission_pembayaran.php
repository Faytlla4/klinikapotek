<?php defined('BASEPATH') || exit('No direct script access allowed');

class Migration_permission_pembayaran extends Migration
{
    public function up()
    {
        /*
         * Permission view khusus menu Pembayaran.
         */
        $this->db->query("
            INSERT INTO permissions (nama_permission, modul)
            VALUES ('Pembayaran.Transaksi.View', 'TRANSAKSI')
            ON CONFLICT (nama_permission) DO NOTHING
        ");

        /*
         * Semua role yang memiliki kelola_pembayaran
         * otomatis mendapatkan permission view pembayaran.
         */
        $this->db->query("
            INSERT INTO role_permissions (id_role, id_permission)
            SELECT
                rp.id_role,
                p_view.id_permission
            FROM role_permissions rp
            INNER JOIN permissions p_action
                ON p_action.id_permission = rp.id_permission
                AND p_action.nama_permission = 'kelola_pembayaran'
            INNER JOIN permissions p_view
                ON p_view.nama_permission = 'Pembayaran.Transaksi.View'
            ON CONFLICT (id_role, id_permission) DO NOTHING
        ");
    }

    public function down()
    {
        $this->db->query("
            DELETE FROM role_permissions
            WHERE id_permission = (
                SELECT id_permission
                FROM permissions
                WHERE nama_permission = 'Pembayaran.Transaksi.View'
            )
        ");

        $this->db->query("
            DELETE FROM permissions
            WHERE nama_permission = 'Pembayaran.Transaksi.View'
        ");
    }
}