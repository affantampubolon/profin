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
use App\Models\MasterModel\KelasProdModel;
use App\Models\MasterModel\PelangganModel;

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
    //master
    protected $kelasProdModel;
    protected $pelangganModel;
    //auth
    protected $userModel;
    protected $empModel;
    protected $paramEmpModel;
    protected $menuModel;
    //Models Pipeline
    protected $pipelineModel;
    protected $pipelineDetModel;



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
        $this->kelasProdModel   = new KelasProdModel();
        $this->pelangganModel   = new PelangganModel();
        //General Models
        $this->userModel        = new UserModel();
        $this->empModel         = new EmpModel();
        $this->paramEmpModel    = new ParamEmpModel();
        $this->menuModel        = new MenuModel();
        $this->pipelineModel    = new PipelineModel();
        $this->pipelineDetModel = new PipelineDetModel();
        // Ambil dan buat tree menu di BaseController
        $this->loadMenu();
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
}
