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
$routes->get('/master/pminspector/datapminspector', 'Master\Master::dataPmInspector');
$routes->get('/master/pminspector/filterpminspector', 'Master\Master::dataFilterPmInspector');
$routes->get('/master/coa/datafilter', 'Master\Master::dataCoaFilter');
$routes->get('/master/katproyek/datafilter', 'Master\Master::dataKatProyek');
$routes->post('/master/coa/datanilai', 'Master\Master::dataCoaVal');
$routes->get('/master/pelanggan/datafiltermstpelanggan', 'Master\Master::dataFilterPelanggan');
$routes->get('/master/cabang', 'Master\Master::getMstCabang');
//KARYAWAN & USER
$routes->get('/master/karyawan/index', 'Master\Master::indexMstKaryawan');
$routes->post('/master/karyawan/datakaryawan', 'Master\Master::dataKaryawan');
$routes->get('/master/karyawan/getkaryawan/(:num)', 'Master\Master::getKaryawan/$1');
$routes->post('/master/karyawan/updatedatakaryawan', 'Master\Master::updateDataKaryawan');
$routes->get('/master/karyawan/formulir', 'Master\Master::indexMstKaryawanForm');
$routes->post('/master/karyawan/insertdatakaryawan', 'Master\Master::insertKaryawan');
$routes->get('/master/user/index', 'Master\Master::indexMstUser');
$routes->post('/master/user/datauser', 'Master\Master::dataUser');
$routes->post('/master/user/updatedatauser', 'Master\Master::updateDataUser');
$routes->get('/master/user/formulir', 'Master\Master::indexMstUserForm');
//Ganti Password
$routes->get('/master/user/indexubahpassword', 'Master\Master::indexGantiPasswordForm');
$routes->post('/master/user/ubahdatapassword', 'Master\Master::updateUserPassword');
//
$routes->get('/master/user/filterkaryawan', 'Master\Master::getFilterKaryawan');
$routes->post('/master/user/insertdatauser', 'Master\Master::insertUser');
//PELANGGAN
$routes->get('/master/pelanggan/index', 'Master\Master::indexMstPelanggan');
$routes->get('/master/pelanggan/filenpwp/(:segment)', 'Master\Master::getNpwpCustFile/$1');
$routes->get('/master/pelanggan/filenib/(:segment)', 'Master\Master::getNibCustFile/$1');
$routes->get('/master/pelanggan/registrasi', 'Master\Master::indexRegisPelanggan');
$routes->post('/master/pelanggan/getdatamstpelanggan', 'Master\Master::dataMstPelanggan');
$routes->post('/master/pelanggan/insertpelanggan', 'Master\Master::insertPelanggan');
// General
$routes->post('/login', 'Auth\Auth::login');
$routes->get('/logout', 'Auth\Auth::logout');

//beranda
$routes->get('/beranda', 'Auth\Auth::beranda');
$routes->post('/beranda/data/getdatapembayaran', 'Beranda\Beranda::dataGraphicPayment');
$routes->post('/beranda/data/getdatagrafikproyek', 'Beranda\Beranda::dataGraphicProject');
$routes->post('/beranda/data/getdatagrafikanggaran', 'Beranda\Beranda::dataGraphicBudgetProject');

//proyek
$routes->get('/proyek/dataproyekfilter', 'Proyek\Proyek::dataFilterProyek');
$routes->get('/proyek/dataanggaranfilter', 'Proyek\Proyek::dataProyekAnggaranFilter');
$routes->get('/proyek/datarealisasifilter', 'Proyek\Proyek::dataProyekRealisasiFilter');
$routes->get('/proyek/datapembayaranfilter', 'Proyek\Proyek::dataProyekPembayaranFilter');

//registrasi proyek
$routes->get('/proyek/registrasi/index', 'Proyek\Proyek::registrasiindex');
$routes->post('/proyek/registrasi/insertdataproyek', 'Proyek\Proyek::insertProyek');

//perbaruan data proyek
$routes->get('/proyek/pembaruandata/index', 'Proyek\Proyek::pembaruandataindex');
$routes->post('/proyek/pembaruandata/dataproyek', 'Proyek\Proyek::dataProyek');
$routes->get('/proyek/pembaruandata/getproyek/(:num)', 'Proyek\Proyek::dataProyekId/$1');
$routes->post('/proyek/pembaruandata/updatedataproyek', 'Proyek\Proyek::updateDataProyek');

//keuangananggaran
$routes->get('/keuangan/anggaran/index', 'Keuangan\Keuangan::anggaranindex');
$routes->post('/keuangan/anggaran/insertdataanggaran', 'Keuangan\Keuangan::insertAnggaran');

//keuanganrealisasi
$routes->get('/keuangan/realisasi/index', 'Keuangan\Keuangan::realisasiindex');
$routes->post('/keuangan/realisasi/insertdatarealisasi', 'Keuangan\Keuangan::insertRealisasi');

//keuangandropping
$routes->get('/keuangan/dropping/index', 'Keuangan\Keuangan::droppingindex');
$routes->post('/keuangan/dropping/insertdatadropping', 'Keuangan\Keuangan::insertDropping');

//keuanganpembayaran
$routes->get('/keuangan/pembayaran/index', 'Keuangan\Keuangan::pembayaranindex');
$routes->post('/keuangan/pembayaran/unggahinvoice', 'Keuangan\Keuangan::unggahInvoicePembayaran');
$routes->post('/keuangan/pembayaran/insertdatapembayaran', 'Keuangan\Keuangan::insertPembayaran');

//Monitoring
//Detail Proyek
$routes->get('/monitoring/detproyek/index', 'Monitoring\Monitoring::detproyekindex');
$routes->get('/monitoring/detproyek/getunduhdata', 'Monitoring\Monitoring::dataUnduhDetProyek');
$routes->get('/monitoring/detproyek/filespk/(:segment)', 'Monitoring\Monitoring::getSpkFile/$1');
$routes->get('/monitoring/detproyek/filelaporan/(:segment)', 'Monitoring\Monitoring::getLaporanFile/$1');
$routes->post('/monitoring/detproyek/getdetdata', 'Monitoring\Monitoring::dataDetProyek');
$routes->get('/monitoring/detproyek/getdetdata/(:num)', 'Monitoring\Monitoring::dataDetProyekId/$1');

//Anggaran dan Biaya
$routes->get('/monitoring/anggaranbiaya/index', 'Monitoring\Monitoring::anggaranbiayaindex');
$routes->get('/monitoring/anggaranbiaya/getunduhdata', 'Monitoring\Monitoring::dataUnduhAnggaranBiaya');
$routes->post('/monitoring/anggaranbiaya/getdetdata', 'Monitoring\Monitoring::dataAnggaranBiayaProyek');
$routes->post('/monitoring/anggaranbiaya/getdatadetrealisasi', 'Monitoring\Monitoring::dataDetRealisasi');
$routes->post('/monitoring/anggaranbiaya/getdatadetdropping', 'Monitoring\Monitoring::dataDetDropping');

//Pembayaran Piutang
$routes->get('/monitoring/pembayaranpiutang/index', 'Monitoring\Monitoring::pembayaranpiutangindex');
$routes->get('/monitoring/pembayaranpiutang/getunduhdata', 'Monitoring\Monitoring::dataUnduhPembayaranPiutang');
$routes->get('/monitoring/pembayaranpiutang/fileinvoice/(:segment)', 'Monitoring\Monitoring::getInvoiceFile/$1');
$routes->post('/monitoring/pembayaranpiutang/getdetdata', 'Monitoring\Monitoring::dataPembayaranPiutang');
$routes->post('/monitoring/pembayaranpiutang/getdetpembayaranpiutang', 'Monitoring\Monitoring::dataDetPembayaranPiutang');

