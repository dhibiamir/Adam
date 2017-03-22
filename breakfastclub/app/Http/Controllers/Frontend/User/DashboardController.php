<?php

namespace App\Http\Controllers\Frontend\User;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\event;
use Carbon\Carbon;
use App\Models\Access\User\User;

/**
 * Class DashboardController
 * @package App\Http\Controllers\Frontend
 */
class DashboardController extends Controller
{

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {

        $events          = event::orderBy('starting_date', 'asc')->get();
        $ending_events   = event::orderBy('ending_date', 'asc')->get();
        $format          = 'Y-m-d';
        $upcoming_events = array();
        $current_events  = array();
        foreach($events as $event){
            if (Carbon::createFromFormat($format, $event->starting_date)->isFuture()){
                $upcoming_events[$event->id]=$event;
            }
        }
        foreach($ending_events as $event){
            if (Carbon::now()
                ->between(Carbon::createFromFormat($format, $event->starting_date),
                          Carbon::createFromFormat($format, $event->ending_date))){
                $current_events[$event->id]=$event;
            }
        }
        foreach ($upcoming_events as $upcoming_event){
            $name               = explode(',', User::find($upcoming_event->assigned_to)->name)[0];//fuse second foreachs with first ones
            $lastname           = explode(',', User::find($upcoming_event->assigned_to)->name)[1];
            $upcoming_event->assigned_to_name = $name .  " " . $lastname ;
        }
        foreach ($current_events as $current_event){
            $name               = explode(',', User::find($current_event->assigned_to)->name)[0];
            $lastname           = explode(',', User::find($current_event->assigned_to)->name)[1];
            $current_event->assigned_to_name = $name .  " " . $lastname ;
        }

        if(count($current_events)===0 || count($upcoming_events)===0){
            if (count($current_events)===0)
                return view('frontend.user.dashboard',[
                    'upcoming_events' => $upcoming_events
                ])->withUser(access()->user());
            elseif (count($upcoming_events)===0){
                return view('frontend.user.dashboard',[
                    'current_events' => $current_events
                ])->withUser(access()->user());
            }


            elseif (($current_events)===0 && count($upcoming_events)===0){
                return view('frontend.user.dashboard')
                    ->withUser(access()->user());
            }

        }
         return view('frontend.user.dashboard',[
            'upcoming_events' => $upcoming_events,
            'current_events'  => $current_events
        ])->withUser(access()->user());


    }
}

