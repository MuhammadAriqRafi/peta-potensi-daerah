<?php

namespace App\Models;

use CodeIgniter\Model;
use Config\Services;

class Post extends Model
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

    // Ajax Methods

    // ? Maps
    public function ajaxGetMaps($start, $length)
    {
        return $this->select('post.title, category.title as category, author, DATE(date_publish), status, post_id')
            ->join('category', 'post.category_id = category.category_id')
            ->orderBy('date_create', 'DESC')
            ->findAll($length, $start);
    }

    public function ajaxGetTotalMaps()
    {
        return $this->where('post_type', 'map')->countAllResults() ?? 0;
    }

    public function ajaxGetMapsSearch($search, $start, $length)
    {
        return $this->select('post.title, category.title as category, author, DATE(date_publish), status, post_id')
            ->join('category', 'post.category_id = category.category_id')
            ->orderBy('date_create', 'DESC')
            ->where('post.post_type', 'map')
            ->like('post.title', $search)
            ->orLike('author', $search)
            ->orLike('category.title', $search)
            ->orLike('DATE(date_publish)', $search)
            ->orLike('status', $search)
            ->findAll($length, $start);
    }

    public function ajaxGetTotalMapsSearch($search)
    {
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
    }

    // ? Profiles
    public function ajaxGetProfiles($start, $length)
    {
        return $this->select('title, author, DATE(date_publish), status, post_id')
            ->where('post_type', 'profil')
            ->orderBy('date_create', 'DESC')
            ->findAll($length, $start);
    }

    public function ajaxGetTotalProfiles()
    {
        return $this->where('post_type', 'profil')->countAllResults() ?? 0;
    }

    public function ajaxGetProfilesSearch($search, $start, $length)
    {
        $profilesSearch = $this->query('SELECT post_type, title, author, DATE(date_publish), status, post_id FROM post WHERE title LIKE "%' . $search . '%" OR author LIKE "%' . $search . '%" OR status LIKE "%' . $search . '%" OR DATE(date_publish) LIKE "%' . $search . '%" HAVING post_type = "profil" LIMIT ' . $length . ' OFFSET ' . $start . '')
            ->getResultArray();

        foreach ($profilesSearch as $key => $value) {
            unset($profilesSearch[$key]['post_type']);
        }

        return $profilesSearch;
    }

    public function ajaxGetTotalProfilesSearch($search)
    {
        return $this->query('SELECT post_type, title, author, DATE(date_publish), status, post_id FROM post WHERE title LIKE "%' . $search . '%" OR author LIKE "%' . $search . '%" OR status LIKE "%' . $search . '%" OR DATE(date_publish) LIKE "%' . $search . '%" HAVING post_type = "profil"')
            ->getNumRows();
    }

    public function getProfileValidationRules()
    {
        $rules = [
            'title' => 'required',
            'date_publish' => 'required',
            'content' => 'required',
            'status' => 'required',
            'description' => 'required',
        ];

        return $rules;
    }
}
