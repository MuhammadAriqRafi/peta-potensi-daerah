<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\FotoTempat;
use Config\Services;

class GalleryController extends BaseController
{
    protected $fotoTempat;

    public function __construct()
    {
        $this->fotoTempat = new FotoTempat();
    }

    public function index($id = null)
    {
        $id = base64_decode($id);
        $galleryPictures = $this->fotoTempat->select('foto_tempat_id, filename')
            ->where('post_id', $id)
            ->findAll();

        return $this->response->setJSON($galleryPictures);

        // dd($galleryPictures);
        // $data = [
        //     'title' => 'Gallery',
        //     'fotos' => $this->fotoTempat->where('post_id', base64_decode($id))->get()->getResultArray(),
        //     'post_id' => $id,
        //     'validation' => Services::validation()
        // ];

        // return view('gallery/index', $data);
    }

    public function store($id = null)
    {
        $image = $this->request->getFile('image');
        $imageName =  storeAs($image, 'img', 'gallery');

        $this->fotoTempat->save([
            'filename' => $imageName,
            'post_id' => base64_decode($id)
        ]);
    }
}
