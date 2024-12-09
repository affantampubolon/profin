<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\AuthModel\AuthModel;

class Auth extends BaseController
{
    protected $authModel;
    // Parent Construct
    public function __construct()
    {
        $this->authModel = new AuthModel();
        $this->Session = \Config\Services::session();
    }

    // Function Index -> halaman LOGIN
    public function index()
    {
        // jika sudah login dan ingin masuk ke laman login maka akan diarahkan ke default page
        if ($this->Session->logged_in == true) {
            $this->goToDefaultPage();
        }

        // Helper Form
        helper(['form']);
        // Ambil Session dari function login 
        $this->Session;
        $data = [
            'title' => "MONAS - LOGIN",
            'validation' => \Config\Services::validation(),
        ];
        return view('auth/login', $data);
    }

    // public function login()
    // {
    //     // Helper Form
    //     helper(['form']);

    //     if (!$this->validate([
    //         'username' => 'required',
    //         'password' => 'required'
    //     ])) {
    //         $validation = \Config\Services::validation();
    //         return redirect()->to('/')->withInput()->with('validation', $validation);
    //     }

    //     // Ambil Inputan username dan password
    //     $username = trim($this->request->getVar('username'));
    //     $password = $this->request->getVar('password');
    //     // Cek Data inputan dengan database apakah ada user / email dari inputan 
    //     $data = $this->authModel->where('username', $username)->orWhere('email', $username)->first();
    //     // Jika data ada maka -> cek password
    //     if ($data) {
    //         // Cek Apakah user Aktif atau tidak :
    //         if ($data['is_active'] == 1) {
    //             // Jika User Active -> Cocokan password hasil inputan dengan password database :
    //             if (password_verify($password, $data['password'])) {
    //                 // Jika password cocok maka -> cek role id nya :
    //                 if ($data['role_id'] == 1) {
    //                     // Jika Role id nya == 1 maka dia admin , maka -> Create session loginnya :
    //                     // Data session login
    //                     $ses_data = [
    //                         'username'   => $data['username'],
    //                         'role_id'    => $data['role_id'],
    //                         'branch_id'  => $data['branch_id'],
    //                         'foto'       => $data['image'],
    //                         'logged_in'  => TRUE
    //                     ];
    //                     // Set session
    //                     $this->Session->set($ses_data);
    //                     // Redirect ke halaman admin
    //                     return redirect()->to('/admin');
    //                 } elseif ($data['role_id'] == 2) {
    //                     // Jika Role id nya == 2 maka dia Operasional , maka -> Create session loginnya :
    //                     // Data session login
    //                     $ses_data = [
    //                         'username'   => $data['username'],
    //                         'role_id'    => $data['role_id'],
    //                         'branch_id'  => $data['branch_id'],
    //                         'foto'       => $data['image'],
    //                         'logged_in'  => TRUE
    //                     ];
    //                     // Set session
    //                     $this->Session->set($ses_data);
    //                     // Redirect ke halaman marketing
    //                     return redirect()->to('/marketing');
    //                 } elseif ($data['role_id'] == 3) {
    //                     // Jika Role id nya == 3 maka dia NON OPERASIONAL , maka -> Create session loginnya :
    //                     // Data session login
    //                     $ses_data = [
    //                         'username'   => $data['username'],
    //                         'role_id'    => $data['role_id'],
    //                         'branch_id'  => $data['branch_id'],
    //                         'foto'       => $data['image'],
    //                         'logged_in'  => TRUE
    //                     ];
    //                     // Set session
    //                     $this->Session->set($ses_data);
    //                     // Redirect ke halaman finance
    //                     return redirect()->to('/finance');
    //                 } elseif ($data['role_id'] == 4) {
    //                     // Jika Role id nya == 4 maka dia MASTER , maka -> Create session loginnya :
    //                     // Data session login
    //                     $ses_data = [
    //                         'username'   => $data['username'],
    //                         'role_id'    => $data['role_id'],
    //                         'branch_id'  => $data['branch_id'],
    //                         'foto'       => $data['image'],
    //                         'logged_in'  => TRUE
    //                     ];
    //                     // Set session
    //                     $this->Session->set($ses_data);
    //                     // Redirect ke halaman finance
    //                     return redirect()->to('/marketing');
    //                 }
    //             }
    //             // Jika password Salah maka -> Kembali kehalaman login dan berikan pesan salah password 
    //             else {
    //                 $this->Session->setFlashdata('msg', 'Password yang anda masukkan salah!');
    //                 return redirect()->to('/');
    //             }
    //         }
    //         // Jika user tidak active maka tidak bisa login : 
    //         else {
    //             $this->Session->setFlashdata('msg', 'User tidak Active silahkan kontak Administrator!');
    //             return redirect()->to('/');
    //         }
    //     }
    //     // Jika data tidak ada berdasarkan inputan maka -> Kembali ke halaman login dan berikan pesan user tidak terdaftar 
    //     else {
    //         $this->Session->setFlashdata('msg', 'Username atau Email tidak terdaftar!');
    //         return redirect()->to('/');
    //     }
    // }

    // public function logout()
    // {
    //     $ses_data = ['username', 'role_id', 'branch_id', 'logged_in'];
    //     $this->Session->remove($ses_data);
    //     $this->Session->setFlashdata('logout', 'Anda Berhasil Logout!');
    //     return redirect()->to('/');
    // }

    // DefaultPage after login
    // public function goToDefaultPage()
    // {
    //     // Jika Sudah login maka tidak bisa ke menu login sebelum logout
    //     // Admin
    //     if ($this->Session->role_id == 1) {
    //         return redirect()->to('/admin');
    //     }
    //     // Jika Bukan Admin maka dia Operasional
    //     else if ($this->Session->role_id  == 2) {
    //         return redirect()->to('/marketing');
    //     }
    //     // Jika Bukan operasional maka dia non operasional 
    //     else if ($this->Session->role_id  == 3) {
    //         return redirect()->to('/finance');
    //     }
    //     // End
    // }

    // Blocked because required user access
    // public function blocked()
    // {
    //     $data = [
    //         'title' => "403 - Forbidden"
    //     ];
    //     echo view('auth/blocked', $data);
    //     // $this->load->view('templates/auth_header', $data);
    //     // $this->load->view('auth/blocked');
    // }

    // Change password
    // public function changePassword()
    // {
    //     // Helper Form
    //     helper(['form']);
    //     $validator = \Config\Services::validation();

    //     $data = [
    //         'title'   => "Change Password",
    //         'Session' => $this->Session,
    //     ];
    //     $data['userdata'] = $this->authModel->where('username', $this->Session->username)->first();

    //     if ($this->request->getMethod() == 'post') {
    //         $rules = [
    //             'current_password'  => 'required',
    //             'new_password'      => 'required|min_length[6]|max_length[16]',
    //             'repeat_password'   => 'required|matches[new_password]',
    //         ];
    //         if ($this->validate($rules)) {

    //             $current   = $this->request->getPost('current_password');
    //             $newpassword = password_hash($this->request->getPost('new_password'), PASSWORD_DEFAULT);

    //             if (password_verify($current, $data['userdata']['password'])) {
    //                 if ($this->authModel->updatePassword($newpassword, $this->Session->username)) {
    //                     session()->setTempdata('success', 'Password berhasil diganti', 3);
    //                     return redirect()->to(current_url());
    //                 } else {
    //                     session()->setTempdata('error', 'Maaf password gagal diganti, ulangi kembali', 3);
    //                     return redirect()->to(current_url());
    //                 }
    //             } else {
    //                 session()->setTempdata('error', 'Password lama tidak sesuai', 3);
    //                 return redirect()->to(current_url());
    //             }
    //         } else {

    //             $data['validation'] = $this->validator;
    //         }
    //     }

    //     echo view('auth/change_password', $data);
    // }
}
