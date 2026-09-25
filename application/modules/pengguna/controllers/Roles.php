<?php defined('BASEPATH') OR exit('No direct script access allowed');

  class Roles extends App_Controller
  {
      public function __construct()
      {
          parent::__construct();

          $this->load->library(array('form_validation', 'session'));
          $this->load->helper(array('url', 'form', 'security'));
      }

      public function index()
      {
          $roles = $this->db
              ->order_by('id_role', 'ASC')
              ->get('roles')
              ->result();

          foreach ($roles as $role) {
              $role->jml_user = $this->db
                  ->where('id_role', $role->id_role)
                  ->count_all_results('user_roles');

              $role->jml_perm = $this->db
                  ->where('id_role', $role->id_role)
                  ->count_all_results('role_permissions');
          }

          $data['role_list'] = $roles;

          Template::set($data);
          Template::set_view('pengguna/roles/index');
          Template::render();
      }

      public function create()
      {
          if ($this->input->method() === 'post') {
              $this->form_validation->set_rules(
                  'nama_role',
                  'Nama Role',
                  'required|trim|max_length[100]|is_unique[roles.nama_role]'
              );

              if ($this->form_validation->run()) {
                  $nama_role = strtoupper(
                      trim($this->input->post('nama_role', true))
                  );

                  $exists = $this->db
                      ->where('nama_role', $nama_role)
                      ->count_all_results('roles');

                  if ($exists > 0) {
                      $this->session->set_flashdata(
                          'error',
                          'Nama role sudah digunakan.'
                      );
                  } else {
                      $this->db->insert('roles', array(
                          'nama_role' => $nama_role
                      ));

                      $this->session->set_flashdata(
                          'success',
                          'Role berhasil ditambahkan.'
                      );

                      redirect(SITE_AREA . '/settings/roles');
                      return;
                  }
              }
          }

          Template::set_view('pengguna/roles/create');
          Template::render();
      }

      public function edit($id = null)
      {
          $id = (int) $id;

          if ($id <= 0) {
              show_404();
          }

          $role = $this->db
              ->where('id_role', $id)
              ->get('roles')
              ->row();

          if (!$role) {
              show_404();
          }

          if ($this->input->method() === 'post') {
              $nama_role = strtoupper(
                  trim($this->input->post('nama_role', true))
              );

              if ($nama_role === '') {
                  $this->session->set_flashdata(
                      'error',
                      'Nama role wajib diisi.'
                  );
              } else {
                  $duplicate = $this->db
                      ->where('nama_role', $nama_role)
                      ->where('id_role !=', $id)
                      ->count_all_results('roles');

                  if ($duplicate > 0) {
                      $this->session->set_flashdata(
                          'error',
                          'Nama role sudah digunakan.'
                      );
                  } else {
                      $this->db
                          ->where('id_role', $id)
                          ->update('roles', array(
                              'nama_role' => $nama_role
                          ));

                      $this->session->set_flashdata(
                          'success',
                          'Nama role berhasil diperbarui.'
                      );

                      redirect(SITE_AREA . '/settings/roles');
                      return;
                  }
              }
          }

          $data['role'] = $role;

          Template::set($data);
          Template::set_view('pengguna/roles/edit');
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

          $role = $this->db
              ->where('id_role', $id)
              ->get('roles')
              ->row();

          if (!$role) {
              show_404();
          }

          $used_by_users = $this->db
              ->where('id_role', $id)
              ->count_all_results('user_roles');

          if ($used_by_users > 0) {
              $this->session->set_flashdata(
                  'error',
                  'Role tidak dapat dihapus karena masih digunakan oleh user.'
              );

              redirect(SITE_AREA . '/settings/roles');
              return;
          }

          $this->db->trans_start();

          $this->db
              ->where('id_role', $id)
              ->delete('role_permissions');

          $this->db
              ->where('id_role', $id)
              ->delete('roles');

          $this->db->trans_complete();

          if ($this->db->trans_status()) {
              $this->session->set_flashdata(
                  'success',
                  'Role berhasil dihapus.'
              );
          } else {
              $this->session->set_flashdata(
                  'error',
                  'Role gagal dihapus.'
              );
          }

          redirect(SITE_AREA . '/settings/roles');
      }
public function matrix($id = null)
  {
      $id = (int) $id;

      if ($id <= 0) {
          show_404();
      }

      $role = $this->db
          ->where('id_role', $id)
          ->get('roles')
          ->row();

      if (!$role) {
          show_404();
      }

      $current_role_id = (int) $this->auth->role_id();

      /*
       * Bila admin sedang mengubah matriks role miliknya sendiri, permission
       * minimum berikut tidak boleh hilang. Jika hilang, redirect sesudah
       * simpan akan ditolak lalu pengguna dibawa ke dashboard/Apotek.
       */
      $locked_permission_names = array(
          'Site.Settings.View',
          'kelola_role_permission',
          'Bonfire.Roles.View',
          'Bonfire.Roles.Manage'
      );

      $locked_permission_ids = array();

      if ($current_role_id === $id) {
          $locked_permissions = $this->db
              ->select('id_permission')
              ->where_in('nama_permission', $locked_permission_names)
              ->get('permissions')
              ->result();

          foreach ($locked_permissions as $permission) {
              $locked_permission_ids[] = (int) $permission->id_permission;
          }
      }

      if ($this->input->method() === 'post') {
          $permissions = $this->input->post('perm');

          if (!is_array($permissions)) {
              $permissions = array();
          }

          $permissions = array_map('intval', $permissions);
          $permissions = array_filter($permissions, function ($id_permission) {
              return $id_permission > 0;
          });

          $permissions = array_values(array_unique($permissions));

          /*
           * Paksa permission navigasi/kelola role tetap ada hanya untuk role
           * yang sedang dipakai oleh akun login.
           */
          $permissions = array_values(array_unique(array_merge(
              $permissions,
              $locked_permission_ids
          )));

          /*
           * Pastikan semua ID permission benar-benar ada sebelum menghapus
           * relasi lama. Ini mencegah data matriks tersimpan sebagian.
           */
          $valid_permission_ids = array();

          if (!empty($permissions)) {
              $valid_permissions = $this->db
                  ->select('id_permission')
                  ->where_in('id_permission', $permissions)
                  ->get('permissions')
                  ->result();

              foreach ($valid_permissions as $permission) {
                  $valid_permission_ids[] = (int) $permission->id_permission;
              }
          }

          sort($permissions);
          sort($valid_permission_ids);

          if ($permissions !== $valid_permission_ids) {
              $data['save_error'] = 'Terdapat permission tidak valid. Matriks tidak
              diubah.';
          } else {
              $this->db->trans_begin();

              $success = $this->db
                  ->where('id_role', $id)
                  ->delete('role_permissions');

              if ($success) {
                  foreach ($permissions as $id_permission) {
                      $success = $this->db->insert('role_permissions', array(
                          'id_role'       => $id,
                          'id_permission' => $id_permission
                      ));

                      if (!$success) {
                          break;
                      }
                  }
              }

              if ($success && $this->db->trans_status()) {
                  $this->db->trans_commit();

                  $this->session->set_flashdata(
                      'success',
                      'Permission role berhasil diperbarui.'
                  );

                  /*
                   * Redirect eksplisit ke rute publik matriks yang sama.
                   */
                  redirect('admin/settings/roles/matrix/' . $id);
                  return;
              }

              $this->db->trans_rollback();

              $data['save_error'] = 'Permission role gagal diperbarui. Tidak ada perubahan
              yang disimpan.';
          }
      }

      $data['perm_list'] = $this->db
          ->select('id_permission, nama_permission, modul')
          ->from('permissions')
          ->order_by('modul', 'ASC')
          ->order_by('nama_permission', 'ASC')
          ->get()
          ->result();

      $punya = $this->db
          ->select('id_permission')
          ->where('id_role', $id)
          ->get('role_permissions')
          ->result();

      $data['punya'] = array();

      foreach ($punya as $permission) {
          $data['punya'][(int) $permission->id_permission] = true;
      }

      $data['role'] = $role;
      $data['locked_permission_ids'] = $locked_permission_ids;

      if (!isset($data['save_error'])) {
          $data['save_error'] = '';
      }

      Template::set($data);
      Template::set_view('pengguna/roles/matrix');
      Template::render();
  }
}