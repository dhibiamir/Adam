<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use App\event;

class MasterComposer
{

    protected $upcoming_events,$current_events;

    public function __construct()
    {
        $upcoming_events = array();
        $current_events  = array();

        if (Schema::hasTable('events')){

            $events          = event::orderBy('starting_date', 'asc')->get();
            $ending_events   = event::orderBy('ending_date', 'asc')->get();
            $format          = 'Y-m-d';
            foreach($events as $event){
                if (Carbon::createFromFormat($format, $event->starting_date)->isFuture()){
                    $upcoming_events[$event->id] = $event;
                }
            }
            foreach($ending_events as $event){
                if (Carbon::now()
                    ->between(Carbon::createFromFormat($format, $event->starting_date),
                        Carbon::createFromFormat($format, $event->ending_date))){
                    $current_events[$event->id] = $event;
                }
            }
            $this->upcoming_events = count($upcoming_events);
            $this->current_events  = count($current_events);
        }

    }

    public function compose(View $view)
    {
        $view->with([
            'upcoming_events_nbr' => $this->upcoming_events,
            'current_events_nbr'  => $this->current_events
        ]);
    }
}