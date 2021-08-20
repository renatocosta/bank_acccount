<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Venue;
use Illuminate\Http\Request;

class OrganizerController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Organizer Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handing the /organizers API endpoints
    |
    */

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * API Endpoint to GET an Event Organizer by their UUID
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOrganizer(Request $request)
    {
        $organizer_uuid = trim($request->organizer_uuid);

        if (!$organizer_uuid && !is_numeric($organizer_uuid)) {
            return response()->json([
                'error' => 'Invalid organizer_uuid provided.',
            ], 400);
        }

        $events =  Event::where('organizer_uuid', $organizer_uuid)->orderBy('started_at')->get();
        $totalEvents = $events->count();

        if (!$totalEvents) {
            return response()->json([
                'error' => 'Organizer not found.',
            ], 404);
        }

        $firstEvent = $events[0];
        $venueCounts = [];
        $latestEvent = $events[count($events) - 1];
        $latestDate = $latestEvent->ended_at;

        foreach ($events as $event) {
            if (array_key_exists($event->venue_uuid, $venueCounts)) {
                $venueCounts[$event->venue_uuid]++;
            } else {
                $venueCounts[$event->venue_uuid] = 1;
            }

            if ($event->ended_at > $latestDate) {
                $latestDate = $event->ended_at;
                $latestEvent = $event;
            }
        }

        $venues = Venue::whereIn('uuid', array_keys($venueCounts))->get();
        $venueCount = [];

        foreach ($venues as $venue) {
            $venueCount[] = [
                'venue_uuid' => $venue->uuid,
                'venue_name' => $venue->name,
                'event_count' => $venueCounts[$venue->uuid]
            ];
        }

        $details = [
            'first_event'  => $firstEvent,
            'last_event'   => $latestEvent,
            'total_events' => $totalEvents,
            'venue_count'  => $venueCount,
        ];

        return response()->json([
            $details
        ], 200);
    }
}
