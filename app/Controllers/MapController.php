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
        $category = new Category();

        $data = [
            'title' => 'Tambah Map',
            'validation' => Services::validation(),
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
            'youtube' => $this->request->getVar('youtube') ?? '',
            'address' => $this->request->getVar('address'),
        ];
        $others = json_encode($data);

        // ? Move and get the image name
        $image = $this->request->getFile('cover');
        if ($image->getError() != 4) $imageName = storeAs($image, 'img', 'map');
        else $imageName = null;

        $this->maps->save([
            'category_id' => base64_decode($this->request->getVar('category')),
            'date_publish' => $this->request->getVar('date_publish'),
            'kecamatan' => $this->request->getVar('kecamatan'),
            'status' => $this->request->getVar('status'),
            'slug' => url_title($title, '-', true),
            'image' => $imageName,
            'post_type' => 'map',
            'others' => $others,
            'title' => $title,
        ]);

        return redirect()->route('backend.maps.index')->with('success', 'Map berhasil ditambahkan!');
    }

    public function edit($id = null)
    {
        $category = new Category();
        $map = $this->maps->find(base64_decode($id));
        $others = json_decode($map['others'], true);

        $data = [
            'title' => 'Ubah Map',
            'map' => $map,
            'others' => $others,
            'validation' => Services::validation(),
            'categories' => $category->select('category_id, title')->get()->getResultArray(),
            'statuses' => ['draft', 'publish']
        ];

        return view('map/edit', $data);
    }

    public function update($id = null)
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
            'youtube' => $this->request->getVar('youtube') ?? '',
            'address' => $this->request->getVar('address'),
        ];
        $others = json_encode($data);

        // ? Move and get the image name
        $image = $this->request->getFile('cover');
        $oldImage = $this->request->getVar('oldImage');

        if ($image->getError() != 4) {
            $imageName = storeAs($image, 'img', 'map');
            unlink('img/' . $oldImage);
        } else $imageName = $oldImage;

        $this->maps->save([
            'post_id' => $id,
            'category_id' => base64_decode($this->request->getVar('category')),
            'date_publish' => $this->request->getVar('date_publish'),
            'date_modify' => date("Y-m-d H:i:s", strtotime('now')),
            'kecamatan' => $this->request->getVar('kecamatan'),
            'status' => $this->request->getVar('status'),
            'slug' => url_title($title, '-', true),
            'image' => $imageName,
            'post_type' => 'map',
            'others' => $others,
            'title' => $title,
        ]);

        return redirect()->route('backend.maps.index')->with('success', 'Map berhasil diubah!');
    }
}
