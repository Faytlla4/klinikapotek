<?php defined('BASEPATH') || exit('No direct script access allowed');

class Migration_master_permission_individual extends Migration
{
    public function up()
    {
        $this->db->query("
            INSERT INTO permissions (nama_permission, modul)
            VALUES
                ('Master.Pelayanan.Manage', 'MASTER'),
                ('Master.Spesialis.Manage', 'MASTER'),
                ('Master.Dokter.Manage', 'MASTER'),
                ('Master.Poli.Manage', 'MASTER'),
                ('Master.Ruangan.Manage', 'MASTER'),
                ('Master.Obat.Manage', 'MASTER'),
                ('Master.Satuan.Manage', 'MASTER'),
                ('Master.Supplier.Manage', 'MASTER'),
                ('Master.Master.View', 'MASTER'),
                ('Site.Master.View', 'MANAJEMEN_SISTEM')
            ON CONFLICT (nama_permission) DO NOTHING
        ");

        // Izin master selain Supplier diberikan kepada role yang memiliki izin lama.
        $this->db->query("
            INSERT INTO role_permissions (id_role, id_permission)
            SELECT DISTINCT old_rp.id_role, new_p.id_permission
            FROM role_permissions old_rp
            INNER JOIN permissions old_p
                ON old_p.id_permission = old_rp.id_permission
                AND old_p.nama_permission = 'kelola_master_data'
            INNER JOIN permissions new_p
                ON new_p.nama_permission IN (
                    'Master.Pelayanan.Manage',
                    'Master.Spesialis.Manage',
                    'Master.Dokter.Manage',
                    'Master.Poli.Manage',
                    'Master.Ruangan.Manage',
                    'Master.Obat.Manage',
                    'Master.Satuan.Manage'
                )
            ON CONFLICT (id_role, id_permission) DO NOTHING
        ");

        // Pertahankan akses master Obat yang sebelumnya berasal dari izin stok.
        $this->db->query("
            INSERT INTO role_permissions (id_role, id_permission)
            SELECT DISTINCT old_rp.id_role, new_p.id_permission
            FROM role_permissions old_rp
            INNER JOIN permissions old_p
                ON old_p.id_permission = old_rp.id_permission
                AND old_p.nama_permission = 'kelola_stok_obat'
            INNER JOIN permissions new_p
                ON new_p.nama_permission = 'Master.Obat.Manage'
            ON CONFLICT (id_role, id_permission) DO NOTHING
        ");

        // Supplier berada di menu MASTER, tetapi sebelumnya aksesnya memakai kelola_pengadaan.
        $this->db->query("
            INSERT INTO role_permissions (id_role, id_permission)
            SELECT DISTINCT old_rp.id_role, new_p.id_permission
            FROM role_permissions old_rp
            INNER JOIN permissions old_p
                ON old_p.id_permission = old_rp.id_permission
                AND old_p.nama_permission = 'kelola_pengadaan'
            INNER JOIN permissions new_p
                ON new_p.nama_permission = 'Master.Supplier.Manage'
            ON CONFLICT (id_role, id_permission) DO NOTHING
        ");

        // Izin konteks diperlukan agar navigasi MASTER dapat ditampilkan.
        $this->db->query("
            INSERT INTO role_permissions (id_role, id_permission)
            SELECT DISTINCT old_rp.id_role, context_p.id_permission
            FROM role_permissions old_rp
            INNER JOIN permissions old_p
                ON old_p.id_permission = old_rp.id_permission
                AND old_p.nama_permission IN (
                    'kelola_master_data',
                    'kelola_stok_obat',
                    'kelola_pengadaan'
                )
            INNER JOIN permissions context_p
                ON context_p.nama_permission IN (
                    'Master.Master.View',
                    'Site.Master.View'
                )
            ON CONFLICT (id_role, id_permission) DO NOTHING
        ");
    }

    public function down()
    {
        $this->db->query("
            DELETE FROM role_permissions
            WHERE id_permission IN (
                SELECT id_permission
                FROM permissions
                WHERE nama_permission IN (
                    'Master.Pelayanan.Manage',
                    'Master.Spesialis.Manage',
                    'Master.Dokter.Manage',
                    'Master.Poli.Manage',
                    'Master.Ruangan.Manage',
                    'Master.Obat.Manage',
                    'Master.Satuan.Manage',
                    'Master.Supplier.Manage'
                )
            )
        ");

        $this->db->query("
            DELETE FROM permissions
            WHERE nama_permission IN (
                'Master.Pelayanan.Manage',
                'Master.Spesialis.Manage',
                'Master.Dokter.Manage',
                'Master.Poli.Manage',
                'Master.Ruangan.Manage',
                'Master.Obat.Manage',
                'Master.Satuan.Manage',
                'Master.Supplier.Manage'
            )
        ");
    }
}