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
        $image = $this->posts->find($id)['image'];
        if ($image) {
            try {
                unlink('img/' . $image);
            } catch (Throwable $th) {
                return redirect()->back()->with('error', $th->getMessage());
            }
        }

        $this->posts->delete($id);
        return redirect()->back()->with('success', ucfirst($context) . ' berhasil dihapus!');
    }
}
