<?php

namespace App\Models\MenuModel;

use CodeIgniter\Model;

class MenuModel extends Model
{
  protected $table = 'mst_menu';
  protected $useTimestamps = true;
  protected $allowedFields = [
    'level_id',
    'parent_menu',
    'id',
    'name',
    'menu_seq',
    'link',
    'flg_used',
    'user_create',
    'create_date',
    'user_update',
    'update_date',
    'icon'
  ];

  public function getMenuUserData($username)
  {
    $query = $this->db->query("SELECT level_code AS level_id, parent_code AS parent_menu, menu_code AS menu_id, sort_menu AS menu_seq, menu_name AS name, link AS link_menu, fUll_path AS path_name, last_number AS is_last, icon FROM f_tv_getmenuuser_data(?)", [$username]);
    return $query->getResultArray();
  }

  public function getBreadcrumb($username, $link_menu)
  {
    $query = $this->db->query("SELECT fUll_path AS path_name FROM f_tv_getmenuuser_data(?) WHERE link = ?", [$username, $link_menu]);
    return $query->getRowArray();
  }

  public function buildMenuTree(array $elements, $parentId = null)
  {
    $branch = array();
    foreach ($elements as $element) {
      if ($element['parent_menu'] == $parentId) {
        $children = $this->buildMenuTree($elements, $element['menu_id']);
        if ($children) {
          $element['children'] = $children;
        }
        $branch[] = $element;
      }
    }
    return $branch;
  }
}
