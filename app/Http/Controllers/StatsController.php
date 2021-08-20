<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Event;
use App\Models\Location;
use App\Models\Venue;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StatsController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Stats Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handing the /stats API endpoints
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
     * API Endpoint to GET an Event Stats
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStats(Request $request)
    {
        $locations = Location::all();
        $events = Event::all();
        $categoryVenues = [];

        $stats = [
            'avg_weekly_events_per_month_location' => [],
            'most_popular_venue_per_category'      => []
        ];

        $monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

        foreach ($locations as $location) {
            $months = [];

            // Not sure how to check whether events are weekly or not, so just getting the number
            // of events per month per location instead
            foreach ($events as $event) {
                if ($event->location_id == $location->id) {
                    $month = \Carbon\Carbon::parse($event->started_at)->format('F');

                    if (array_key_exists($month, $months)) {
                        $months[$month]++;
                    } else {
                        $months[$month] = 1;
                    }
                }

                // Count the number of venues per category for later use, but only if the category_uuid is set
                if ($event->category_uuid) {
                    if (array_key_exists($event->category_uuid, $categoryVenues)) {
                        if (array_key_exists($event->venue_uuid, $categoryVenues[$event->category_uuid])) {
                            $categoryVenues[$event->category_uuid][$event->venue_uuid]++;
                        } else {
                            $categoryVenues[$event->category_uuid][$event->venue_uuid] = 1;
                        }
                    } else {
                        $categoryVenues[$event->category_uuid][$event->venue_uuid] = 1;
                    }
                }
            }

            $data = [
                'location' => [
                    'name' => $location->name
                ]
            ];

            foreach ($monthNames as $monthName) {
                if (array_key_exists($monthName, $months)) {
                    $data['months'][$monthName] = $months[$monthName];
                }
            }

            $stats['avg_weekly_events_per_month_location'][] = $data;
        }

        // Months have been processed, now process the categories and venues
        $categoryCount = [];
        $categoryVenueIds = [];

        foreach ($categoryVenues as $category_uuid => $categoryVenue) {
            // Reverse sort the venue list by the array values
            arsort($categoryVenue);

            foreach ($categoryVenue as $venue_uuid => $eventCount) {
                // Ignore venues with less than 2 events
                if ($eventCount < 2) {
                    continue;
                }

                // Assume that a tie is disqualified and removed from the results
                if (array_key_exists($category_uuid, $categoryCount)) {
                    if ($eventCount < $categoryCount[$category_uuid]) {
                        break;
                    } else {
                        unset($categoryCount[$category_uuid]);
                        unset($categoryVenueIds[$category_uuid]);
                    }
                } else {
                    $categoryCount[$category_uuid] = $eventCount;
                    $categoryVenueIds[$category_uuid] = $venue_uuid;
                }
            }
        }

        foreach ($categoryVenueIds as $category_uuid => $venue_uuid) {
            $category = Category::where('uuid', $category_uuid)->first();
            $venue = Venue::where('uuid', $venue_uuid)->first();

            $stats['most_popular_venue_per_category'][] = [
                'category' => [
                    'category_uuid' => $category->uuid,
                    'name'          => $category->name
                ],

                'venue' => [
                    'venue_uuid' => $venue->uuid,
                    'name'       => $venue->name
                ],
            ];
        }

        return response()->json([
            $stats
        ], 200);
    }
}
