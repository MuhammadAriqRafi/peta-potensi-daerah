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
            'categories' => $category->select('category_id, title')->get()->getResultArray(),
            'statuses' => ['draft', 'publish']
        ];

        return view('map/create', $data);
    }

    public function store()
    {
        if (!$this->validate([
            'title' => 'required',
            'date_publish' => 'required',
            'category' => 'required',
            'kecamatan' => 'required',
            'description' => 'required',
            'status' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'address' => 'required'
        ])) {
            return redirect()->back()->withInput();
        }

        $title = $this->request->getVar('title');

        // ? Convert data to json before inserting to 'others' field in db
        $data = [
            'latitude' => $this->request->getVar('latitude'),
            'longitude' => $this->request->getVar('longitude'),
            'description' => $this->request->getVar('description'),
            'youtube' => $this->request->getVar('youtube'),
            'address' => $this->request->getVar('address'),
        ];
        $others = json_encode($data);

        // ? Move and get the image name
        $image = $this->request->getFile('cover');
        if ($image->getError() != 4) $imageName = storeAs($image, 'img', 'map');
        else $imageName = null;

        $this->maps->save([
            'category_id' => base64_decode($this->request->getVar('category')),
            'post_type' => 'map',
            'date_publish' => $this->request->getVar('date_publish'),
            'image' => $imageName,
            'slug' => url_title($title, '-', true),
            'title' => $title,
            'kecamatan' => $this->request->getVar('kecamatan'),
            'others' => $others,
            'status' => $this->request->getVar('status')
        ]);

        return redirect()->back()->with('success', 'Map berhasil ditambahkan!');
    }

    // TODO: Add destroy post method (consider putting it in new controller PostController, so map and about controller can call the same method)
}
