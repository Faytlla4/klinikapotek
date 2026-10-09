<?php defined('BASEPATH') || exit('No direct script access allowed');

/** Entry point konteks Master. */
class Master extends App_Controller
{
    public function index()
    {
        $masters = array(
            array('Master.Pelayanan.Manage', 'pelayanan'),
            array('Master.Spesialis.Manage', 'spesialis'),
            array('Master.Dokter.Manage', 'dokter'),
            array('Master.Poli.Manage', 'poli'),
            array('Master.Ruangan.Manage', 'ruangan'),
            array('Master.Obat.Manage', 'obat'),
            array('Master.Satuan.Manage', 'satuan'),
            array('Master.Supplier.Manage', 'pengadaan/supplier')
        );

        foreach ($masters as $master) {
            if ($this->auth->has_permission($master[0])) {
                redirect(SITE_AREA . '/master/' . $master[1]);
                return;
            }
        }

        $this->auth->restrict('Master.Pelayanan.Manage');
    }
}