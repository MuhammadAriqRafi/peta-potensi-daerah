<?php

namespace App\Models;

use App\Controllers\Interfaces\CRUDInterface;
use App\Controllers\Interfaces\DatatableInterface;
use CodeIgniter\Model;

class Popup extends Model implements DatatableInterface, CRUDInterface
{
    protected $DBGroup          = 'default';
    protected $table            = 'popup';
    protected $primaryKey       = 'popup_id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['title', 'value', 'status'];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    public function fetchValidationRules(string $options = null): array
    {
        return $rules = [
            'title' => 'required',
            'image' => [
                'rules' => 'max_size[image,1024]|is_image[image]|mime_in[image,image/jpg,image/jpeg,image/png]' . $options,
                'errors' => [
                    'uploaded' => 'Image is required'
                ]
            ]
        ];
    }

    public function isActivePopupExist()
    {
        $activePopup = $this->where('status', 'active')->countAllResults();
        if ($activePopup) return true;
        return false;
    }

    public function getRecords($start, $length, $orderColumn, $orderDirection)
    {
        return $this->select('title, value, status, popup_id')
            ->orderBy($orderColumn, $orderDirection)
            ->findAll();
    }

    public function getTotalRecords()
    {
        return $this->countAllResults() ?? 0;
    }

    public function getRecordSearch($search, $start, $length, $orderColumn, $orderDirection)
    {
        return $this->select('title, value, status, popup_id')
            ->orderBy($orderColumn, $orderDirection)
            ->like('title', $search)
            ->orLike('value', $search)
            ->findAll($length, $start);
    }

    public function getTotalRecordSearch($search)
    {
        return $this->select('title, value, status popup_id')
            ->orderBy('popup_id', 'DESC')
            ->like('title', $search)
            ->orLike('value', $search)
            ->countAllResults();
    }
}
