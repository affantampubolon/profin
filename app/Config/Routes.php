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
$routes->post('/master/barang/subgrup', 'Master\Master::getSubGrupBarang');
$routes->post('/master/barang/kelas', 'Master\Master::getKelasBarang');
$routes->post('/master/filter/subgrup', 'Master\Master::getFilterSubgrp');
$routes->post('/master/filter/kelas', 'Master\Master::getFilterClass');
$routes->get('/master/pelanggancab', 'Master\Master::getMstPelanggan');
$routes->get('/master/kategoripelanggan', 'Master\Master::getMstKategoriPelanggan');
$routes->get('/master/pelanggan/registrasi', 'Master\Master::indexRegisPelanggan');
$routes->get('/master/probabilitas', 'Master\Master::dataFilterProbabilitas');
// Master Wilayah
$routes->get('/master/area/provinsi', 'Master\Master::getMstAreaProvinsi');
$routes->post('/master/area/kotakab', 'Master\Master::getMstAreaKotaKab');
$routes->post('/master/area/kecamatan', 'Master\Master::getMstAreaKecamatan');
$routes->post('/master/area/kelurahandesa', 'Master\Master::getMstAreaKelurahan');
$routes->post('/master/area/kodepos', 'Master\Master::getMstAreaKodePos');
// General
$routes->post('/login', 'Auth\Auth::login');
$routes->get('/logout', 'Auth\Auth::logout');
$routes->get('/beranda', 'Auth\Auth::beranda');
$routes->get('/pipeline/groupuser', 'Pipeline\Pipeline::getUserGroupSession');
$routes->get('/pipeline/pembuatan', 'Pipeline\Pipeline::index');
$routes->post('/pipeline/upload', 'Pipeline\Pipeline::uploadpipeline');
$routes->get('/pipeline/formulir', 'Pipeline\Pipeline::indexform');
$routes->get('/pipeline/persetujuan', 'Pipeline\Pipeline::indexPersetujuan');
$routes->get('/pipeline/monitoring', 'Pipeline\Pipeline::indexMonitoring');
$routes->post('/pipeline/temp/save', 'Pipeline\Pipeline::saveTemporerDetailPipeline');
$routes->get('/pipeline/temp/getdata', 'Pipeline\Pipeline::getTemporerDetailPipeline');
$routes->post('/pipeline/temp/delete', 'Pipeline\Pipeline::deleteTemporerDetailPipeline');
$routes->post('/pipeline/temp/insert', 'Pipeline\Pipeline::insertFormPipeline');
//draft pipeline
$routes->post('/pipeline/draft/getdata', 'Pipeline\Pipeline::dataDraftPipeline');
$routes->post('/pipeline/draft/update', 'Pipeline\Pipeline::updateDraftPipeline');
$routes->post('/pipeline/draft/delete', 'Pipeline\Pipeline::deleteDraftPipeline');

//verifikasi pipeline
$routes->post('/pipeline/verifikasi/getdata', 'Pipeline\Pipeline::dataVerifPipeline');
$routes->post('/pipeline/verifikasi/update', 'Pipeline\Pipeline::updateVerifikasi');

$routes->get('/izin/verifikasi', 'Izin\Izin::verifikasi');
$routes->get('/izin/monitoring', 'Izin\Izin::monitoring');
$routes->get('/realisasi/verifikasi', 'Realisasi\Realisasi::verifikasi');
$routes->get('/realisasi/monitoring', 'Realisasi\Realisasi::monitoring');
$routes->get('/rencana/verifikasi', 'Rencana\Rencana::verifikasi');
$routes->get('/rencana/monitoring', 'Rencana\Rencana::monitoring');
