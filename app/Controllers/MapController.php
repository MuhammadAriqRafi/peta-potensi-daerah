<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Category;
use App\Models\Post;
use Config\Services;

class MapController extends BaseController
{
    protected $maps;

    public function __construct()
    {
        $this->maps = new Post();
    }

    public function index()
    {
        $data = [
            'title' => 'Map Settings',
            'maps' => $this->maps->getMaps(),
            'validation' => Services::validation()
        ];


        return view('map/index', $data);
    }

    public function create()
    {
        // TODO: Cara filter field record yang dipanggil dari database

        $category = new Category();

        $data = [
            'title' => 'Tambah Map',
            'validation' => Services::validation(),
            'kecamatans' => $this->maps->where('post_type', 'map')->findColumn('kecamatan'),
            'categories' => $category->select('category_id, title')->get()->getResultArray()
        ];

        return view('map/create', $data);
    }

    public function store()
    {
        // TODO: decode id dari request category
        dd($this->request->getVar());
    }
}
