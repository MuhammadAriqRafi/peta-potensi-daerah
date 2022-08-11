<?php

namespace App\Models;

use App\Controllers\Interfaces\CRUDInterface;
use App\Controllers\Interfaces\DatatableInterface;
use CodeIgniter\Model;

class Category extends Model implements DatatableInterface, CRUDInterface
{
    protected $DBGroup          = 'default';
    protected $table            = 'category';
    protected $primaryKey       = 'category_id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['post_type', 'image', 'title', 'slug', 'description'];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    public function getRecords($start, $length, $orderColumn, $orderDirection)
    {
        return $this->select('category_id, title')
            ->orderBy($orderColumn, $orderDirection)
            ->findAll($length, $start);
    }

    public function getRecordSearch($search, $start, $length, $orderColumn, $orderDirection)
    {
        return $this->select('category_id, title')
            ->orderBy($orderColumn, $orderDirection)
            ->like('title', $search)
            ->findAll($length, $start);
    }

    public function getTotalRecords()
    {
        return $this->countAllResults() ?? 0;
    }

    public function getTotalRecordSearch($search)
    {
        return $this->select('category_id, title')
            ->like('title', $search)
            ->countAllResults();
    }

    public function fetchValidationRules($options = null): array
    {
        return $rules = [
            'title' => $options['title'] ?? '' . 'required',
            'image' => $options['image'] ?? '' . 'max_size[image,1024]|is_image[image]|mime_in[image,image/jpg,image/jpeg,image/png]',
        ];
    }
}
