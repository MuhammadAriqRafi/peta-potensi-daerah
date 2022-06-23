<?php

namespace App\Models;

use CodeIgniter\Model;

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
        return $this->db->table('post')
            ->select('post_id, post.post_type, post.title, category.title as category, author, date_publish, status')
            ->join('category', 'post.category_id = category.category_id')
            ->get()->getResultArray();
    }

    public function getProfiles()
    {
        return  $this->db->table('post')
            ->select('post_id, post_type, title, author, date_publish, status')
            ->where('post_type', 'profil')
            ->get()->getResultArray();
    }
}
