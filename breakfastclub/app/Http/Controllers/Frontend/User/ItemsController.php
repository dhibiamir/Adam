<?php

namespace App\Http\Controllers\Frontend\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use App\item;
use App\Models\Access\User\user;
use Illuminate\Support\Facades\DB;
use App\history;
use Storage;
use File;
use App\category;
use App\Http\Requests;
use \Carbon\Carbon;
use Intervention\Image\Facades\Image;
use App\event;
use \Milon\Barcode\DNS1D;
use \Milon\Barcode\DNS2D;
use SimpleSoftwareIO\QrCode\QrCode;


class ItemsController extends Controller
{


    public function getAll()
    {
        $items = item::all();
        $categories=category::all();


        $itembynumber = DB::table('items')
            ->orderBy('name')
            ->groupBy('name')
            ->get();

        foreach ($itembynumber as $itm)
        {
            $itm->nbr = count(DB::table('items')->where('name',$itm->name)->get());
        }


        return view('frontend.items',['items' => $items,'categories' => $categories,'itembynumber'=>$itembynumber])
            ->withUser(access()->user());
    }






    public function find_by_name($name)
    {

        $categories=category::all();
        
        //$itemsnames= DB::table('items')->where('name',$name)->get(); err


        //$itemsnames= DB::table('items')->where('name',str_replace('_',' ',$name))->get();

        $itemsnames= item::where('name',str_replace('_',' ',$name))->get();

        //dd($itemsnames);

        return view('frontend.user.item.showname',['itemsnames' => $itemsnames,'categories' => $categories ])
            ->withUser(access()->user());

    }






    public function find_by_id($name,$id)
    {


        $item = item::find($id); //object

        $categories=category::all();
        //ena zedtha hedhi begin
        $jsons = array();
        //end


            $categoryname[$item->idcategory] = category::find($item->idcategory)->name; //finding the category's name using its id
            //dd($categoryname);




        //finding new and not affected items
        $all_items=item::all();
        $reserved_items=history::where('status','not_available')->get();
        $reserved_items_ids=array();

        foreach($reserved_items as $reserved_item){
            $reserved_items_ids[$reserved_item->iditem]=$reserved_item->iditem;
        }

        foreach($reserved_items_ids as $reserved_item_id){


            unset ($all_items[array_search(item::find($reserved_item_id), $all_items->all())]);

        }
        /* end 2*/


        if (history::where('iditem', '=', $id)->exists()) {
            $histories = item::find($id)->history;

            //$json= json_decode($hists[0]->log);
            //foreach($json->histories as $history){echo $history->action;}

            foreach ($histories as $history) {
                $events[$history->idevent] = event::find($history->idevent)->name;

                //finding the start date of the event
                $eventstart[$history->idevent] = event::find($history->idevent)->starting_date;

                //finding the ending date of an event
                $eventend[$history->idevent] = event::find($history->idevent)->ending_date;


                //hedhi ena begin
                $jsons[$history->id] = json_decode($history->log);
                //end
            }


            //hedhi ena begin
            foreach ($jsons as $json) {
                foreach($json->histories as $json_history){
                    $json_history->user_name = str_replace(","," ",User::find($json_history->user_id)->name);
                }

            }
            $logs = json_encode($jsons);
            //end




            return view('frontend.user.item.show',array('logs' => $logs, 'eventstart' =>$eventstart, 'eventend' =>$eventend ,/*end */'item'=>$item , 'histories'=>$histories, 'events' =>$events/*,'itemsnames'=>$itemsnames*/,'categories' => $categories,'categoryname'=>$categoryname));

        }


        else{
            return view('frontend.user.item.show',array('logs' => '','item'=>$item/*,'itemsnames'=>$itemsnames*/,'categories' => $categories,'categoryname'=>$categoryname));
        }


        //return ['item'=>$item, 'events' => $events]);
    }






    public function store(Request $request)
    {

        $number = $request->number;


        for($i = 0; $i < $number; $i++) {

            $item= new item();

            $item->name = $request->name;

            $item->idcategory=$request->idcategory;
            $item->purchase_date = $request->purchase_date;

            if (isset($request->description)){

                $item->description = $request->description;
            }

            $item->state = $request->state;

            $item->save();


            $img = Input::file("image");

            if($request->hasFile('image')) {

                $taswira = '/img/' . time() . "_" . $item->id . "_" . $img->getClientOriginalName();
                $filename = time() . "_" . $item->id . "_" . $img->getClientOriginalName();
                $path = public_path($taswira);
                Image::make($img->getRealPath())->save($path); //interv
                $item->filepath = $filename;
            }

            else { $item->filepath="7admaraa"; }

            $item->save();
           

        }


        return redirect()->back()->withFlashSuccess('Article ajouté avec succés');

    }



    public function edit($id)
    {
        $item = item::find($id);
        //$item = DB::table('items')->where('id', $id)->first();
        //dd($item);

        return (['item' => $item]);
        //echo $item;
    }


    public function update_item_image(Request $request,$id)
    {
        $item = item::find($id);

        $taswira='img/'.$item->filepath;
        $path = public_path($taswira);
        File::delete($path);


        if($request->hasFile('avatar')){
            $img     = $request->file('avatar');
           /* $filename   = time() . "." . $avatar->getClientOriginalExtension();
            Image::make($avatar)->resize(300,300)->save(public_path('/uploads/avatars/' . $filename));
            $item->filepath = $filename;*/

            $taswira = '/img/' . time() . "_" .$item->id."_" .$img->getClientOriginalName();
            $filenameUpdate=time() . "_" .$item->id."_" .$img->getClientOriginalName();
            $path = public_path($taswira);
            Image::make($img->getRealPath())->save($path); //interv
            $item->filepath=$filenameUpdate;


            $item->save();
        }
        $kek=asset('img/'.'/'.$item->filepath);
        echo json_encode($kek);

    }



    public function update(Request $request,$id)
    {

        $item = item::find($id);

        $item->name = $request->nameUpdate;
        $item->idcategory=$request->idcategoryUpdate;
        $item->purchase_date = $request->purchase_dateUpdate;

        if (isset($request->descriptionUpdate)){

            $item->description = $request->descriptionUpdate;
        }

        $item->state = $request->stateUpdate;

        $item->save();

    }


    public function destroy($id)
    {
        $item = item::find($id);

        //$img = Image::make('public/img/'.$item->filepath);
        //$img->destroy();
        $taswira='img/'.$item->filepath;
        $path = public_path($taswira);
        File::delete($path);

        $item->delete();
        
    }


}
