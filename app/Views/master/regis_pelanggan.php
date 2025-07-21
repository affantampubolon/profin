<?= $this->extend('layout/template'); ?>

<!-- BEGIN : Main Content-->
<?= $this->section('content'); ?>
<!-- Container-fluid starts-->
<div class="container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-6">
                <h4>Pelanggan</h4>
            </div>
            <div class="col-6">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="index.html">
                            <svg class="stroke-icon">
                                <use
                                    href="assets/svg/icon-sprite.svg#stroke-home"></use>
                            </svg></a>
                    </li>
                    <?php
                    $path_parts = explode(">", $breadcrumb['path_name']);
                    $current_path = '';
                    foreach ($path_parts as $part):
                        $current_path .= $part . '>';
                        if ($part != end($path_parts)): ?>
                            <li class="breadcrumb-item"><?= $part ?></li>
                        <?php else: ?>
                            <li class="breadcrumb-item active" aria-current="page"><?= $part ?></li>
                    <?php endif;
                    endforeach;
                    ?>
                </ol>
            </div>
        </div>
    </div>
</div>
<!-- Container-fluid starts-->
<div class="container-fluid">
    <div class="row size-column">
        <div class="col-xl-12 col-md-12 box-col-12">
            <div class="card">
                <div class="card-header card-no-border">
                    <h4>Formulir Registrasi Pelanggan</h4>
                    <div class="col-xl-6 col-md-6">
                        <label class="form-label" for=""
                        >Pelanggan Baru</label
                        >
                        <select id="masterpelangganbaru" class="select2 form-control" name="master_pelanggan_baru"></select>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <form class="row g-3">
                        <input type="hidden" id="idRefUser" name="idRefUser">
                        <div class="col-xl-12 col-md-12 box-col-12">
                            <p class="text-uppercase"><b>Identitas Pelanggan</b></p>
                            <div class="row p-2">
                                <div class="col-xl-6 col-md-6">
                                    <label class="form-label" for=""
                                    >Nama Pelanggan</label
                                    >
                                    <input
                                    class="form-control"
                                    id="namaPelanggan"
                                    type="text"
                                    disabled
                                    />
                                </div>
                                <div class="col-xl-6 col-md-6">
                                    <label class="form-label" for=""
                                    >Nama Pemilik <span class="txt-danger f-w-600">*</span></label
                                    >
                                    <input
                                    class="form-control"
                                    id="namaPemilik"
                                    type="text"
                                    placeholder="nama pemilik"
                                    required
                                    />
                                </div>
                            </div>
                            <div class="row p-2">
                                <div class="col-xl-6 col-md-6">
                                    <label class="form-label" for=""
                                    >Email</label
                                    >
                                    <input
                                    class="form-control"
                                    id="emailPelanggan"
                                    type="email"
                                    placeholder="email"
                                    />
                                </div>
                                <div class="col-xl-6 col-md-6">
                                    <label class="form-label" for=""
                                    >No. Telepon</label
                                    >
                                    <input
                                    class="form-control"
                                    id="notelpPelanggan"
                                    type="text"
                                    placeholder="no telepon"
                                    />
                                </div>
                            </div>
                            <div class="row p-2">
                                <div class="col-xl-6 col-md-6">
                                    <label class="form-label" for=""
                                    >No. KTP <span class="txt-danger f-w-600">*</span></label
                                    >
                                    <input
                                    class="form-control"
                                    id="ktpPelanggan"
                                    type="text"
                                    placeholder="no KTP"
                                    required
                                    />
                                </div>
                                <div class="col-xl-6 col-md-6">
                                    <label class="form-label" for=""
                                    >No. SIUP</label
                                    >
                                    <input
                                    class="form-control"
                                    id="siupPelanggan"
                                    type="text"
                                    placeholder="no SIUP"
                                    />
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-12 col-md-12 box-col-12">
                            <p class="text-uppercase"><b>Detail Lokasi</b></p>
                            <div class="row p-2">
                                <div class="col-xl-12 col-md-12">
                                    <label class="form-label" for=""
                                    >Alamat <span class="txt-danger f-w-600">*</span></label
                                    >
                                    <textarea
                                        class="form-control"
                                        id="alamatPelanggan"
                                        required
                                    ></textarea>
                                </div>
                            </div>
                            <div class="row p-2">
                                <div class="col-xl-6 col-md-6">
                                    <label class="form-label" for=""
                                    >Provinsi <span class="txt-danger f-w-600">*</span></label
                                    >
                                    <select id="masterprovinsi" class="select2 form-control" name="master_provinsi"></select>
                                </div>
                                <div class="col-xl-6 col-md-6">
                                    <label class="form-label" for=""
                                    >Kota / Kabupaten <span class="txt-danger f-w-600">*</span></label
                                    >
                                    <select id="masterkota" class="select2 form-control" name="master_kota">
                                        <option value="">Pilih Kota/Kab</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row p-2">
                                <div class="col-xl-6 col-md-6">
                                    <label class="form-label" for=""
                                    >Kecamatan <span class="txt-danger f-w-600">*</span></label
                                    >
                                    <select id="masterkecamatan" class="select2 form-control" name="master_kecamatan">
                                        <option value="">Pilih Kecamatan</option>
                                    </select>
                                </div>
                                <div class="col-xl-6 col-md-6">
                                    <label class="form-label" for=""
                                    >Kelurahan / Desa <span class="txt-danger f-w-600">*</span></label
                                    >
                                    <select id="masterkelurahan" class="select2 form-control" name="master_kelurahan">
                                        <option value="">Pilih Kelurahan / Desa</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row p-2">
                                <div class="col-xl-6 col-md-6">
                                    <label class="form-label" for=""
                                    >Kode Pos</label
                                    >
                                    <input
                                    class="form-control"
                                    id="kodepos"
                                    type="text"
                                    disabled
                                    />
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-12 col-md-12 box-col-12">
                            <p class="text-uppercase"><b>Klasifikasi Pelanggan</b></p>
                            <div class="row p-2">
                                <div class="col-xl-6 col-md-6">
                                    <label class="form-label" for=""
                                    >Kategori Pelanggan</label
                                    >
                                    <input
                                    class="form-control"
                                    id="kategoriPelanggan"
                                    type="text"
                                    name="kategori_pelanggan"
                                    disabled
                                    />
                                </div>
                            </div>
                            <!-- Form Apoteker -->
                            <div id="formApoteker" style="display: none;">
                                <div class="row p-2">
                                    <div class="col-xl-6 col-md-6">
                                        <label class="form-label" for=""
                                        >Nama Apoteker <span class="txt-danger f-w-600">*</span></label
                                        >
                                        <input
                                        class="form-control"
                                        id="namaApoteker"
                                        type="text"
                                        placeholder="nama apoteker"
                                        />
                                    </div>
                                </div>
                                <div class="row p-2">
                                    <div class="col-xl-6 col-md-6">
                                        <label class="form-label" for=""
                                        >No. S.I.P.A <span class="txt-danger f-w-600">*</span></label
                                        >
                                        <input
                                        class="form-control"
                                        id="noSipa"
                                        type="text"
                                        placeholder="no SIPA"
                                        />
                                    </div>
                                    <div class="col-xl-6 col-md-6">
                                        <label class="form-label" for=""
                                        >No. S.I.A <span class="txt-danger f-w-600">*</span></label
                                        >
                                        <input
                                        class="form-control"
                                        id="noSia"
                                        type="text"
                                        placeholder="no SIA"
                                        />
                                    </div>
                                </div>
                                <div class="row p-2">
                                    <div class="col-xl-6 col-md-6">
                                        <label class="form-label" for=""
                                        >Masa Berlaku S.I.P.A <span class="txt-danger f-w-600">*</span></label
                                        >
                                        <input
                                        class="form-control digits"
                                        type="date"
                                        id="edSipa"
                                        />
                                    </div>
                                    <div class="col-xl-6 col-md-6">
                                        <label class="form-label" for=""
                                        >Masa Berlaku S.I.A <span class="txt-danger f-w-600">*</span></label
                                        >
                                        <input
                                        class="form-control digits"
                                        type="date"
                                        id="edSia"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-12 col-md-12 box-col-12">
                            <p class="text-uppercase"><b>Informasi Pajak</b></p>
                            <div class="row p-2">
                                <div class="col-xl-12 col-md-12">
                                    <label class="form-label" for=""
                                    >Status Pajak <span class="txt-danger f-w-600">*</span></label
                                    >
                                    <select id="statusPajak" class="select2 form-control" name="status_pajak" required>
                                        <option value="01">01 - Bukan Pemungut PPN</option>
                                        <option value="02">02 - Pemungut Bendaharawan</option>
                                        <option value="03">03 - Pemungut Selain Bendaharawan</option>
                                        <option value="04">04 - DPP Nilai Lain</option>
                                        <option value="05">05 - Demeed Pajak Masukan</option>
                                        <option value="06">06 - Penyerahan Lainnya</option>
                                        <option value="07">07 - Penyerahan PPN-nya Tidak Dipungut</option>
                                        <option value="08">08 - Penyerahan PPN-nya Tidak Dibebaskan</option>
                                        <option value="09">09 - Penyerahan Aktiva (Pasal 16D UU PPN)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row p-2">
                                <div class="col-xl-6 col-md-6">
                                    <label class="form-label" for=""
                                    >NPWP</label
                                    >
                                    <input
                                    class="form-control"
                                    id="npwpPelanggan"
                                    type="text"
                                    placeholder="npwp"
                                    />
                                </div>
                                <div class="col-xl-6 col-md-6">
                                    <label class="form-label" for=""
                                    >Nama</label
                                    >
                                    <input
                                    class="form-control"
                                    id="namanpwpPelanggan"
                                    type="text"
                                    placeholder="nama"
                                    />
                                </div>
                            </div>
                            <div class="row p-2">
                                <div class="col-xl-12 col-md-12">
                                    <label class="form-label" for=""
                                    >Alamat</label
                                    >
                                    <textarea
                                        class="form-control"
                                        id="alamatnpwpPelanggan"
                                    ></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-12 col-md-12 box-col-12">
                            <p class="text-uppercase"><b>Informasi Tambahan</b></p>
                            <div class="row p-2">
                                <div class="col-xl-6 col-md-6">
                                    <label class="form-label" for=""
                                    >Kontruksi Bangunan</label
                                    >
                                    <select id="tipeBangunan" class="select2 form-control" name="tipe_bangunan">
                                        <option value="MALL">MALL</option>
                                        <option value="RUKO">RUKO</option>
                                        <option value="KIOS">KIOS</option>
                                        <option value="TOKO">TOKO</option>
                                        <option value="WARUNG">WARUNG</option>
                                    </select>
                                </div>
                                <div class="col-xl-6 col-md-6">
                                    <label class="form-label" for=""
                                    >Status Hak Bangunan</label
                                    >
                                    <select id="hakBangunan" class="select2 form-control" name="hak_bangunan">
                                        <option value="MILIK">MILIK</option>
                                        <option value="SEWA">SEWA</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-12 col-md-12 box-col-12">
                            <div class="row p-2">
                                <div class="col-xl-6 col-md-6">
                                    <input
                                        class="form-check-input"
                                        id="cekVerifikasi"
                                        type="checkbox"
                                    />
                                    <label class="form-check-label" for="invalidCheck"
                                        >Sudah yakin dengan data yang telah diisi?  <span class="txt-danger f-w-600">*</span></label
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-12 col-md-12 box-col-12">
                            <button class="btn btn-primary" type="submit">
                                <i class="fa fa-save"></i> Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row size-column">
        <div class="col-xl-12 col-md-12 box-col-12">
        </div>
    </div>
</div>
<!-- Container-fluid Ends-->
<?= $this->endSection(); ?>
<!-- END : End Main Content-->