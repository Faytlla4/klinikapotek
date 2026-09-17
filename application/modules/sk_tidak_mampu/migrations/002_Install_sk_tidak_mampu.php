<?php defined('BASEPATH') || exit('No direct script access allowed');

class Migration_Install_sk_tidak_mampu extends Migration
{
	/**
	 * @var string The name of the database table
	 */
	private $table_name = 'sk_tidak_mampu';

	/**
	 * @var array The table's fields
	 */
	private $fields = array(
	'id' => array(
		'type'       => 'INT',
		'constraint' => 11,
		'auto_increment' => true,
	),
        'nama' => array(
            'type'       => 'VARCHAR',
            'constraint' => 30,
            'null'       => false,
        ),
        'alamat' => array(
            'type'       => 'TEXT',
            'null'       => false,
        ),
        'jenis_surat' => array(
            'type'       => 'VARCHAR',
            'constraint' => 30,
            'null'       => false,
        ),
        'no_telepon' => array(
            'type'       => 'INT',
            'constraint' => 16,
            'null'       => false,
        ),
        'tanggal' => array(
            'type'       => 'DATE',
            'null'       => false,
        ),
        'deleted' => array(
            'type'       => 'TINYINT',
            'constraint' => 1,
            'default'    => '0',
        ),
		'deleted_on' => array(
			'type'       => 'TIMESTAMP',
		),
        'deleted_by' => array(
            'type'       => 'BIGINT',
            'constraint' => 20,
            'null'       => true,
        ),
        'created_on' => array(
            'type'       => 'TIMESTAMP',
            'default'    => 'NOW()',
        ),
        'created_by' => array(
            'type'       => 'BIGINT',
            'constraint' => 20,
            'null'       => false,
        ),
        'modified_on' => array(
            'type'       => 'TIMESTAMP',
        ),
        'modified_by' => array(
            'type'       => 'BIGINT',
            'constraint' => 20,
            'null'       => true,
        ),
	);

	/**
	 * Install this version
	 *
	 * @return void
	 */
	public function up()
	{
		$this->dbforge->add_field($this->fields);
		$this->dbforge->add_key('id', true);
		$this->dbforge->create_table($this->table_name);
	}

	/**
	 * Uninstall this version
	 *
	 * @return void
	 */
	public function down()
	{
		$this->dbforge->drop_table($this->table_name);
	}
}