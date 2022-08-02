<?php

namespace App\Models;

use App\Controllers\Interfaces\CRUDInterface;
use CodeIgniter\Model;

class Menu extends Model implements CRUDInterface
{
    protected $DBGroup          = 'default';
    protected $table            = 'menu';
    protected $primaryKey       = 'menu_id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['title', 'url', 'target'];

    public function fetchValidationRules(): array
    {
        return $rules = [
            'title' => 'required',
            'url' => 'required',
            'target' => 'required'
        ];
    }
}
