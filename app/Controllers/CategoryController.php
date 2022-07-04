<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Category;
use Config\Services;

class CategoryController extends BaseController
{

    protected $categories;

    public function __construct()
    {
        $this->categories = new Category();
    }

    public function index()
    {
        $data = [
            'title' => 'Category',
            'categories' => $this->categories->findAll(),
            'validation' => Services::validation()
        ];

        return view('category/index', $data);
    }

    public function store()
    {
        if (!$this->validate([
            'title' => 'required|is_unique[category.title]',
            'image' => [
                'rules' => 'max_size[image,1024]|is_image[image]|mime_in[image,image/jpg,image/jpeg,image/png]',
                'errors' => [
                    'uploaded' => 'Image is required'
                ]
            ]
        ])) {
            return redirect()->back()->withInput();
        }

        $image = $this->request->getFile('image');

        if ($image->getError() != 4) $imageName = storeAs($image, 'img', 'category');
        else $imageName = null;

        $this->categories->save([
            'post_type' => 'map',
            'image' => $imageName,
            'title' => $this->request->getVar('title'),
            'slug' => url_title($this->request->getVar('title'), '-', true),
            'description' => $this->request->getVar('description'),
        ]);

        return redirect()->back()->with('success', 'Category berhasil ditambahkan!');
    }

    public function edit($id = null)
    {
        $data = [
            'title' => 'Edit Category',
            'category' => $this->categories->find(base64_decode($id)),
            'validation' => Services::validation()
        ];

        return view('category/edit', $data);
    }

    public function update($id = null)
    {
        if (!$this->validate([
            'title' => 'required|is_unique[category.title,title,{title}]',
            'image' => [
                'rules' => 'uploaded[image]|max_size[image,1024]|is_image[image]|mime_in[image,image/jpg,image/jpeg,image/png]',
                'errors' => [
                    'uploaded' => 'Image is required'
                ]
            ]
        ])) {
            return redirect()->back()->withInput();
        }

        $image = $this->request->getFile('image');
        $oldImage = $this->request->getVar('oldImage');

        if ($image->getError() != 4) {
            $imageName = storeAs($image, 'img', 'category');
            unlink('img/' . $oldImage);
        } else $imageName = $oldImage;

        $this->categories->save([
            'category_id' => $id,
            'post_type' => 'map',
            'image' => $imageName,
            'title' => $this->request->getVar('title'),
            'slug' => url_title($this->request->getVar('title'), '-', true),
            'description' => $this->request->getVar('description'),
        ]);

        return redirect()->back()->with('success', 'Category berhasil diubah!');
    }

    public function destroy($id = null)
    {
        $category = $this->categories->find($id);

        try {
            unlink('img/' . $category['image']);
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }

        return redirect()->back()->with('success', 'Category berhasil dihapus!');
    }
}
