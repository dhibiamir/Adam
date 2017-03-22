<?php

namespace App\Http\Controllers\Frontend\User;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use App\item;
use App\history;
use \Carbon\Carbon;
use Intervention\Image\Facades\Image;
use App\category;




class CategoriesController extends Controller
{


    public function getAll()
    {
        $category = category::all();
        return view('frontend.categories',['categories' => $category])
            ->withUser(access()->user());
    }



    public function find_by_id($id)
    {

        $category = category::find($id);


        //$kek = item::find($id)->category;
        $cats = category::find($id)->items; //hehehe
        //dd($cats);
       /* if (item::where('idcategory', '=', $id)->exists()) {
            $names = item::find($id)->category;

            foreach ($names as $name) {
                $category[$name->idcategory] = event::find($history->idevent)->name;
            }*/



        /*$x=category::all();
        foreach ($x as $classed){
           $kek= item::all()->where('categoryid',$classed->id)->get();}
        dd($kek);*/


        return view('frontend.user.category.show',array('category'=>$category,'cats'=>$cats));

    }

    
    public function store(Request $request)
    {
        $category = new category();
        $category->name = $request->name;

        if (isset($request->description)){

            $category->description = $request->description;
        }

        $category->save();
        return redirect()->back()->withFlashSuccess('Catégorie ajoutée avec succés');

    }


    
    public function edit($id)
    {
        $category = category::find($id);

        return (['category' => $category]);
    }


    public function update(Request $request,$id)
    {

        $category = category::find($id);

        $category->name = $request->nameUpdate;

        if (isset($request->descriptionUpdate)){

            $category->description = $request->descriptionUpdate;
        }

        $category->save();

    }



    
    public function destroy($id)
    {
        $category = category::find($id);;
        $category->delete();

    }


}
