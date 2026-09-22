<?php defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Kelola User (skema custom apotek). Guard: kelola_user.
 * Di-route dari admin/settings/users menggantikan Bonfire legacy.
 */
class Users extends App_Controller
{
    public function __construct()
    {
        parent::__construct();
        // ponytail: profile milik sendiri boleh semua role login; kelola user lain tetap butuh kelola_user.
        if ($this->router->fetch_method() !== 'profile') {
            $this->auth->restrict('kelola_user');
        }
        $this->load->model('audit/audit_log_model');
        $this->form_validation->set_error_delimiters("<span class='error text-danger'>", "</span>");
    }

    public function index()
    {
        $q = trim($this->input->get('q') ?: '');
        $this->db->select('users.*, roles.nama_role')
            ->join('user_roles', 'user_roles.id_user = users.id_user', 'left')
            ->join('roles', 'roles.id_role = user_roles.id_role', 'left')
            ->order_by('users.id_user', 'ASC');
        if ($q !== '') {
            $this->db->group_start()
                ->where("users.username LIKE '%" . $this->db->escape_like_str($q) . "%'", NULL, FALSE)
                ->or_where("users.nama LIKE '%" . $this->db->escape_like_str($q) . "%'", NULL, FALSE)
                ->group_end();
        }
        Template::set(array('user_list' => $this->db->get('users')->result(), 'q' => $q));
        Template::set('toolbar_title', 'User');
        Template::render();
    }

    public function create()
    {
        if ($this->input->post('save')) {
            $this->form_validation->set_rules(array(
                array('field' => 'username', 'label' => 'Username', 'rules' => 'required|trim|min_length[3]|max_length[100]|is_unique[users.username]'),
                array('field' => 'nama', 'label' => 'Nama', 'rules' => 'required|trim|max_length[150]'),
                array('field' => 'password', 'label' => 'Password', 'rules' => 'required|min_length[6]'),
                array('field' => 'id_role', 'label' => 'Role', 'rules' => 'required|integer'),
            ));
            if ($this->form_validation->run() !== false) {
                $hash = $this->auth->hash_password($this->input->post('password'), 8);
                if (empty($hash['hash'])) {
                    Template::set_message('Gagal membuat hash password.', 'error');
                } else {
                    $this->db->trans_start();
                    $this->db->insert('users', array(
                        'username' => $this->input->post('username'),
                        'password' => $hash['hash'],
                        'nama' => $this->input->post('nama'),
                        'status' => 'AKTIF',
                    ));
                    $id_user = $this->db->insert_id();
                    if ($id_user) {
                        $this->db->insert('user_roles', array('id_user' => $id_user, 'id_role' => (int) $this->input->post('id_role')));
                    }
                    $this->db->trans_complete();
                    if ($this->db->trans_status() !== false && $id_user) {
                        $this->audit_log_model->catat($this->auth->user_id(), 'create', 'users', $id_user, '');
                        Template::set_message('User berhasil dibuat.', 'success');
                        redirect(SITE_AREA . '/settings/users');
                    }
                    Template::set_message('Gagal membuat user.', 'error');
                }
            }
        }
        Template::set('role_list', $this->db->order_by('id_role', 'ASC')->get('roles')->result());
        Template::set('toolbar_title', 'Tambah User');
        Template::render();
    }

    public function edit($id = null)
    {
        $id = (int) $id > 0 ? (int) $id : 0;
        $row = $id ? $this->db->where('id_user', $id)->get('users')->row() : false;
        if (! $row) {
            Template::set_message('User tidak ditemukan.', 'error');
            redirect(SITE_AREA . '/settings/users');
        }
        if ($this->input->post('save')) {
            $this->form_validation->set_rules(array(
                array('field' => 'nama', 'label' => 'Nama', 'rules' => 'required|trim|max_length[150]'),
                array('field' => 'id_role', 'label' => 'Role', 'rules' => 'required|integer'),
                array('field' => 'status', 'label' => 'Status', 'rules' => 'required|in_list[AKTIF,NONAKTIF]'),
            ));
            $pwd = (string) $this->input->post('password');
            if ($pwd !== '' && strlen($pwd) < 6) {
                Template::set_message('Password minimal 6 karakter bila diisi.', 'error');
            } elseif ($this->form_validation->run() !== false) {
                // Cegah admin menonaktifkan dirinya sendiri.
                if ((int) $id === (int) $this->auth->user_id() && $this->input->post('status') !== 'AKTIF') {
                    Template::set_message('Tidak dapat menonaktifkan akun sendiri.', 'error');
                } else {
                    $data = array(
                        'nama' => $this->input->post('nama'),
                        'status' => $this->input->post('status'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    );
                    if ($pwd !== '') {
                        $hash = $this->auth->hash_password($pwd, 8);
                        if (empty($hash['hash'])) {
                            Template::set_message('Gagal membuat hash password.', 'error');
                            $hash = null;
                        } else {
                            $data['password'] = $hash['hash'];
                        }
                    }
                    $this->db->trans_start();
                    $this->db->where('id_user', $id)->update('users', $data);
                    $this->db->where('id_user', $id)->delete('user_roles');
                    $this->db->insert('user_roles', array('id_user' => $id, 'id_role' => (int) $this->input->post('id_role')));
                    $this->db->trans_complete();
                    if ($this->db->trans_status() !== false) {
                        $this->audit_log_model->catat($this->auth->user_id(), 'update', 'users', $id, '');
                        Template::set_message('User diperbarui.', 'success');
                        redirect(SITE_AREA . '/settings/users');
                    }
                    Template::set_message('Gagal memperbarui user.', 'error');
                }
            }
        }
        $row->id_role = $this->db->where('id_user', $id)->get('user_roles')->row()->id_role ?? null;
        Template::set('pengguna', $row);
        Template::set('role_list', $this->db->order_by('id_role', 'ASC')->get('roles')->result());
        Template::set('toolbar_title', 'Edit User');
        Template::render();
    }

    public function profile()
    {
        // ponytail: skema apotek pakai id_user (bukan id); dari session agar berlaku semua role.
        $user_id = (int) $this->auth->user_id();

        if (isset($_POST['save'])) {
            $nama = trim((string) $this->input->post('nama'));
            $password = (string) $this->input->post('password');
            $pass_confirm = (string) $this->input->post('pass_confirm');

            if ($nama === '') {
                Template::set_message('Nama tidak boleh kosong.', 'error');
            } elseif ($password !== '' && strlen($password) < 6) {
                Template::set_message('Password minimal 6 karakter bila diisi.', 'error');
            } elseif ($password !== '' && $password !== $pass_confirm) {
                Template::set_message('Konfirmasi password tidak cocok.', 'error');
            } else {
                $update = array('nama' => $nama, 'updated_at' => date('Y-m-d H:i:s'));
                if ($password !== '') {
                    $hash = $this->auth->hash_password($password, 8);
                    if (empty($hash['hash'])) {
                        Template::set_message('Gagal membuat hash password.', 'error');
                        $update = false;
                    } else {
                        $update['password'] = $hash['hash'];
                    }
                }
                if (is_array($update)) {
                    if ($this->db->where('id_user', $user_id)->update('users', $update)) {
                        Template::set_message('Profil berhasil diperbarui.', 'success');
                        redirect(SITE_AREA . '/profile');
                    }
                    Template::set_message('Gagal memperbarui profil.', 'error');
                }
            }
        }

        $user = $this->db->where('id_user', $user_id)->get('users')->row();
        if (! $user) {
            Template::set_message('Data user tidak ditemukan.', 'error');
            redirect(SITE_AREA);
        }
        Template::set('pengguna', $user);
        Template::set('toolbar_title', 'Profil Saya');
        Template::render();
    }
}

