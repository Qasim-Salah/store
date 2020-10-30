<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

//use App\Http\Requests\MainCategoryRequest;
use App\Http\Requests\MainCategoryRequest;
use App\Models\MainCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

//use DB;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MainCategoriesController extends Controller
{
    public function index()
    {
//        Helper
        $default_lang = get_default_lang();
        $categories = MainCategory::where('translation_lang', $default_lang)->selection()->get();

        return view('admin.mainCategories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.mainCategories.create');
    }


    public function store(MainCategoryRequest $request)
    {

        try {

// اجيب اللغات من الانبت
            $main_categories = collect($request->category);
            //   افلتر اللغة الاصلية
            $filter = $main_categories->filter(function ($value, $key) {
                return $value['abbr'] == get_default_lang();
            });
            //    احول البينات الىarray
            //    [0] ارجع اول بيانات باريي
            $default_category = array_values($filter->all()) [0];


            $filePath = "";
            //اذا الصورة جاية بالركوست
            if ($request->has('photo')) {
                //حفظ الصورة
                $filePath = uploadImage('mainCategories', $request->photo);
            }
            if (!$request->has('category.0.active'))
                $request->request->add(['active' => 0]);
            else
                $request->request->add(['active' => 1]);
            DB::beginTransaction();
            //اخزن البينات بالايدي
            $default_category_id = MainCategory::insertGetId([
                'translation_lang' => $default_category['abbr'],
                'translation_of' => 0,
                'name' => $default_category['name'],
                'slug' => $default_category['name'],
                'photo' => $filePath,
                'active' => $request->active,
            ]);
            //احضار اللغات البقية
            $categories = $main_categories->filter(function ($value, $key) {
                return $value['abbr'] != get_default_lang();
            });


            if (isset($categories) && $categories->count()) {

                $categories_arr = [];
                foreach ($categories as $category) {
                    $categories_arr[] = [
                        'translation_lang' => $category['abbr'],
                        'translation_of' => $default_category_id,
                        'name' => $category['name'],
                        'slug' => $category['name'],
                        'photo' => $filePath
                    ];
                }

                MainCategory::insert($categories_arr);
            }

            DB::commit();

            return redirect()->route('admin.mainCategories')->with(['success' => 'تم الحفظ بنجاح']);

        } catch (\Exception $ex) {
            DB::rollback();
            return redirect()->route('admin.mainCategories')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }

    }


    public function edit($mainCat_id)
    {
        try {
            //get specific categories and its translations
            $mainCategory = MainCategory::with('categories')
                ->selection()
                ->find($mainCat_id);

            if (!$mainCategory)
                return redirect()->route('admin.mainCategories')->with(['error' => 'هذا القسم غير موجود ']);

            return view('admin.mainCategories.edit', compact('mainCategory'));
        } catch (\Exception $ex) {
            return redirect()->route('admin.mainCategories')->with(['error' => 'حدث خطأ ما ']);
        }
    }

    public function update($mainCat_id, MainCategoryRequest $request)
    {


        try {
            $main_category = MainCategory::find($mainCat_id);

            if (!$main_category)
                return redirect()->route('admin.mainCategories')->with(['error' => 'هذا القسم غير موجود ']);

            // update date

            $category = array_values($request->category) [0];

            if (!$request->has('category.0.active'))
                $request->request->add(['active' => 0]);
            else
                $request->request->add(['active' => 1]);

            DB::beginTransaction();
            MainCategory::where('id', $mainCat_id)
                ->update([
                    'name' => $category['name'],
                    'active' => $request->active,
                ]);

            // save image

            if ($request->has('photo')) {
                $filePath = uploadImage('mainCategories', $request->photo);
                MainCategory::where('id', $mainCat_id)
                    ->update([
                        'photo' => $filePath,
                    ]);
            }

            DB::commit();
            return redirect()->route('admin.mainCategories')->with(['success' => 'تم ألتحديث بنجاح']);
        } catch (\Exception $ex) {
            DB::rollback();
            return redirect()->route('admin.mainCategories')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }

    }


    public function destroy($id)
    {
        try {
            $mainCategory = MainCategory::find($id);
            if (!$mainCategory)
                return redirect()->route('admin.mainCategories')->with(['error' => 'هذا القسم غير موجود ']);

            $vendors = $mainCategory->vendors();
            if (isset($vendors) && $vendors->count() > 0) {
                return redirect()->route('admin.mainCategories')->with(['error' => 'لأ يمكن حذف هذا القسم  ']);
            }

            $image = Str::after($mainCategory->photo, 'assets/');
            $image = base_path('assets/' . $image);
            unlink($image); //delete from folder
             $mainCategory->categories()->delete();
            $mainCategory->delete();
            return redirect()->route('admin.mainCategories')->with(['success' => 'تم حذف القسم بنجاح']);

        } catch (\Exception $ex) {

            return redirect()->route('admin.mainCategories')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }

    public function changeStatus($id)
    {
        try {
            $mainCategory = MainCategory::find($id);
            if (!$mainCategory)
                return redirect()->route('admin.mainCategories')->with(['error' => 'هذا القسم غير موجود ']);

           $status =  $mainCategory -> active  == 0 ? 1 : 0;

          $mainCategory -> update(['active' =>$status ]);

           return redirect()->route('admin.mainCategories')->with(['success' => ' تم تغيير الحالة بنجاح ']);

        } catch (\Exception $ex) {
            return redirect()->route('admin.mainCategories')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }

}
