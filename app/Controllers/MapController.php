<?php

namespace App\Controllers;

use App\Controllers\CRUDController;
use App\Models\Category;
use App\Models\Post;
use Config\Services;

class MapController extends CRUDController
{
    public function __construct()
    {
        parent::__construct(new Post('map'));
    }

    public function index()
    {
        $categories = new Category();

        $data = [
            'title' => 'Map Settings',
            'maps' => $this->model->getMaps(),
            'categories' => $categories->select('category_id, title')->get()->getResultArray(),
            'statuses' => ['draft', 'publish'],
            'indexUrl' => '/backend/maps/ajaxIndex',
            'storeUrl' => '/backend/maps/ajaxStore',
            'destroyUrl' => '/backend/maps/ajaxDestroy/',
            'updateUrl' => '/backend/maps/ajaxUpdate/',
            'editUrl' => '/backend/maps/ajaxEdit/',
        ];

        foreach ($data['categories'] as $key => $category) {
            $data['categories'][$key]['category_id'] = base64_encode($category['category_id']);
        }

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

    public function edit($id = null)
    {
        return $this->response->setJSON([$id]);
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

    // ? Ajax Methods
    public function ajaxIndex()
    {
        return parent::ajaxIndex();
    }

    public function ajaxStore()
    {
        // ? Convert data to json before inserting to 'others' field in db
        $othersData = [
            'latitude' => $this->request->getVar('latitude'),
            'longitude' => $this->request->getVar('longitude'),
            'description' => $this->request->getVar('description'),
            'youtube' => $this->request->getVar('youtube') ?? '',
            'address' => $this->request->getVar('address'),
        ];

        $others = json_encode($othersData);
        $title = $this->request->getVar('title');
        $cover = $this->request->getFile('cover');

        $data = [
            'image' => '',
            'category_id' => base64_decode($this->request->getVar('category')),
            'date_publish' => $this->request->getVar('date_publish'),
            'kecamatan' => $this->request->getVar('kecamatan'),
            'status' => $this->request->getVar('status'),
            'slug' => url_title($title, '-', true),
            'post_type' => 'map',
            'others' => $others,
            'title' => $title,
        ];

        if ($cover) {
            $file = [
                'image_file' => $cover,
                'image_path' => 'img/',
                'image_context' => 'map'
            ];
            $data = array_merge($data, $file);
        } else {
            $data['image'] = null;
        }

        $this->setData($data);
        return parent::ajaxStore();
    }

    public function ajaxEdit($id = null)
    {
        $this->setData(['select' => 'title, DATE(date_publish) as date_publish, category_id, kecamatan, others, status, image, post_id']);
        return parent::ajaxEdit($id);
    }

    public function ajaxUpdate($id = null)
    {
        $title = $this->request->getVar('title');

        // ? Convert data to json before inserting to 'others' field in db
        $others = [
            'latitude' => $this->request->getVar('latitude'),
            'longitude' => $this->request->getVar('longitude'),
            'description' => $this->request->getVar('description'),
            'youtube' => $this->request->getVar('youtube') ?? '',
            'address' => $this->request->getVar('address'),
        ];

        $id = base64_decode($id);
        $others = json_encode($others);
        $cover = $this->request->getFile('cover');
        $oldImage = $this->model->select('image')->find($id)['image'];

        $data = [
            'image' => $oldImage,
            'post_id' => $id,
            'category_id' => base64_decode($this->request->getVar('category')),
            'date_publish' => $this->request->getVar('date_publish'),
            'date_modify' => date("Y-m-d H:i:s", strtotime('now')),
            'kecamatan' => $this->request->getVar('kecamatan'),
            'status' => $this->request->getVar('status'),
            'slug' => url_title($title, '-', true),
            'post_type' => 'map',
            'others' => $others,
            'title' => $title,
        ];

        // ? If user input image
        if ($cover->getError() != 4) {
            $file = [
                'file' => $cover,
                'file_old' => $oldImage,
                'file_path' => 'img/',
                'file_context' => 'map'
            ];
            $data = array_merge($data, $file);
        }

        $this->setData($data);
        return parent::ajaxUpdate($id);
    }

    public function ajaxDestroy($id = null)
    {
        $mapId = base64_decode($id);
        $map = $this->model->find($mapId);
        $data = [
            'image_name' => $map['image'],
            'image_path' => 'img/',
            'image_context' => 'map'
        ];

        $this->setData($data);
        return $this->response->setJSON(parent::ajaxDestroy($id));
    }
}
