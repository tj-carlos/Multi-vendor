<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Brand;
use Image;

class BrandController extends Controller
{
    public function AllBrand(){
        $brands = Brand::latest()->get();
        return view('backend.brand.brand_all', compact('brands'));
    }//end method
    public function AddBrand(){
        return view('backend.brand.brand_add');
    }//end

    public function StoreBrand(Request $request){

        $image = $request->file('brand_image');
        $name_gen = hexdec(uniqid()).'.'.$image->getClientOriginalExtension();//gen uid for img
        Image::make($image)->resize(300,300)->save('upload/brand/'.$name_gen);
        $save_url = 'upload/brand/'.$name_gen;

        Brand::insert([
           'brand_name' => $request->brand_name,
           'brand_slug' => strtolower(str_replace(' ', '-',$request->brand_name)),
           'brand_image' => $save_url,
        ]);


        $notification = array(
            'message' => 'Brand Inserted Successfully ',
            'alert-type' => 'success' 
        );
        return redirect()->route('all.brand')->with($notification);


    }//end 
    public function EditBrand($id){

        $brand = Brand::findOrfail($id);
        return view('backend.brand.brand_edit', compact('brand'));
    }
    public function UpdateBrand(Request $request){
        $brand_id = $request->id;
        $old_image =$request->old_image;

        if($request->file('brand_image')){
            $image = $request->file('brand_image');
        $name_gen = hexdec(uniqid()).'.'.$image->getClientOriginalExtension();//gen uid for img
        Image::make($image)->resize(300,300)->save('upload/brand/'.$name_gen);
        $save_url = 'upload/brand/'.$name_gen;

        if(file_exists($old_image)){
            unlink($old_image);

        }

        Brand::findOrFail($brand_id)->update([
           'brand_name' => $request->brand_name,
           'brand_slug' => strtolower(str_replace(' ', '-',$request->brand_name)),
           'brand_image' => $save_url,
        ]);


        $notification = array(
            'message' => 'Brand updated with image successfully ',
            'alert-type' => 'success' 
        );
        return redirect()->route('all.brand')->with($notification);
        }else{
            Brand::findOrFail($brand_id)->update([
                'brand_name' => $request->brand_name,
                'brand_slug' => strtolower(str_replace(' ', '-',$request->brand_name)),
               
             ]);
     
     
             $notification = array(
                 'message' => 'Brand without image successfully ',
                 'alert-type' => 'success' 
             );
             return redirect()->route('all.brand')->with($notification);
        }
    }//end method

    public function DeleteBrand($id){
        $brand = Brand::findOrFail($id);
        $img = $brand->brand_image;
        unlink($img ); 

        Brand::findOrFail($id)->delete();

        $notification = array(
            'message' => 'Brand Deleted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification); 

    }// End Method 
    
}
