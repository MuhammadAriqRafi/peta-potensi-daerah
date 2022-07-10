<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Post;
use Throwable;

class PostController extends BaseController
{
    protected $posts;

    public function __construct()
    {
        $this->posts = new Post();
    }

    public function destroy($id = null, $context = null)
    {
        // Check if post has image
        $id = base64_decode($id);
        $image = $this->posts->find($id)['image'];

        if ($image) {
            try {
                unlink('img/' . $image);
            } catch (Throwable $th) {
            }
        }

        $this->posts->delete($id);
        return $this->response->setJSON([
            'status' => 1,
            'message' => ucfirst($context) . ' berhasil dihapus!'
        ]);
    }

    public function ajaxGetDataDataTables($context, $model)
    {
        helper('utilities');
        $draw = $_REQUEST['draw'];
        $start = $_REQUEST['start'];
        $length = $_REQUEST['length'];
        $search = $_REQUEST['search']['value'];
        $total = $context == 'maps' ? $model->ajaxGetTotalMaps() : $model->ajaxGetTotalProfiles();
        $output = [
            'length' => $length,
            'draw' => $draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
        ];

        if ($search != '') {
            $list = $context == 'maps' ? $model->ajaxGetMapsSearch($search, $start, $length) : $model->ajaxGetProfilesSearch($search, $start, $length);
            $total_search = $context == 'maps' ? $model->ajaxGetTotalMapsSearch($search) : $model->ajaxGetTotalProfilesSearch($search);
            $output = [
                'recordsTotal' => $total_search,
                'recordsFiltered' => $total_search
            ];
        } else $list = $context == 'maps' ? $model->ajaxGetMaps($start, $length) : $model->ajaxGetProfiles($start, $length);

        $data = [];
        foreach ($list as $listData) {
            $record = [];

            // ? Convert $listData array associative to simple array
            $listData = array_values($listData);

            foreach ($listData as $key => $value) {
                // ? If $key is the last element of the array, which is the post_id, encode it
                $record[] = $key == count($listData) - 1 ? strtr(editDeleteBtn(), ['$id' => base64_encode($value), '$context' => $context]) : $value;
            }

            $data[] = $record;
        }

        $output['data'] = $data;
        return $this->response->setJSON($output);
    }
}
