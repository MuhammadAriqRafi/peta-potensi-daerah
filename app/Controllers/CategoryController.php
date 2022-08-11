<?php

namespace App\Controllers;

use App\Controllers\CRUDController;
use App\Models\Category;
use Config\Services;

class CategoryController extends CRUDController
{
    public function __construct()
    {
        parent::__construct(new Category());
    }

    public function index()
    {
        $data = [
            'title' => 'Category',
            'categories' => $this->model->findAll(),
            'validation' => Services::validation(),
            'indexUrl' => '/backend/maps/categories/ajaxIndex',
            'storeUrl' => '/backend/maps/categories/store',
            'editUrl' => '/backend/maps/categories/edit/',
            'updateUrl' => '/backend/maps/categories/update/',
            'destroyUrl' => '/backend/maps/categories/destroy/',
        ];

        return view('category/index', $data);
    }

    public function ajaxIndex()
    {
        return parent::index();
    }

    public function store()
    {
        $data = [
            'image' => '',
            'post_type' => 'map',
            'title' => $this->request->getVar('title'),
            'slug' => url_title($this->request->getVar('title'), '-', true),
            'description' => $this->request->getVar('description'),
            'validation_options' => [
                'title' => 'is_unique[category.title]|',
                'image' => 'uploaded[image]|'
            ],
        ];

        $image = $this->request->getFile('image');

        if ($image) {
            $file = [
                'image_file' => $image,
                'image_path' => 'img/',
                'image_context' => 'category',
            ];
            $data = array_merge($data, $file);
        } else {
            $data['image'] = null;
        }

        $this->setData($data);
        return parent::store();
    }

    public function edit($id = null)
    {
        $this->setData(['select' => 'category_id, title, description, image']);
        return parent::edit($id);
    }

    public function update($id = null)
    {
        $id = base64_decode($id);
        $image = $this->request->getFile('image');
        $oldImage = $this->model->select('image')->find($id)['image'];

        $data = [
            'image' => $oldImage,
            'category_id' => $id,
            'post_type' => 'map',
            'title' => $this->request->getVar('title'),
            'slug' => url_title($this->request->getVar('title'), '-', true),
            'description' => $this->request->getVar('description'),
            'validation_options' => [
                'title' => "is_unique[category.title,category_id,{$id}]|"
            ],
        ];

        if ($image->getError() != 4) {
            $file = [
                'file' => $image,
                'file_old' => $oldImage,
                'file_path' => 'img/',
                'file_context' => 'category',
            ];
            $data = array_merge($data, $file);
        }

        $this->setData($data);
        return parent::update($id);
    }

    public function destroy($id = null)
    {
        $image = $this->model->select('image')->find(base64_decode($id))['image'];

        if ($image) {
            $data = [
                'image_name' => $image,
                'image_path' => 'img/',
                'image_context' => 'category'
            ];
            $this->setData($data);
        }

        return $this->response->setJSON(parent::destroy($id));
    }
}
