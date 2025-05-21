<?php

namespace App\Models\RealisasiKunjModel;

use CodeIgniter\Model;

class RealisasiKunjModel extends Model
{
    protected $table      = 'trn_real';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id_ref', 'value', 'probability', 'latitude', 'longitude', 'pict', 'description', 'user_update', 'update_date', 'flg_verify', 'user_verify', 'date_verify', 'feedback'];

    //data verifikasi realisasi kunjungan
    public function getDataVerifikasiRealisasi($nik, $tanggal)
    {
        $builder = $this->db->table('trn_plan a')
            ->select("
                DISTINCT ON (a.cust_id) cust_id,
                b.id, 
                b.id_ref, 
                a.branch_id, 
                a.nik, 
                a.date, 
                f_tv_customer_name(a.cust_id) AS cust_name, 
                b.value, 
                b.probability, 
                b.latitude, 
                b.longitude, 
                b.description, 
                b.status,
                b.flg_visit,
                a.flg_non_route,
                f_tv_flg_noo_cust(a.cust_id) AS flg_noo,
                b.pict,
                SUM(b.value) OVER (PARTITION BY a.cust_id,a.nik,a.date)::NUMERIC AS tot_value
            ")
            ->join('trn_real b', 'a.id = b.id_ref', 'inner')
            ->where('a.nik', $nik)
            ->where('a.date', $tanggal)
            ->where('b.flg_verify IS NULL')
            ->orderBy('a.cust_id, a.date');

        return $builder->get()->getResult();
    }

    //data detail verifikasi realisasi kunjungan
    public function getDataVerifikasiRealisasiDet($nik, $tanggal, $pelanggan)
    {
        $builder = $this->db->table('trn_plan a')
            ->select("
                f_tv_user_cust_name(a.cust_user_id) AS cust_user_name,
                a.group_id,
                f_tv_group_name(a.group_id) AS group_name,
                a.subgroup_id,
                f_tv_subgroup_name(a.group_id, a.subgroup_id) AS subgroup_name,
                a.class_id, 
                f_tv_class_name(a.group_id, a.subgroup_id, a.class_id) AS class_name,
                b.value,
                b.probability
            ")
            ->join('trn_real b', 'a.id = b.id_ref', 'inner')
            ->where('a.nik', $nik)
            ->where('a.date', $tanggal)
            ->where('a.cust_id', $pelanggan)
            ->where('b.flg_verify IS NULL')
            ->orderBy('a.group_id, a.subgroup_id, a.class_id');

            return $builder->get()->getResult();
    }

    // Update data realisasi kunjungan berdasarkan ID
    public function updateRealisasi($date, $nik, $cust_id, $data)
    {
        // Update trn_real berdasarkan id_ref yang sesuai dengan trn_plan
        return $this->db->table('trn_real')
            ->whereIn('id_ref', function ($builder) use ($date, $nik, $cust_id) {
                $builder->select('id')
                    ->from('trn_plan')
                    ->where('date', $date)
                    ->where('nik', $nik)
                    ->where('cust_id', $cust_id);
            })
            ->update($data);
    }

    //data monitoring realisasi kunjungan
    public function getDataMonitoringRealisasi($nik, $tanggal_1, $tanggal_2)
    {
        $builder = $this->db->table('trn_plan a')
            ->select("
                DISTINCT ON (a.cust_id) cust_id,
                b.id, 
                b.id_ref, 
                a.branch_id, 
                a.nik, 
                a.date, 
                f_tv_customer_name(a.cust_id) AS cust_name, 
                b.value, 
                b.probability, 
                b.latitude, 
                b.longitude, 
                b.description,
                b.feedback, 
                b.status,
                b.flg_visit,
                a.flg_non_route,
                f_tv_flg_noo_cust(a.cust_id) AS flg_noo,
                b.pict,
                SUM(b.value) OVER (PARTITION BY a.cust_id,a.nik,a.date)::NUMERIC AS tot_value
            ")
            ->join('trn_real b', 'a.id = b.id_ref', 'inner')
            ->where('b.flg_verify', 't')
            ->where('a.nik', $nik)
            ->where('a.date >=', $tanggal_1)
            ->where('a.date <=', $tanggal_2)
            ->orderBy('a.cust_id, a.date');

        return $builder->get()->getResult();
    }

    //data detail monitoring realisasi kunjungan
    public function getDataMonitoringRealisasiDet($nik, $tanggal, $pelanggan)
    {
        $builder = $this->db->table('trn_plan a')
            ->select("
                f_tv_user_cust_name(a.cust_user_id) AS cust_user_name,
                a.group_id,
                f_tv_group_name(a.group_id) AS group_name,
                a.subgroup_id,
                f_tv_subgroup_name(a.group_id, a.subgroup_id) AS subgroup_name,
                a.class_id, 
                f_tv_class_name(a.group_id, a.subgroup_id, a.class_id) AS class_name,
                b.value,
                b.probability
            ")
            ->join('trn_real b', 'a.id = b.id_ref', 'inner')
            ->where('a.nik', $nik)
            ->where('a.date', $tanggal)
            ->where('a.cust_id', $pelanggan)
            ->where('b.flg_verify', 't')
            ->orderBy('a.group_id, a.subgroup_id, a.class_id');

            return $builder->get()->getResult();
    }
}