<?php defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Kelola Role + matriks permission (skema custom apotek). Guard: kelola_role_permission.
 * Di-route dari admin/settings/roles menggantikan Bonfire legacy.
 */
class Roles extends App_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->auth->restrict('kelola_role_permission');
        $this->load->model('audit/audit_log_model');
        $this->form_validation->set_error_delimiters("<span class='error text-danger'>", "</span>");
    }

    public function index()
    {
        $roles = $this->db->order_by('id_role', 'ASC')->get('roles')->result();
        foreach ($roles as $r) {
            $r->jml_user = $this->db->where('id_role', $r->id_role)->count_all_results('user_roles');
            $r->jml_perm = $this->db->where('id_role', $r->id_role)->count_all_results('role_permissions');
        }
        Template::set('role_list', $roles);
        Template::set('toolbar_title', 'Role & Permission');
        Template::render();
    }

    public function create()
    {
        if ($this->input->post('save')) {
            $this->form_validation->set_rules(array(
                array('field' => 'nama_role', 'label' => 'Nama Role', 'rules' => 'required|trim|max_length[100]|is_unique[roles.nama_role]'),
            ));
            if ($this->form_validation->run() !== false) {
                $this->db->insert('roles', array('nama_role' => strtoupper($this->input->post('nama_role'))));
                $id = $this->db->insert_id();
                if ($id) {
                    $this->audit_log_model->catat($this->auth->user_id(), 'create', 'roles', $id, '');
                    Template::set_message('Role berhasil dibuat.', 'success');
                    redirect(SITE_AREA . '/settings/roles/matrix/' . $id);
                }
                Template::set_message('Gagal membuat role.', 'error');
            }
        }
        Template::set('toolbar_title', 'Tambah Role');
        Template::render();
    }

    /** Matriks permission per role (checkbox massal). */
    public function matrix($id = null)
    {
        $id = (int) $id > 0 ? (int) $id : 0;
        $role = $id ? $this->db->where('id_role', $id)->get('roles')->row() : false;
        if (! $role) {
            Template::set_message('Role tidak ditemukan.', 'error');
            redirect(SITE_AREA . '/settings/roles');
        }
        if ($this->input->post('save')) {
            $pilih = $this->input->post('perm');
            $pilih = is_array($pilih) ? array_map('intval', $pilih) : array();
            // Cegah admin mencabut hak kelola_role_permission dirinya sendiri.
            $my_role = $this->db->where('id_user', $this->auth->user_id())->get('user_roles')->row();
            if ($my_role && (int) $my_role->id_role === (int) $id) {
                $jaga = $this->db->where('nama_permission', 'kelola_role_permission')->get('permissions')->row();
                if ($jaga && ! in_array((int) $jaga->id_permission, $pilih)) {
                    $pilih[] = (int) $jaga->id_permission;
                    Template::set_message('Hak kelola_role_permission dipertahankan untuk role Anda.', 'attention');
                }
            }
            $this->db->trans_start();
            $this->db->where('id_role', $id)->delete('role_permissions');
            foreach ($pilih as $id_perm) {
                $this->db->insert('role_permissions', array('id_role' => (int) $id, 'id_permission' => $id_perm));
            }
            $this->db->trans_complete();
            if ($this->db->trans_status() !== false) {
                $this->audit_log_model->catat($this->auth->user_id(), 'update', 'role_permissions', $id, '');
                Template::set_message('Matriks permission disimpan.', 'success');
                redirect(SITE_AREA . '/settings/roles/matrix/' . $id);
            }
            Template::set_message('Gagal menyimpan matriks.', 'error');
        }
        $perms = $this->db->order_by('modul', 'ASC')->order_by('nama_permission', 'ASC')->get('permissions')->result();
        $punya = array();
        foreach ($this->db->where('id_role', $id)->get('role_permissions')->result() as $rp) {
            $punya[(int) $rp->id_permission] = true;
        }
        Template::set(array('role' => $role, 'perm_list' => $perms, 'punya' => $punya));
        Template::set('toolbar_title', 'Matriks Permission');
        Template::render();
    }
}
