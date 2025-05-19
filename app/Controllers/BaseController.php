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
use App\Models\PipelineModel\PipelineModel;
use App\Models\PipelineModel\PipelineDetModel;
use App\Models\RencanaKunjModel\RencanaKunjModel;
use App\Models\RealisasiKunjModel\RealisasiKunjModel;
use App\Models\MasterModel\KelasProdModel;
use App\Models\MasterModel\CabangModel;
use App\Models\MasterModel\PelangganModel;
use App\Models\MasterModel\SalesMarketingModel;
use App\Models\MasterModel\SkalaProbabilitasModel;
use App\Models\IzinModel\IzinModel;
use App\Models\MasterModel\DepartmentModel;
use App\Models\MasterModel\WilayahDetModel;
use App\Models\PelaporanModel\PelaporanModel;

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
    protected $kelasProdModel;
    protected $cabangModel;
    protected $pelangganModel;
    protected $probabilitasModel;
    protected $wilayahModel;
    //auth
    protected $userModel;
    protected $empModel;
    protected $paramEmpModel;
    protected $menuModel;
    // pipeline 
    protected $pipelineModel;
    protected $pipelineDetModel;
    protected $rencanaKunjModel;
    protected $realisasiKunjModel;
    // izin 
    protected $izinModel;
    // department
    protected $DeptModel;

    //pelaporan
    protected $pelaporanModel;

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
        $this->kelasProdModel      = new KelasProdModel();
        $this->cabangModel         = new CabangModel();
        $this->pelangganModel      = new PelangganModel();
        $this->probabilitasModel   = new SkalaProbabilitasModel();
        $this->wilayahModel        = new WilayahDetModel();
        $this->salesMarketingModel = new SalesMarketingModel();
        //General Models
        $this->userModel        = new UserModel();
        $this->empModel         = new EmpModel();
        $this->paramEmpModel    = new ParamEmpModel();
        $this->menuModel        = new MenuModel();
        //Pipeline
        $this->pipelineModel    = new PipelineModel();
        $this->pipelineDetModel = new PipelineDetModel();
        //Rencana Kunjungan
        $this->rencanaKunjModel = new RencanaKunjModel();
        //Realisasi Kunjungan
        $this->realisasiKunjModel = new RealisasiKunjModel();
        //Pelaporan
        $this->pelaporanModel = new PelaporanModel();
        //
        $this->izinModel        = new IzinModel();
        
        $this->DeptModel        = new DepartmentModel();
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
