<?php defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Migration relasi user -> dokter (role DOKTER).
 *
 * Alasan: tabel dokter belum punya id_user sehingga dokter yang login tidak
 * bisa dipetakan ke baris dokternya. Akibatnya filter "antrian dokternya
 * sendiri" mustahil tanpa menebak nama. Kolom nullable + SET NULL agar data
 * dokter lama tetap valid; backfill mengaitkan dr. Contoh ke user dokter.
 */
class Migration_dokter_user extends Migration
{
    public function up()
    {
        $this->db->query("ALTER TABLE dokter ADD COLUMN IF NOT EXISTS id_user BIGINT REFERENCES users(id_user) ON UPDATE CASCADE ON DELETE SET NULL");
        $this->db->query("CREATE INDEX IF NOT EXISTS idx_dokter_user ON dokter(id_user)");
        $this->db->query("UPDATE dokter SET id_user = 3 WHERE id_dokter = 1 AND id_user IS NULL");
    }

    public function down()
    {
        $this->db->query("DROP INDEX IF EXISTS idx_dokter_user");
        $this->db->query("ALTER TABLE dokter DROP COLUMN IF EXISTS id_user");
    }
}
