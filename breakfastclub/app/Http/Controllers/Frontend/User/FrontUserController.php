<?php

namespace App\Http\Controllers\Frontend\User;

use App\event;
use App\item;
use App\history;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Access\User\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Exceptions\GeneralException;
use Intervention\Image\Facades\Image;





/**
 * Class EventsController
 * @package App\Http\Controllers\Frontend\User
 */
class FrontUserController extends Controller
{

    public function getAll()
    {
        $users= DB::table('users')->join('assigned_roles', 'assigned_roles.user_id', '=', 'users.id')
            ->where('assigned_roles.role_id', '4')
            ->orWhere('assigned_roles.role_id', '5')
            ->orWhere('assigned_roles.role_id', '6')
            ->get(); // get the users with roles 4,5 and 6 respectively named Administrateur,Chef de Projet, and Magasinier

        foreach($users as $user){
            $roles[$user->user_id]= DB::table('roles')
                ->join('assigned_roles', 'assigned_roles.role_id', '=', 'roles.id')
                ->where('assigned_roles.user_id', $user->user_id)
                ->get(); // get the role associated to each user
            $user->role_name=$roles[$user->user_id]['0']->name;
            $user->Nbr_Events=count(User::find($user->user_id)->events);
        }


        return view('frontend.users', ['users' => $users]);
    }

    public function find_by_id($id)
    {
        $user            = User::find($id);
        if(is_null($user)){
            return redirect()->back()->withFlashWarning('Utilisateur non valide');
        } else {
            $role            = DB::table('roles')->join('assigned_roles', 'assigned_roles.role_id', '=', 'roles.id')->where('assigned_roles.user_id', $id)->get();
            $user->role_name = $role['0']->name;
            $nbr_events      = array();
            $items           = array();
            $states          = array();
            $events          = array();
            if ($role['0']->name == "Administrateur"){
                return redirect()->route('frontend.user.dashboard');
            } elseif ($role['0']->name == "Chef de Projet"){
                $events           = User::find($id)->events;
                $user->Nbr_Events = count($events);
                foreach ($events as $event){
                    $event->assigned_to_name = str_replace(","," ",User::find($event->assigned_to)->name);
                    $format = 'Y-m-d';
                    $ending_date = Carbon::createFromFormat($format, $event->ending_date);
                    $starting_date = Carbon::createFromFormat($format, $event->starting_date);
                    if ($ending_date->isPast()){
                        $states[$event->id]='Terminé';
                    }
                    elseif ($starting_date->isPast()) {
                        $states[$event->id]='En Cours';
                    }
                    else $states[$event->id]='Prochainement';

                }
                return view('frontend.user.user.show', [
                        'events'    => $events,
                        'user'      => $user,
                        'states'    => $states,
                        'nbr'       => count(User::find($id)->events),
                        'role_id'   => $role['0']->role_id,
                        'role_name' => $role['0']->name
                ]);
            } elseif($role['0']->name == "Magasinier"){

                $histories            = history::all()->all();
                $user_items_histories = array();
                $events               = array();
                $items                = array();
                $states               = array();
                $nbr_events_in        = array();
                $nbr                  = 0;


                foreach($histories as $history){
                    $logs = json_decode($history->log)->histories;
                    unset($users_ids);
                    $users_ids = array();

                    foreach($logs as $log){
                        $users_ids[] = $log->user_id;
                    }

                    if(in_array($user->id,$users_ids)){
                        $user_items_histories[] = $history;
                    }
                }

                if(!empty($user_items_histories)){
                    foreach ($user_items_histories as $user_items_history){
                        $events[$user_items_history->iditem] = event::find($user_items_history->idevent);
                        $items[$user_items_history->iditem]  = item::find($user_items_history->iditem);
                    }

                    $nbr = count($user_items_histories);
                    foreach ($events as $event){
                        $nbr_events[$event->id]  = $event->id;
                        $nbr_events_in           = array_keys(array_count_values($nbr_events));
                        $event->assigned_to_name = str_replace(","," ",User::find($event->assigned_to)->name);
                        $format = 'Y-m-d';
                        $ending_date = Carbon::createFromFormat($format, $event->ending_date);
                        $starting_date = Carbon::createFromFormat($format, $event->starting_date);
                        if ($ending_date->isPast()){
                            $states[$event->id]='Terminé';
                        }
                        elseif ($starting_date->isPast()) {
                            $states[$event->id]='En Cours';
                        }
                        else $states[$event->id]='Prochainement';
                    }
                }

                return view('frontend.user.user.show', [
                        'user_items_histories' => $user_items_histories,
                        'events'               => $events,
                        'items'                => $items,
                        'states'               => $states,
                        'user'                 => $user,
                        'nbr'                  => $nbr,
                        'nbr_events_in'        => count($nbr_events_in),
                        'role_id'              => $role['0']->role_id,
                        'role_name'            => $role['0']->name
                ]);

            } else {
                return redirect()->back()->withFlashWarning('Utilisateur non valide');
            }

        }
    }

    public function store(Request $request)
    {
        if (in_array($request->role_id, array("4", "5", "6"))) {

            $mail_check = User::where('email',$request->email)->get();

            if (count($mail_check) == 0){
                $user = new User();

                $user->name              = $request->name . "," . $request->lastname;
                $user->email             = $request->email;
                $user->password          = bcrypt($request->password);
                $user->confirmed         = 1;
                $user->confirmation_code = md5(uniqid(mt_rand(), true));

                $user->save();

                $user_id = DB::table('users')
                    ->where('email',$request->email)->value('id');

                DB::table('assigned_roles')->insert(
                    ['user_id' => $user_id, 'role_id' => $request->role_id]
                );


                /*$user = User::findOrFail($user_id);

                //$user can be user instance or id
                if (! $user instanceof User) {
                    $user = $this->find($user);
                }

                Mail::send('frontend.auth.emails.confirm', ['token' => $user->confirmation_code], function ($message) use ($user) {
                    $message->to($user->email, $user->name)->subject(app_name() . ': ' . trans('exceptions.frontend.auth.confirmation.confirm'));
                });

                if (count(Mail::failures()) > 0) {
                    throw new GeneralException("There was a problem sending the confirmation e-mail");
                }*/


                return redirect()->back()->withFlashSuccess('Utilisateur créé avec succés !');

            } else {

                return redirect()->back()->withFlashDanger('Adresse Mail non valide !');
            }

        } else {
            return redirect()->back()->withFlashDanger('Fonction incorrecte, utilisateur non créé !');
        }


    }

    public function edit($id)
    {
        $user = User::find($id);
        $role = DB::table('roles')
            ->join('assigned_roles', 'assigned_roles.role_id', '=', 'roles.id')
            ->where('assigned_roles.user_id', $user->id)
            ->get(); // get the role associated to this user

        return ([
            'user' => $user,
            'role' => $role
        ]);

    }

    public function update_avatar(Request $request,$id)
    {
        $user = User::find($id);
        if($request->hasFile('avatar')){
            $avatar     = $request->file('avatar');
            $filename   = time() . "." . $avatar->getClientOriginalExtension();
            Image::make($avatar)->resize(300,300)->save(public_path('/uploads/avatars/' . $filename));
            $user->image = $filename;
            $user->save();
        }

        echo json_encode($user->image);

    }

    public function update(Request $request,$id)
    {
        $user = User::find($id);

        $user->name = $request->nameUpdate. "," . $request->lastnameUpdate;
        $user->email = $request->emailUpdate;

        DB::table('assigned_roles')
            ->where('user_id', '=', $user->id)
            ->delete();

        DB::table('assigned_roles')
            ->insert(['user_id' => $user->id, 'role_id' => $request->roleUpdate]);
        //test if its the same role,then delete and create a new one if necessary
        $user->save();
    }

    public function destroy($id)
    {
        $user = User::find($id);

        DB::table('assigned_roles')
            ->where('user_id', '=', $user->id)
            ->delete();

        $events_to_reset = DB::table('events')
            ->where('assigned_to', '=', $user->id)
            ->get();

        foreach($events_to_reset as $event_to_reset){
            $event = event::find($event_to_reset->id);
            $event->assigned_to = 5;
            $event->save();
            unset($event);
        }
        // 2 possible approaches,either delete the events,or keep them(the better choice)but assign them to a different person
        $user->delete();

    }

    public function updatePassword(Request $request,$id)
    {
        $user = User::find($id);
        if (strcmp($request->new_password,$request->new_password_confirmation)==0)

        $user->password = bcrypt($request->new_password);

        $user->save();

    }

}