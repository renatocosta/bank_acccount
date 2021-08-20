<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Models\Location;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ImportEvents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'events:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private $apiUrl;
    private $apiToken;
    private $apiClient;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->apiUrl = getenv('EVENTBRITE_API_URL');
        $this->apiToken = getenv('EVENTBRITE_API_KEY');

        $this->apiClient = new \GuzzleHttp\Client([
            'headers' => [
                'Authorization' => 'Bearer '. $this->apiToken,
                'Content-Type'  => 'applicaton/json'
            ]
        ]);
    }

    /**
     * @param $eventData
     * @param $event
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function importData($eventData, $event)
    {
        foreach ($eventData as $data) {
            $table = $data['table'];
            $endpoint = $data['endpoint'];
            $value = strtolower($table . '_id');

            if (isset($event->$value)) {
                $uuid = $event->$value;
                $table = '\\App\Models\\' . $table;

                $exists = $table::where('uuid', $uuid)->first();

                // Only import the Data if its not already in the DB
                if (!$exists) {
                    $url = $this->apiUrl . "$endpoint/$uuid/";

                    try {
                        $response = $this->apiClient->request('GET', $url);
                        $statusCode = $response->getStatusCode();

                        if ($statusCode == 200) {
                            $json = $response->getBody()->getContents();

                            $dataToImport = json_decode($json);
                            $name = property_exists($dataToImport, 'name') ? $dataToImport->name : null;

                            $newData = new $table;
                            $newData->uuid = $uuid;
                            $newData->name = $name;
                            $newData->json_data = json_encode($dataToImport);
                            $newData->save();

                            Log::debug("Successfully imported {$data['table']} : [Name: $name] [uuid: $uuid]");

                        } else {
                            Log::debug("Failed to import {$data['table']}, Status Code: $statusCode");
                            throw new \Exception("Unable to retrieve $table data from Eventbrite API for $value: $uuid");
                        }
                    } catch (\GuzzleHttp\Exception\ClientException $e) {
                        Log::debug("Failed to import {$data['table']}, Eventbrite API Error Message: " . $e->getMessage());
                        throw new \Exception("Unable to retrieve $table data from Eventbrite API for $value: $uuid");
                    }
                }
            }
        }
    }

    private function importEvents($pageNumber=1)
    {
        // Get the list of locations from the DB
        $locations = Location::all();

        if ($locations->count()) {
            foreach ($locations as $location) {
                $url = $this->apiUrl . 'events/search?location.longitude=' . $location->longitude;
                $url .= '&location.latitude=' . $location->latitude . '&location.within=100km';
                $url .= "&page=$pageNumber";

                try {
                    $response = $this->apiClient->request('GET', $url);
                    $statusCode = $response->getStatusCode();

                    if ($statusCode == 200) {
                        $json = $response->getBody()->getContents();
                        $data = json_decode($json);
                        $events = $data->events;
                        $pagination = $data->pagination;

                        // Additional Event Data that needs to be imported for each event
                        $eventData = [
                            [
                                'table' => 'Category',
                                'endpoint' => 'categories'
                            ],

                            [
                                'table' => 'Organizer',
                                'endpoint' => 'organizers'
                            ],

                            [
                                'table' => 'Venue',
                                'endpoint' => 'venues'
                            ],
                        ];

                        foreach ($events as $eventToImport) {
                            // Only import the event if it doesn't already exist in the DB
                            $exists = Event::where('uuid', $eventToImport->id)->first();

                            if (!$exists) {
                                $event = new Event();
                                $event->name = $eventToImport->name->text;
                                $event->uuid = $eventToImport->id;
                                $event->eventbrite_url = $eventToImport->url;
                                $event->status = $eventToImport->status;
                                $event->currency = $eventToImport->currency;
                                $event->venue_uuid = $eventToImport->venue_id;
                                $event->organizer_uuid = $eventToImport->organizer_id;
                                $event->category_uuid = $eventToImport->category_id;
                                $event->location_id = $location->id;
                                $event->started_at = \Carbon\Carbon::parse($eventToImport->start->utc)->format('Y-m-d H:i:s');
                                $event->ended_at = \Carbon\Carbon::parse($eventToImport->end->utc)->format('Y-m-d H:i:s');
                                $event->json_data = json_encode($eventToImport);

                                $this->importData($eventData, $eventToImport);

                                $event->save();
                            }
                        }

                        if ($pagination->page_number < $pagination->page_count) {
                            $this->importEvents($pagination->page_number + 1);
                        }
                    } else {
                        throw new \Exception("Eventbrite API returned an unexpected status code: $statusCode" . PHP_EOL);
                    }

                } catch (\Exception $e) {
                    $msg = 'ERROR: Unable to import events: '. $e->getTraceAsString();
                    echo $msg . PHP_EOL;
                    Log::debug($msg);
                }
            }
        } else {
            echo 'No locations found in the database.' . PHP_EOL;
        }
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
       $this->importEvents();
    }
}
