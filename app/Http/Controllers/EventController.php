<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EventController extends Controller
{
    /**
     * Display a listing of the events (public view).
     */
    public function index()
    {
        $events = Event::orderBy('event_date', 'asc')->get();
        return view('events.index', compact('events'));
    }

    /**
     * Display the specified event.
     */
    public function show(Event $event)
    {
        return view('events.show', compact('event'));
    }

    // Admin-only methods below
    public function __construct()
    {
        // Apply admin middleware to CRUD methods for events
        $this->middleware('auth')->except(['index', 'show']);
        $this->middleware('admin')->except(['index', 'show']);
    }

    /**
     * Show the form for creating a new event.
     */
    public function create()
    {
        return view('events.create');
    }

    /**
     * Store a newly created event in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'link' => ['nullable', 'string'],
            'event_date' => ['nullable', 'date'],
            'event_time' => ['nullable', 'date_format:H:i'],
            'venue' => ['nullable', 'string', 'max:255'],
            'is_movie_screening' => ['boolean'],
            'is_concert' => ['boolean'],
            'is_seminar_workshop' => ['boolean'],
            'is_other_event' => ['boolean'],
            'campaign' => ['boolean'],
            'ticket_price' => ['required', 'numeric', 'min:0'],
        ]);

        try {
            Event::create([
                'name' => $request->name,
                'description' => $request->description,
                'link' => $request->link,
                'event_date' => $request->event_date,
                'event_time' => $request->event_time,
                'venue' => $request->venue,
                'is_movie_screening' => $request->boolean('is_movie_screening'),
                'is_concert' => $request->boolean('is_concert'),
                'is_seminar_workshop' => $request->boolean('is_seminar_workshop'),
                'is_other_event' => $request->boolean('is_other_event'),
                'campaign' => $request->boolean('campaign'),
                'ticket_price' => $request->ticket_price,
            ]);

            return redirect()->route('events.index')->with('success', 'Event created successfully.');
        } catch (\Exception $e) {
            Log::error('Event creation failed: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Failed to create event.']);
        }
    }

    /**
     * Show the form for editing the specified event.
     */
    public function edit(Event $event)
    {
        return view('events.edit', compact('event'));
    }

    /**
     * Update the specified event in storage.
     */
    public function update(Request $request, Event $event)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'event_date' => ['nullable', 'date'],
            'event_time' => ['nullable', 'date_format:H:i'],
            'venue' => ['nullable', 'string', 'max:255'],
            'is_movie_screening' => ['boolean'],
            'is_concert' => ['boolean'],
            'is_seminar_workshop' => ['boolean'],
            'is_other_event' => ['boolean'],
            'ticket_price' => ['required', 'numeric', 'min:0'],
        ]);

        try {
            $event->update($request->all());
            return redirect()->route('events.index')->with('success', 'Event updated successfully.');
        } catch (\Exception $e) {
            Log::error('Event update failed: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Failed to update event.']);
        }
    }

    /**
     * Remove the specified event from storage.
     */
    public function destroy(Event $event)
    {
        try {
            $event->delete();
            return redirect()->route('events.index')->with('success', 'Event deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Event deletion failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to delete event.']);
        }
    }
}
