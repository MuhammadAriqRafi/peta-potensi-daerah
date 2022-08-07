<?php

namespace App\Models;

use App\Controllers\Interfaces\CRUDInterface;
use App\Controllers\Interfaces\DatatableInterface;
use CodeIgniter\Model;

class Post extends Model implements DatatableInterface, CRUDInterface
{
    protected $DBGroup          = 'default';
    protected $table            = 'post';
    protected $primaryKey       = 'post_id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['post_type', 'slug', 'title', 'date_publish', 'date_modify', 'status', 'description', 'content', 'others', 'category_id', 'image', 'kecamatan'];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Custom Property
    protected $context;

    public function __construct($context)
    {
        parent::__construct();
        $this->context = $context;
    }

    public function getRecords($start, $length, $orderColumn, $orderDirection)
    {
        if ($this->context == 'map') {
            return $this->select('post.title, category.title as category, author, DATE(date_publish) as date_publish, status, post_id')
                ->join('category', 'post.category_id = category.category_id')
                ->orderBy($orderColumn ?? 'date_publish', $orderDirection)
                ->findAll($length, $start);
        } else if ($this->context == 'profil') {
            return $this->select('title, author, DATE(date_publish), status, post_id')
                ->where('post_type', 'profil')
                ->orderBy($orderColumn, $orderDirection)
                ->findAll($length, $start);
        }
    }

    public function getRecordSearch($search, $start, $length, $orderColumn, $orderDirection)
    {
        if ($this->context == 'map') {
            return $this->select('post.title, category.title as category, author, DATE(date_publish) as date_publish, status, post_id')
                ->join('category', 'post.category_id = category.category_id')
                ->orderBy($orderColumn ?? 'date_publish', $orderDirection)
                ->where('post.post_type', 'map')
                ->like('post.title', $search)
                ->orLike('author', $search)
                ->orLike('category.title', $search)
                ->orLike('DATE(date_publish)', $search)
                ->orLike('status', $search)
                ->findAll($length, $start);
        } else if ($this->context == 'profil') {
            $profilesSearch = $this->query('SELECT post_type, title, author, DATE(date_publish), status, post_id FROM post WHERE title LIKE "%' . $search . '%" OR author LIKE "%' . $search . '%" OR status LIKE "%' . $search . '%" OR DATE(date_publish) LIKE "%' . $search . '%" HAVING post_type = "profil" LIMIT ' . $length . ' OFFSET ' . $start . '')
                ->getResultArray();

            foreach ($profilesSearch as $key => $value) {
                unset($profilesSearch[$key]['post_type']);
            }

            return $profilesSearch;
        }
    }

    public function getTotalRecords()
    {
        return $this->where('post_type', $this->context)->countAllResults() ?? 0;
    }

    public function getTotalRecordSearch($search)
    {
        if ($this->context == 'map') {
            return $this->select('post.title, category.title as category, author, DATE(date_publish), status, post_id')
                ->join('category', 'post.category_id = category.category_id')
                ->orderBy('date_create', 'DESC')
                ->where('post.post_type', 'map')
                ->like('post.title', $search)
                ->orLike('author', $search)
                ->orLike('category.title', $search)
                ->orLike('DATE(date_publish)', $search)
                ->orLike('status', $search)
                ->countAllResults();
        } else if ($this->context == 'profil') {
            return $this->query('SELECT post_type, title, author, DATE(date_publish), status, post_id FROM post WHERE title LIKE "%' . $search . '%" OR author LIKE "%' . $search . '%" OR status LIKE "%' . $search . '%" OR DATE(date_publish) LIKE "%' . $search . '%" HAVING post_type = "profil"')
                ->getNumRows();
        }
    }

    public function fetchValidationRules(): array
    {
        if ($this->context == 'map') {
            return $rules = [
                'title' => 'required',
                'category' => 'required',
                'kecamatan' => 'required',
                'description' => 'required',
                'status' => 'required',
                'latitude' => 'required',
                'longitude' => 'required',
                'address' => 'required'
            ];
        } else if ($this->context == 'profil') {
            return $rules = [
                'title' => 'required',
                'date_publish' => 'required',
                'content' => 'required',
                'status' => 'required',
                'description' => 'required',
            ];
        }
    }

    public function getMaps()
    {
        return $this->select('post_id, post.post_type, post.title, category.title as category, author, date_publish, status')
            ->join('category', 'post.category_id = category.category_id')
            ->orderBy('date_create', 'DESC')
            ->findAll();
    }

    public function getProfiles()
    {
        return  $this->select('post_id, title, author, date_publish, status')
            ->where('post_type', 'profil')
            ->get()->getResultArray();
    }
}
