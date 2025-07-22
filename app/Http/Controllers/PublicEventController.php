<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class PublicEventController extends Controller
{
    public function index()
    {
        $events = Event::where('published', 0)->latest()->paginate(10);
        return view('public.events.index', compact('events'));
    }

    public function show(Event $event)
    {
        //abort_unless($event->published, 404);
        return view('public.events.show', compact('event'));
    }
}
