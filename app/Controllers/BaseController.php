<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Models\UserModel\UserModel;
use App\Models\UserModel\EmpModel;
use App\Models\UserModel\ParamEmpModel;
use App\Models\MenuModel\MenuModel;
// beranda
use App\Models\BerandaModel\BerandaModel;
//proyek
use App\Models\ProyekModel\ProyekModel;
//keuangan
use App\Models\KeuanganModel\AnggaranModel;
use App\Models\KeuanganModel\RealisasiModel;
use App\Models\KeuanganModel\DroppingModel;
use App\Models\KeuanganModel\PembayaranModel;
//monitoring
use App\Models\MonitoringModel\MonitoringModel;
//master
use App\Models\MasterModel\PmInspectorModel;
use App\Models\MasterModel\CoaModel;
use App\Models\MasterModel\KategoriProyekModel;
// 
use App\Models\MasterModel\CabangModel;
use App\Models\MasterModel\PosisiModel;
use App\Models\MasterModel\HakAksesModel;
use App\Models\MasterModel\PelangganModel;
use App\Models\MasterModel\KaryawanModel;
use App\Models\MasterModel\DepartmentModel;
use App\Models\MasterModel\WilayahDetModel;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var list<string>
     */
    protected $helpers = [];

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    // protected $session;

    /**
     * @return void
     */
    protected $session;
    protected $validation;
    protected $db;
    protected $menuTree;

    // Deklarasi model di BaseController
    // master
    protected $cabangModel;
    protected $pelangganModel;
    protected $wilayahModel;
    //auth
    protected $userModel;
    protected $empModel;
    protected $paramEmpModel;
    protected $menuModel;
    //beranda
    protected $berandaModel;
    //proyek
    protected $proyekModel;
    //keuangan
    protected $anggaranModel;
    protected $realisasiModel;
    protected $droppingModel;
    protected $pembayaranModel;
    //monitoring
    protected $monitoringModel;

    //master
    protected $pmInspectorModel;
    protected $coaModel;
    protected $kategoriProyekModel;
    // department
    protected $DeptModel;

    // posisi
    protected $posisiModel;

    // hak akses
    protected $hakAksesModel;

    // karyawan
    protected $karyawanModel;

    protected $breadcrumb;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.
        $this->session = \Config\Services::session();
        $this->validation = \Config\Services::validation();
        // Deklarasi helper form
        helper(['form']);
        // Inisialisasi db
        $this->db = \Config\Database::connect();
        // Inisialisasi $this->view
        $this->view = \Config\Services::renderer();
        // Deklarasi models
        //Master Models
        $this->cabangModel         = new CabangModel();
        $this->pelangganModel      = new PelangganModel();
        $this->wilayahModel        = new WilayahDetModel();
        //General Models
        $this->userModel        = new UserModel();
        $this->empModel         = new EmpModel();
        $this->paramEmpModel    = new ParamEmpModel();
        $this->menuModel        = new MenuModel();
        $this->berandaModel    = new BerandaModel();
        //proyek
        $this->proyekModel = new ProyekModel();
        //keuangan
        $this->anggaranModel = new AnggaranModel();
        $this->realisasiModel = new RealisasiModel();
        $this->droppingModel = new DroppingModel();
        $this->pembayaranModel = new PembayaranModel();
        //monitoring
        $this->monitoringModel = new MonitoringModel();
        //master
        $this->pmInspectorModel = new PmInspectorModel();
        $this->coaModel = new CoaModel();
        $this->kategoriProyekModel = new KategoriProyekModel();
        
        $this->DeptModel         = new DepartmentModel();

        $this->posisiModel       = new PosisiModel();

        $this->karyawanModel     = new KaryawanModel();

        $this->hakAksesModel        = new HakAksesModel();
        // Ambil dan buat tree menu di BaseController
        $this->loadMenu();

        // Fungsi Lain - lain
        $this->breadcrumb = $this->getBreadcrumb();
    }

    private function loadMenu()
    {
        $username = $this->session->get('username');

        // Hanya load menu jika user sudah login
        if ($username) {
            $menuData = $this->menuModel->getMenuUserData($username);
            $menuTree = $this->menuModel->buildMenuTree($menuData);
            $this->view->setVar('menuTree', $menuTree);
        } else {
            $this->view->setVar('menuTree', []);
        }
    }

    private function getBreadcrumb()
    {
        $username = $this->session->get('username');
        $link_menu = uri_string();

        return $this->menuModel->getBreadcrumb($username, $link_menu);
    }
}
