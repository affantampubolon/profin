<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.

// Auth
$routes->get('/', 'Auth\Auth::index');
$routes->post('/login', 'Auth\Auth::login');
$routes->get('/logout', 'Auth\Auth::logout');
$routes->get('/beranda', 'Auth\Auth::beranda');
$routes->get('/pipeline/pembuatan', 'Pipeline\Pipeline::index');
$routes->post('/pipeline/upload', 'Pipeline\Pipeline::uploadpipeline');
$routes->get('/pipeline/formulir', 'Pipeline\Pipeline::indexform');
$routes->post('/pipeline/getSubGrupBarang', 'Pipeline\Pipeline::getSubGrupBarang');
$routes->post('/pipeline/getKelasBarang', 'Pipeline\Pipeline::getKelasBarang');
$routes->get('/pipeline/getMstPelanggan', 'Pipeline\Pipeline::getMstPelanggan');
$routes->post('/pipeline/saveTemp', 'Pipeline\Pipeline::saveTemporerDetailPipeline');
$routes->get('/pipeline/getTemp', 'Pipeline\Pipeline::getTemporerDetailPipeline');
$routes->post('/pipeline/insertForm', 'Pipeline\Pipeline::insertFormPipeline');
$routes->get('/izin/verifikasi', 'Izin\Izin::verifikasi');
$routes->get('/izin/monitoring', 'Izin\Izin::monitoring');
