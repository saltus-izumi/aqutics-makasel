<?php
namespace App\Http\Controllers\Admin\Master;

use App\Models\ImageCategoryMaster;
use Illuminate\Http\Request;

class ImageCategoryMasterController
{
    public function index(Request $request, $categoryKind = ImageCategoryMaster::CATEGORY_KIND_EXTERIOR)
    {
        return view('admin.master.image-category-master.index')
            ->with(compact(
                'categoryKind'
            ));
    }
}
