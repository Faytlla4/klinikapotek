<?php defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Migration relasi user -> pasien (role PASIEN / portal pasien).
 *
 * Pasien yang login dipetakan ke baris pasiennya agar portal hanya
 * menampilkan data miliknya sendiri. Kolom nullable + SET NULL.
 */
class Migration_pasien_user extends Migration
{
    public function up()
    {
        $this->db->query("ALTER TABLE pasien ADD COLUMN IF NOT EXISTS id_user BIGINT REFERENCES users(id_user) ON UPDATE CASCADE ON DELETE SET NULL");
        $this->db->query("CREATE INDEX IF NOT EXISTS idx_pasien_user ON pasien(id_user)");
    }

    public function down()
    {
        $this->db->query("DROP INDEX IF EXISTS idx_pasien_user");
        $this->db->query("ALTER TABLE pasien DROP COLUMN IF EXISTS id_user");
    }
}
