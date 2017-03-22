<?php

namespace App\Http\Controllers\Frontend\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Carbon\Carbon;
use DateTime;
use App\item;
use App\Models\Access\User\User;
use App\event;
use App\history;



/**
 * Class EventsController
 * @package App\Http\Controllers\Frontend\User
 */
class EventsController extends Controller
{

    public function getAll()
    {
        $chefs  = DB::table('users')
            ->join('assigned_roles', 'assigned_roles.user_id', '=', 'users.id')
            ->where('assigned_roles.role_id', '5')
            ->get();//get users with role id 5 named 'Chef de Projet'
        $events = event::all();
        $states = array();
        foreach ($events as $event){

            if (count(DB::table('histories')
                    ->where('idevent',$event->id)
                    ->Where('status','not_available')->get()) >= 1  ){
                $state = "Non cloturé";
            }
            else $state = "Cloturé";
            $event->assigned_to_user_id = $event->assigned_to;
            $name                  = explode(',', User::find($event->assigned_to)->name)[0];
            $lastname              = explode(',', User::find($event->assigned_to)->name)[1];
            //or simply explode(',', User::find($event->assigned_to)->name)[1];for the sake of diversity here
            $event->assigned_to    = $name .  " " . $lastname ;

            $format = 'Y-m-d';

            $ending_date   = Carbon::createFromFormat($format, $event->ending_date);
            $starting_date = Carbon::createFromFormat($format, $event->starting_date);
            $ending_date->subSecond();
            $starting_date->subSecond();

            if ($ending_date->isPast()){
                $states[$event->id]='Terminé,' . " " . $state; //+cloturé ou non according to items table/history (ifs)
            }
            elseif ($starting_date->isPast()) {
                $states[$event->id]='En Cours';
            }
            else $states[$event->id]='Prochainement';

        }
        foreach($chefs as $chef){
            $chef->id   = $chef->user_id;
            $chef->name = str_replace(","," ",$chef->name);
        }
        return view('frontend.events', [
                'events' => $events,
                'states' => $states,
                'chefs'  => $chefs,
            ]);
    }
    
    public function find_by_id($id)
    {
        $event          = event::find($id);
        $items_history  = $event->history;

        $returned_items       = array();
        $in_use_items         = array();
        $returned_items_names = array();
        $in_use_items_names   = array();

        foreach ($items_history as $item_history){
            //delete
            $items[$item_history->iditem]         = item::find($item_history->iditem);
            $items[$item_history->iditem]->status = $item_history->status;
            //end
            if($item_history->status == 'available'){
                $returned_items[$item_history->iditem]         = item::find($item_history->iditem);
                $returned_items[$item_history->iditem]->status = $item_history->status;
                $returned_items_names[$item_history->iditem]   = $returned_items[$item_history->iditem]->name;
            } else{
                $in_use_items[$item_history->iditem]         = item::find($item_history->iditem);
                $in_use_items[$item_history->iditem]->status = $item_history->status;
                $in_use_items_names[$item_history->iditem]   = $in_use_items[$item_history->iditem]->name;
            }

        } //display + add/remove item

        $returned_items_numbers = array_count_values($returned_items_names);
        $returned_items_keys    = array_keys($returned_items_numbers);

        $in_use_items_numbers   = array_count_values($in_use_items_names);
        $in_use_items_keys      = array_keys($in_use_items_numbers);

        $items_to_return = array();

        foreach ($in_use_items_keys as $in_use_items_key){
            $named_items_ids = array();
            for($cpt =0; $cpt < $in_use_items_numbers[$in_use_items_key]; $cpt++){
                $id_position       = array_search($in_use_items_key,$in_use_items_names);
                $named_items_ids[] = $id_position;
                unset($in_use_items_names[$id_position]);
            }
            $in_use_item                = item::find($named_items_ids[0]);
            $in_use_item->number_in_use = $in_use_items_numbers[$in_use_items_key];
            $in_use_item->ids_in_use    = $named_items_ids;
            $items_to_return[]          = $in_use_item;
            unset($in_use_item);
        }


        $chefs  = DB::table('users')
            ->join('assigned_roles', 'assigned_roles.user_id', '=', 'users.id')
            ->where('assigned_roles.role_id', '5')
            ->get();//get users with role id 5 which means,they have 'Chef de Projet' role

        if (count(DB::table('histories')->where('idevent',$id)->Where('status','not_available')->get()) >= 1  ){
             $state = "Non cloturé";
        }
        else $state = "Cloturé";


        foreach($chefs as $chef){
            $chef->id=$chef->user_id;
            $chef->name = explode(',', $chef->name)[0]. " " . explode(',', $chef->name)[1];
        }

        $event->assigned_to_user_id = $event->assigned_to;
        
        $name        = explode(',', User::find($event->assigned_to)->name)[0];
        $lastname    = explode(',', User::find($event->assigned_to)->name)[1];
        $assigned_to = $name .  " " . $lastname ;


        $format = 'Y-m-d';
        $ending_date   = Carbon::createFromFormat($format, $event->ending_date);
        $starting_date = Carbon::createFromFormat($format, $event->starting_date);

        $starting_date_fill = str_replace("-","/",$event->starting_date);
        $ending_date_fill   = str_replace("-","/",$event->ending_date);

        if ($ending_date->isPast()){
            $state='Terminé,' . " " . $state;
        }
        elseif ($starting_date->isPast()) {
            $state='En Cours';
        }
        else $state='Prochainement';


        $event->starting_date = strftime('%A, %d %B, %Y',strtotime($event->starting_date));
        $event->ending_date   = strftime('%A, %d %B, %Y',strtotime($event->ending_date));
        $reserved_items_ids   = array();
        $all_items            = item::all();
        $reserved_items       = history::where('status','not_available')->get();

        foreach($reserved_items as $reserved_item){
            $events_reserved [$reserved_item->iditem]    = event::find($reserved_item->idevent);
            $reserved_items_ids [$reserved_item->iditem] = $reserved_item->iditem;

        }

        foreach($reserved_items_ids as $reserved_item_id){

            $item_to_unset          = array_search(item::find($reserved_item_id), $all_items->all());

            $boundary_starting_date = Carbon::createFromFormat($format, $events_reserved [$reserved_item_id]->starting_date);
            $boundary_ending_date   = Carbon::createFromFormat($format, $events_reserved [$reserved_item_id]->ending_date);

            // Checks if this is the same event as the main one
            $same_events = $events_reserved [$reserved_item_id]->id == $id;

            // Checks if event length is one day,and it is parallel to our event(that is also one day length)
            $is_one_day_main_event = $ending_date->eq($starting_date);
            $is_one_day_loop_event = $boundary_starting_date->eq($boundary_ending_date);
            $same_day_events       = $boundary_starting_date->eq($ending_date);
            $identical_events      = $same_day_events && $is_one_day_loop_event && $is_one_day_main_event;

            // To make sure dates are well between the boundaries
            $boundary_starting_date->addSecond();
            $boundary_ending_date->subSecond();

            // Checks if the event,this item is in,happens to be parallel to our event
            $boundary_date[0] = $boundary_starting_date->between($starting_date, $ending_date);
            $boundary_date[1] = $boundary_ending_date->between($starting_date, $ending_date);

            // Checks if the event,this item is in,happens to be already finished,
            //but the item is still in use(not returned)
            $boundary_date[2] = $boundary_ending_date->isPast();

            $boundary_dates   = $boundary_date[0] || $boundary_date[1] || $boundary_date[2];

            // To make sure dates are well between the boundaries
            $boundary_starting_date->subSecond();
            $boundary_ending_date->addSecond();

            // Checks if the main event,is within the dates of this one
            $within_dates[0]  = $starting_date->between($boundary_starting_date, $boundary_ending_date);
            $within_dates[1]  = $ending_date->between($boundary_starting_date, $boundary_ending_date);
            $within_dates[2]  = $starting_date->between($boundary_ending_date, $boundary_starting_date);
            $within_dates[3]  = $ending_date->between($boundary_ending_date, $boundary_starting_date);
            $within_the_dates = $within_dates[0] || $within_dates[1] || $within_dates[2] || $within_dates[3];

            // Sums up all Booleans in one variable
            $Dismiss_item = $boundary_dates || $identical_events || $same_events || $within_the_dates;

            if( $Dismiss_item ){
                unset ($all_items[$item_to_unset]);
            }
        }

        $available_items = array();
        //$items_numbers   = array();
        foreach ($all_items as $item){
            $available_items[$item->id] = $item->name;
        }

        $items_to_reserve = array();

        $items_numbers = array_count_values($available_items);
        $keys          = array_keys($items_numbers);

        foreach ($keys as $key){
            $named_items_ids = array();
            for($cpt =0; $cpt < $items_numbers[$key]; $cpt++){
                $id_position       = array_search($key,$available_items);
                $named_items_ids[] = $id_position;
                unset($available_items[$id_position]);
            }
            $available_item                   = item::find($named_items_ids[0]);
            $available_item->number_available = $items_numbers[$key];
            $available_item->ids_available    = $named_items_ids;
            $items_to_reserve[]               = $available_item;
            unset($available_item);
        }

        return view('frontend.user.event.show', [
            'items_numbers'          => $items_numbers,
            'available_items'        => $all_items,
            'event'                  => $event,
            'state'                  => $state,
            'assigned_to'            => $assigned_to,
            'chefs'                  => $chefs,
            'starting_date'          => $starting_date_fill,
            'ending_date'            => $ending_date_fill,
            'items_to_reserve'       => $items_to_reserve,
            'returned_items_numbers' => $returned_items_numbers,
            'items_to_return'        => $items_to_return,
            'in_use_items_numbers'   => $in_use_items_numbers,
            'returned_items_keys'    => $returned_items_keys
        ]);
    }

    public function store(Request $request)
    {

        $event = new event();

        $event->name          = $request->name;
        $event->location      = $request->location;
        $event->starting_date = $request->starting_date;
        $event->ending_date   = $request->ending_date;
        $event->assigned_to   = $request->assigned_to;

        if (isset($request->description)){

            $event->description = $request->description;
        }

        $event->save();
        return redirect()->back()->withFlashSuccess('Evénement ajouté avec succés'); //went fine just implement it in ajax
    }

    public function edit($id)
    {
        $event = event::find($id);

        return (['event' => $event]);

    }

    public function update(Request $request,$id)
    {
        $event           = event::find($id);
        $event->name     = $request->nameUpdate;
        $event->location = $request->locationUpdate;

        if (isset($request->descriptionUpdate)){
            $event->description = $request->descriptionUpdate;
        }

        $event->starting_date = $request->starting_dateUpdate;
        $event->ending_date   = $request->ending_dateUpdate;
        $event->assigned_to   = $request->assigned_toUpdate;
        $event->save();
        return redirect()->back()->withFlashSuccess('Evénement ajouté avec succés');

    }

    public function destroy($id)
    {
        $event = event::find($id);
        $event->delete();
    }

    public function close($id){
        $event         = event::find($id);
        $items_history = $event->history;
        $event->closed = true;
        $event->save();

        foreach ($items_history as $item_history){
            $item_history->status = 'available';

            $json_log          = json_decode($item_history->log);
            $object            = new \stdClass();
            $object->action    = "Liberation";
            $object->user_id   = access()->user()->id;
            $time              = Carbon::now()->format(DateTime::ATOM);
            $object->date      = explode('T',$time)[0];
            $object->time      = explode('+',explode('T',$time)[1])[0];

            $json_log->histories[count($json_log->histories)] = $object;
            $item_history->log = json_encode($json_log);
            $item_history->save();
        }
    }

    public function itemRelease($id){

        $eventid = explode('-', $id)[0];
        $itemid  = explode('-', $id)[1];
        $event_histories = event::find($eventid)->history;

        foreach ($event_histories->all() as $event_history){
            if($event_history->iditem == $itemid){
                $item_to_reset = $event_history;
            }

        }
        if($item_to_reset->status  === "not_available"){
            $item_to_reset->status = "available";

            $json_log          = json_decode($item_to_reset->log);
            $object            = new \stdClass();
            $object->action    = "Liberation";
            $object->user_id   = access()->user()->id;
            $time              = Carbon::now()->format(DateTime::ATOM);
            $object->date      = explode('T',$time)[0];
            $object->time      = explode('+',explode('T',$time)[1])[0];

            $json_log->histories[count($json_log->histories)] = $object;
            $item_to_reset->log = json_encode($json_log);
            $item_to_reset->save();
        }


    }

    public function itemReleaseMulti(Request $request,$id){
        
        foreach($request->releaseItem as $toRelaseItem){
            $history = history::where('idevent', $id)
                ->Where('iditem', $toRelaseItem)
                ->get();
            if($history->all()[0]->status  === "not_available"){

                $history->all()[0]->status  = "available";

                $json_log          = json_decode($history->all()[0]->log);
                $object            = new \stdClass();
                $object->action    = "Liberation";
                $object->user_id   = access()->user()->id;
                $time              = Carbon::now()->format(DateTime::ATOM);
                $object->date      = explode('T',$time)[0];
                $object->time      = explode('+',explode('T',$time)[1])[0];

                $json_log->histories[count($json_log->histories)] = $object;
                $history->all()[0]->log = json_encode($json_log);
                $history->all()[0]->save();

            }
        }
    }

    public function itemReserve($eventid_itemid){

        $eventid  = explode('-', $eventid_itemid)[0];
        $itemid   = explode('-', $eventid_itemid)[1];
        $user_id  = explode('-', $eventid_itemid)[2];
        
        $item_to_reserve = DB::table('histories')->where('iditem', $itemid)->Where('idevent', $eventid)->get();
        
        if (count($item_to_reserve) == 0){
            $history = new history();
            $history->iditem   = $itemid;
            $history->idevent  = $eventid;
            $history->status   = 'not_available';
            $history->log      = '{"histories":[]}';
            $history->save();
            $json_log          = json_decode($history->log);
            $object            = new \stdClass();
            $object->action    = "Affectation";
            $object->user_id   = $user_id;
            $time              = Carbon::now()->format(DateTime::ATOM);
            $object->date      = explode('T',$time)[0];
            $object->time      = explode('+',explode('T',$time)[1])[0];

            $json_log->histories[count($json_log->histories)] = $object;
            $history->log = json_encode($json_log);
            $history->save();
        }
        else {
            $history = history::find($item_to_reserve[0]->id);
            $history->status   = "not_available";

            $json_log          = json_decode($history->log);
            $object            = new \stdClass();
            $object->action    = "Affectation";
            $object->user_id   = $user_id;
            $time              = Carbon::now()->format(DateTime::ATOM);
            $object->date      = explode('T',$time)[0];
            $object->time      = explode('+',explode('T',$time)[1])[0];

            $json_log->histories[count($json_log->histories)] = $object;
            $history->log = json_encode($json_log);
            $history->save();

        }
    }

    public function itemReserveMulti(Request $request,$id){

        foreach($request->reserveItem as $toReserveItem){

            $item_to_reserve = DB::table('histories')
                ->where('iditem', $toReserveItem)
                ->Where('idevent', $id)
                ->get();
            
            if (count($item_to_reserve) == 0){
                $history = new history();
                $history->iditem   = $toReserveItem;
                $history->idevent  = $id;
                $history->status   = 'not_available';

                $history->log      = '{"histories":[]}';
                $history->save();
                $json_log          = json_decode($history->log);
                $object            = new \stdClass();
                $object->action    = "Affectation";
                $object->user_id   = access()->user()->id;
                $time              = Carbon::now()->format(DateTime::ATOM);
                $object->date      = explode('T',$time)[0];
                $object->time      = explode('+',explode('T',$time)[1])[0];

                $json_log->histories[count($json_log->histories)] = $object;
                $history->log = json_encode($json_log);
                $history->save();

            }
            else {
                
                $history = history::find($item_to_reserve[0]->id);
                $history->status   = "not_available";

                $json_log              = json_decode($history->log);
                $object            = new \stdClass();
                $object->action    = "Affectation";
                $object->user_id   = access()->user()->id;
                $time              = Carbon::now()->format(DateTime::ATOM);
                $object->date      = explode('T',$time)[0];
                $object->time      = explode('+',explode('T',$time)[1])[0];

                $json_log->histories[count($json_log->histories)] = $object;
                $history->log = json_encode($json_log);
                $history->save();
            }

        }

    }

    public function PDFGen (Request $request){


        $details    = app_details()['details'];
        $name       = explode(',', User::find($request->assigned_to)->name)[0];
        $lastname   = explode(',', User::find($request->assigned_to)->name)[1];
        $issued_for = $name .  " " . $lastname ;

        $name_issuer       = explode(',', $request->issuer_name)[0];
        $lastname_issuer   = explode(',', $request->issuer_name)[1];
        $issuer_name = $name_issuer .  " " . $lastname_issuer ;

        $data = array(
            'date'        => date("m-d-Y",strtotime(Carbon::now())),
            'issued_for'  => $issued_for,
            'issuer_name' => $issuer_name,
            'request'     => $request,
            'name'        => $details['company_name'],
            'address'     => $details['company_address'],
            'phone'       => $details['company_phone'],
            'website'     => $details['company_website'],
            'contact'     => $details['company_contact'],
            'logo'        => $details['company_logo_link']
        );

        $dompdf = App::make('dompdf.wrapper');
        if (!$request->paper_order){
            $dompdf->loadView('frontend.user.event.pdf.order_pdf_gen',compact('data'));
        }
        else $dompdf->loadView('frontend.user.event.pdf.pre_templated_order_pdf_gen',compact('data'));



        $dompdf->stream("Ordore_de_mission.pdf");

        return $dompdf->stream();
    }

    public function PDFEquipementsGen (Request $request){

        $details = app_details()['details'];

        //$name       = explode(',', User::find($request->issued_for_name)->name)[0];
        //$lastname   = explode(',', User::find($request->issued_for_name)->name)[1];
        $issued_for   = str_replace(","," ",User::find($request->issued_for_name)->name) ;

        $name_issuer       = explode(',', $request->issuer_name)[0];
        $lastname_issuer   = explode(',', $request->issuer_name)[1];
        $issuer_name       = $name_issuer .  " " . $lastname_issuer ;//replace all of this by simply replacing the "," with " "
        $items             = history::where('status','not_available')->Where('idevent',$request->eventid)->get();
        $equips            = array();
        $categs            = array();
        $reserved_items    = array();
        foreach ($items as $item){
            $equips[]          = item::find($item->iditem);
        }
        foreach ($equips as $equip){
            $categs[$equip->id] = $equip->idcategory;
        }
        $categ_nbr = array_count_values($categs);
        $categ_ids = array_keys($categ_nbr);
        foreach ($categ_ids as $categ_id){
            $reserved_items[$categ_id] = item::where('idcategory', $categ_id)->get()->first();
        }

        $data    = array(
            'date'       => date("m-d-Y",strtotime(Carbon::now())),
            'request'    => $request,
            'name'       => $details['company_name'],
            'address'    => $details['company_address'],
            'phone'      => $details['company_phone'],
            'website'    => $details['company_website'],
            'contact'    => $details['company_contact'],
            'logo'       => $details['company_logo_link'],
            'issued_for' => $issued_for,
            'issued_by'  => $issuer_name,
            'categ_nbr'  => $categ_nbr,
            'items'      => $reserved_items,
        );

        $dompdf  = App::make('dompdf.wrapper');

        if (!$request->paper){
            $dompdf->loadView('frontend.user.event.pdf.items_pdf_gen',compact('data'));
        }
        else $dompdf->loadView('frontend.user.event.pdf.pre_templated_items_pdf_gen',compact('data'));

        $dompdf->stream("Bon_de_sortie.pdf");

        return $dompdf->stream();
    }

}
