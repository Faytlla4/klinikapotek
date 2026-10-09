ALTER TABLE obat
    ADD COLUMN IF NOT EXISTS kategori character varying(100),
    ADD COLUMN IF NOT EXISTS bentuk_sediaan character varying(100),
    ADD COLUMN IF NOT EXISTS kandungan character varying(200),
    ADD COLUMN IF NOT EXISTS produsen character varying(150),
    ADD COLUMN IF NOT EXISTS id_supplier_utama BIGINT REFERENCES supplier(id_supplier),
    ADD COLUMN IF NOT EXISTS harga_satuan NUMERIC(15,2) NOT NULL DEFAULT 0,
    ADD COLUMN IF NOT EXISTS harga_jual NUMERIC(15,2) NOT NULL DEFAULT 0;

UPDATE obat SET harga_satuan = harga WHERE harga_satuan = 0;
UPDATE obat SET harga_jual = harga WHERE harga_jual = 0;
