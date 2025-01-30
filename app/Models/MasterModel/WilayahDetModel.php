<?php

namespace App\Models\MasterModel;

use CodeIgniter\Model;

class WilayahDetModel extends Model
{
    protected $table = ''; // Table name is dynamically set in each method.
    protected $allowedFields = []; // No fields are directly updated via this model.

    /**
     * Get distinct province_id and name where flg_used = 't'.
     * 
     * @return array
     */
    public function getAreaProvinsi()
    {
        return $this->db->table('mst_area_province')
            ->select('province_id, name')
            ->where('flg_used', 't')
            ->orderBy('province_id')
            ->get()
            ->getResultArray();
    }

    /**
     * Get distinct city_id and name where flg_used = 't' and province_id = [$province_area].
     * 
     * @param string $province_area
     * @return array
     */
    public function getAreaKotaKab($province_area)
    {
        if (!$province_area) return [];
        return $this->db->table('mst_area_city')
            ->select('city_id, name')
            ->where('flg_used', 't')
            ->where('province_id', $province_area)
            ->orderBy('city_id')
            ->get()
            ->getResultArray();
    }

    /**
     * Get distinct district_id and name where flg_used = 't', province_id = [$province_area], and city_id = [$city_area].
     * 
     * @param string $province_area
     * @param string $city_area
     * @return array
     */
    public function getAreaKecamatan($province_area, $city_area)
    {
        if (!$province_area || !$city_area) return [];
        return $this->db->table('mst_area_district')
            ->select('district_id, name')
            ->where('flg_used', 't')
            ->where('province_id', $province_area)
            ->where('city_id', $city_area)
            ->orderBy('district_id')
            ->get()
            ->getResultArray();
    }

    /**
     * Get distinct subdistrict_id and name where flg_used = 't', province_id = [$province_area], city_id = [$city_area], and district_id = [$district_area].
     * 
     * @param string $province_area
     * @param string $city_area
     * @param string $district_area
     * @return array
     */
    public function getAreaKelurahan($province_area, $city_area, $district_area)
    {
        if (!$province_area || !$city_area || !$district_area) return [];
        return $this->db->table('mst_area_subdistrict')
            ->select('subdistrict_id, name')
            ->where('flg_used', 't')
            ->where('province_id', $province_area)
            ->where('city_id', $city_area)
            ->where('district_id', $district_area)
            ->orderBy('subdistrict_id')
            ->get()
            ->getResultArray();
    }

    /**
     * Get zip_code where flg_used = 't', province_id = [$province_area], city_id = [$city_area], district_id = [$district_area], and subdistrict_id = [$subdistrict_area].
     * 
     * @param string $province_area
     * @param string $city_area
     * @param string $district_area
     * @param string $subdistrict_area
     * @return array
     */
    public function getAreaKodePos($province_area, $city_area, $district_area, $subdistrict_area)
    {
        if (!$province_area || !$city_area || !$district_area || !$subdistrict_area) return [];
        return $this->db->table('mst_area_subdistrict')
            ->select('zip_code')
            ->where('flg_used', 't')
            ->where('province_id', $province_area)
            ->where('city_id', $city_area)
            ->where('district_id', $district_area)
            ->where('subdistrict_id', $subdistrict_area)
            ->get()
            ->getResultArray();
    }
}
