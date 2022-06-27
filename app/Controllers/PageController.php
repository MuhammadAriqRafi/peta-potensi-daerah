<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Visitor;

class PageController extends BaseController
{
    protected $categories;
    protected $menus;
    protected $visitors;

    public function __construct()
    {
        $this->categories = new Category();
        $this->menus = new Menu();
        $this->visitors = new Visitor();
    }

    public function index()
    {
        $data = [
            'title' => 'Peta Potensi Daerah Lampung Timur',
            'categories' => $this->categories->findAll(),
            'menus' => $this->menus->findAll()
        ];

        return view('pages/index', $data);
    }

    public function backend_dashboard()
    {
        $data = [
            'title' => 'Dashboard',
            'visitors' => [
                'total' => $this->visitors->countAllResults(),
                'week' => $this->visitors->countVisitorInAWeek(),
                'month' => $this->visitors->countVisitorInAMonth(),
                'today' => $this->visitors->countDailyVisitor()
            ]
        ];

        return view('pages/dashboard', $data);
    }
}
