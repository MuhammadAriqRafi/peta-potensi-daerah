<?php

namespace App\Models;

use CodeIgniter\Model;

class Popup extends Model
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

    public function getPopupValidationRules()
    {
        return $rules = [
            'title' => 'required',
            'image' => [
                'rules' => 'uploaded[image]|max_size[image,1024]|is_image[image]|mime_in[image,image/jpg,image/jpeg,image/png]',
                'errors' => [
                    'uploaded' => 'Image is required'
                ]
            ]
        ];
    }
}
