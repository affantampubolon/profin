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
// Master
$routes->post('/master/getSubGrupBarang', 'Master\Master::getSubGrupBarang');
$routes->post('/master/getKelasBarang', 'Master\Master::getKelasBarang');
$routes->get('/master/getMstPelanggan', 'Master\Master::getMstPelanggan');
$routes->get('/master/getMstKategoriPelanggan', 'Master\Master::getMstKategoriPelanggan');
$routes->get('/master/pelanggan/registrasi', 'Master\Master::indexRegisPelanggan');
// Master Wilayah
$routes->get('/master/getAreaProvinsi', 'Master\Master::getMstAreaProvinsi');
$routes->post('/master/getAreaKotaKab', 'Master\Master::getMstAreaKotaKab');
$routes->post('/master/getAreaKecamatan', 'Master\Master::getMstAreaKecamatan');
$routes->post('/master/getAreaKelurahan', 'Master\Master::getMstAreaKelurahan');
$routes->post('/master/getAreaKodePos', 'Master\Master::getMstAreaKodePos');
// General
$routes->post('/login', 'Auth\Auth::login');
$routes->get('/logout', 'Auth\Auth::logout');
$routes->get('/beranda', 'Auth\Auth::beranda');
$routes->get('/pipeline/pembuatan', 'Pipeline\Pipeline::index');
$routes->post('/pipeline/upload', 'Pipeline\Pipeline::uploadpipeline');
$routes->get('/pipeline/formulir', 'Pipeline\Pipeline::indexform');
$routes->get('/pipeline/persetujuan', 'Pipeline\Pipeline::indexPersetujuan');
$routes->post('/pipeline/saveTemp', 'Pipeline\Pipeline::saveTemporerDetailPipeline');
$routes->get('/pipeline/getTemp', 'Pipeline\Pipeline::getTemporerDetailPipeline');
$routes->post('/pipeline/deleteTemp', 'Pipeline\Pipeline::deleteTemporerDetailPipeline');
$routes->post('/pipeline/insertForm', 'Pipeline\Pipeline::insertFormPipeline');
$routes->get('/izin/verifikasi', 'Izin\Izin::verifikasi');
$routes->get('/izin/monitoring', 'Izin\Izin::monitoring');
