<?php

namespace App\Models;

use App\Controllers\Interfaces\CRUDInterface;
use CodeIgniter\Model;
use Config\Services;

class Setting extends Model implements CRUDInterface
{
    protected $DBGroup          = 'default';
    protected $table            = 'settings';
    protected $primaryKey       = 'setting_id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['value', 'class', 'sort'];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    public function fetchValidationRules($options = null): array
    {
        return $rules = [
            'value' => $options ?? 'required',
        ];
    }
}
