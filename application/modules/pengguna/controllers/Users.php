 <?php defined('BASEPATH') OR exit('No direct script access allowed');

  class Users extends App_Controller
  {
      public function __construct()
      {
          parent::__construct();

          $this->load->library(array('form_validation', 'session'));
          $this->load->helper(array('url', 'form', 'security'));
      }

      public function index()
      {
          $this->db
              ->select('
                  users.id_user,
                  users.username,
                  users.nama,
                  users.status,
                  roles.id_role,
                  roles.nama_role
              ')
              ->from('users')
              ->join('user_roles', 'user_roles.id_user = users.id_user', 'left')
              ->join('roles', 'roles.id_role = user_roles.id_role', 'left')
              ->order_by('users.id_user', 'DESC');

          $data['user_list'] = $this->db->get()->result();

          $data['role_list'] = $this->db
              ->order_by('id_role', 'ASC')
              ->get('roles')
              ->result();

          Template::set($data);
          Template::set_view('pengguna/users/index');
          Template::render();
      }

      public function create()
      {
          if ($this->input->method() === 'post') {
              $this->form_validation->set_rules(
                  'username',
                  'Username',
                  'required|trim|max_length[50]|is_unique[users.username]'
              );

              $this->form_validation->set_rules(
                  'nama',
                  'Nama',
                  'required|trim|max_length[150]'
              );

              $this->form_validation->set_rules(
                  'password',
                  'Password',
                  'required|min_length[6]'
              );

              $this->form_validation->set_rules(
                  'id_role',
                  'Role',
                  'required|integer'
              );

              if ($this->form_validation->run()) {
                  $this->db->trans_start();

                  $user_data = array(
                      'username' => trim($this->input->post('username', true)),
                      'nama'     => trim($this->input->post('nama', true)),
                      'password' => password_hash(
                          $this->input->post('password'),
                          PASSWORD_DEFAULT
                      ),
                      'status'   => $this->input->post('status', true) ?: 'AKTIF'
                  );

                  $this->db->insert('users', $user_data);
                  $id_user = $this->db->insert_id();

                  $this->db->insert('user_roles', array(
                      'id_user' => $id_user,
                      'id_role' => (int) $this->input->post('id_role')
                  ));

                  $this->db->trans_complete();

if ($this->db->trans_status()) {
    $this->session->set_flashdata(
        'success',
        'User berhasil ditambahkan.'
    );

    redirect(SITE_AREA . '/settings/users');
    return;
}

                  $this->session->set_flashdata(
                      'error',
                      'User gagal ditambahkan.'
                  );
              }
          }

          $data['role_list'] = $this->db
              ->order_by('id_role', 'ASC')
              ->get('roles')
              ->result();

          Template::set($data);
          Template::set_view('pengguna/users/create');
          Template::render();
      }

      public function edit($id = null)
  {
      $id = (int) $id;

      if ($id <= 0) {
          show_404();
      }

      $user = $this->db
          ->select('users.id_user, users.username, users.nama, users.status,
          user_roles.id_role')
          ->from('users')
          ->join('user_roles', 'user_roles.id_user = users.id_user', 'left')
          ->where('users.id_user', $id)
          ->get()
          ->row();

      if (!$user) {
          show_404();
      }

      $data = array(
          'user'       => $user,
          'role_list'  => $this->db
              ->order_by('id_role', 'ASC')
              ->get('roles')
              ->result(),
          'save_error' => ''
      );

      if ($this->input->method() === 'post') {
          $this->form_validation->set_rules(
              'nama',
              'Nama',
              'required|trim|max_length[150]'
          );

          $this->form_validation->set_rules(
              'id_role',
              'Role',
              'required|integer'
          );

          $this->form_validation->set_rules(
              'status',
              'Status',
              'required|in_list[AKTIF,NONAKTIF]'
          );

          if ($this->input->post('password') !== '') {
              $this->form_validation->set_rules(
                  'password',
                  'Password',
                  'min_length[6]'
              );
          }

          if ($this->form_validation->run()) {
              $id_role = (int) $this->input->post('id_role', true);

              $role_exists = $this->db
                  ->where('id_role', $id_role)
                  ->count_all_results('roles') > 0;

              if (!$role_exists) {
                  $data['save_error'] = 'Role yang dipilih tidak ditemukan.';
              } else {
                  $user_data = array(
                      'nama'       => trim($this->input->post('nama', true)),
                      'status'     => $this->input->post('status', true),
                      'updated_at' => date('Y-m-d H:i:s')
                  );

                  $password = $this->input->post('password');

                  if ($password !== '') {
                      $user_data['password'] = password_hash(
                          $password,
                          PASSWORD_DEFAULT
                      );
                  }

                  $this->db->trans_begin();

                  $success = $this->db
                      ->where('id_user', $id)
                      ->update('users', $user_data);

                  if ($success) {
                      $success = $this->db
                          ->where('id_user', $id)
                          ->delete('user_roles');
                  }

                  if ($success) {
                      $success = $this->db->insert('user_roles', array(
                          'id_user' => $id,
                          'id_role' => $id_role
                      ));
                  }

                  if ($success && $this->db->trans_status()) {
                      $this->db->trans_commit();

                      $this->session->set_flashdata(
                          'success',
                          'User berhasil diperbarui.'
                      );

                      redirect(SITE_AREA . '/settings/users');
                      return;
                  }

                  $this->db->trans_rollback();
                  $data['save_error'] = 'User gagal diperbarui. Tidak ada perubahan yang
                  disimpan.';
              }
          }
      }

      Template::set($data);
      Template::set_view('pengguna/users/edit');
      Template::render();
  }

      public function delete($id = null)
      {
          $id = (int) $id;

          if ($id <= 0) {
              show_404();
          }

          if ($this->input->method() !== 'post') {
              show_error('Metode tidak diizinkan.', 405);
          }

          if ($this->auth->user_id() == $id) {
              $this->session->set_flashdata(
                  'error',
                  'User yang sedang login tidak boleh dihapus.'
              );

              redirect(SITE_AREA . '/settings/users');
              return;
          }

          $user = $this->db
              ->where('id_user', $id)
              ->get('users')
              ->row();

          if (!$user) {
              show_404();
          }

          $this->db->trans_start();

          $this->db
              ->where('id_user', $id)
              ->delete('user_roles');

          $this->db
              ->where('id_user', $id)
              ->delete('users');

          $this->db->trans_complete();

          if ($this->db->trans_status()) {
              $this->session->set_flashdata(
                  'success',
                  'User berhasil dihapus.'
              );
          } else {
              $this->session->set_flashdata(
                  'error',
                  'User gagal dihapus.'
              );
          }

          redirect(SITE_AREA . '/settings/users');
      }
  }
