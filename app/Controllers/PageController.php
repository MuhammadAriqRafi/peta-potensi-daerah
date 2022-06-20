<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Category;
use App\Models\Menu;

class PageController extends BaseController
{
    protected $categories;
    protected $menu;

    public function __construct()
    {
        $this->categories = new Category();
        $this->menu = new Menu();
    }

    public function index()
    {
        $categories = $this->categories->findAll();
        $menus = $this->menu->findAll();

        $data = [
            'title' => 'Peta Potensi Daerah Lampung Timur',
            'categories' => $categories,
            'menus' => $menus
        ];

        return view('index', $data);
    }

    public function tentang()
    {
        return "Tentang Kami";
    }
}
