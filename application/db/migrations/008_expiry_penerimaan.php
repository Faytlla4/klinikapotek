<?php defined('BASEPATH') || exit('No direct script access allowed');

/** Menyimpan batch dan tanggal kedaluwarsa pada setiap penerimaan obat. */
class Migration_expiry_penerimaan extends Migration
{
    public function up()
    {
        $this->db->query("ALTER TABLE penerimaan_obat_detail
            ADD COLUMN IF NOT EXISTS nomor_batch character varying(100),
            ADD COLUMN IF NOT EXISTS tanggal_kadaluarsa date");
        $this->db->query("CREATE INDEX IF NOT EXISTS idx_penerimaan_obat_detail_expiry
            ON penerimaan_obat_detail (tanggal_kadaluarsa)");
    }

    public function down()
    {
        $this->db->query('DROP INDEX IF EXISTS idx_penerimaan_obat_detail_expiry');
        $this->db->query('ALTER TABLE penerimaan_obat_detail DROP COLUMN IF EXISTS tanggal_kadaluarsa');
        $this->db->query('ALTER TABLE penerimaan_obat_detail DROP COLUMN IF EXISTS nomor_batch');
    }
}
