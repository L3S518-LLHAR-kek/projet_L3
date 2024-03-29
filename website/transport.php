<?php 
require_once "config.php";

function alreadyInDB($origin, $destination){
    //TO-DO:: implement a realistic version of this function once the DB is up to date
    return false;
}

// make a request to the google maps API to get the distance between two points
function distanceMatrixRequestBuilder($origins, $destinations, $mode, $transit_mode=null){
    $url = "https://maps.googleapis.com/maps/api/distancematrix/json?".
    "origins=".urlencode($origins["lat"].",".$origins["lon"]).
    "&destinations=".urlencode($destinations["lat"].",".$destinations["lon"]).
    "&key=".MAPS_API_KEY.
    "&mode=".$mode;
    if ($transit_mode != null){
        $url .= "&transit_mode=".$transit_mode;
    }
    return $url;
}

abstract class Transport {

    public $model;  // model of the vehicle Renault Twingo, TGV, A320, etc
    public $fuel;
    public $fuelConsumption;  // in L/100km
    public $passengers; // number of passengers in the vehicle

    public abstract function fetchDistance($origin, $destination);
    // should return an array with distance, duration, carbon footprint, fuel consumption, travel cost
    public abstract function getTravel($origin, $destination);

    public function getFuelPrice(){
        // return the price of the fuel in €/L 
        // TO-DO: implement a function to fetch the fuel price from the DB or whatsoever
        switch ($this->fuel) {
            case "ESSENCE":
                break;
            case "GAZOLE":
                break;
            case "ELECTRIC":
                break;
            case "ESS+ELEC HNR":
                break;
            case "GAZ+ELEC HNR":
                break;
            case "ELEC+ESSENC HR":
                break;
            case "ESS+G.P.L.":
                break;
            case "SUPERETHANOL":
                break;
            case "GAZ NAT.VEH":
                break;
        }
        return 1.80;
    }

    function getFuelEmission() {
        // returns the CO2 emission factor of the fuel in kg CO2e/L
        // if a car is hybrid we return the value of its non electric fuel
        // the fuel consumption is given by the database
        $val = null;
        switch ($this->fuel) {
            case "ESSENCE":
                $val = 2.5; 
                break;
            case "GAZOLE":
                $val = 2.64;
                break;
            case "ELECTRIC":
                $val = 0;
                break;
            case "G.P.L.";
                $val = 1.6;
                break;
            case "ESS+ELEC HNR":
                $val = 2.5;
                break;
            case "GAZ+ELEC HNR":
                $val = 2.64;
                break;
            case "ELEC+ESSENC HR":
                $val = 2.5;
                break;
            case "ESS+G.P.L.":
                $val = 2.5;
                break;
            case "SUPERETHANOL":
                $val = 1.63;
                break;
            case "GAZ NAT.VEH":
                $val = 1.94;
                break;
            case "LOCOMOTIVE DIESEL FUEL":
                $val = 2.68;
                break;
            default: // average of gasoline and essence
                $val = 2.6;
        }
        return $val;
    }
}

class Car extends Transport {

    public $body; // BERLINE, SUV, BREAK, etc
 //TO-DO: wait for the form to be up to date to implement a function to fetch the body from the DB

    function __construct($body, $fuel, $fuelConsumption, $passengers) {
        $this->body = $body;
        $this->fuel = $fuel;
        $this->fuelConsumption = $fuelConsumption; // in L/100km
        $this->passengers = $passengers;
    }

    function fetchDistance($origin, $destination) {
        $url = distanceMatrixRequestBuilder($origin, $destination, "driving");
        $response = file_get_contents($url);
        return json_decode($response, true);
    }

    function getTravel($origin, $destination) {
        $travel = array( "distance" => null,        // in m
                        "duration" => null,         // in seconds
                        "fuelConsumption" => null,  // in L
                        "carbonFootprint" => null,  // in kg CO2e
                        "carbonFootprintPassenger" => null,  // in kg CO2e / passenger
                        "travelCost" => null        // in €
                    );
        if (!alreadyInDB($origin, $destination)){
            $jsonAPI = $this->fetchDistance($origin, $destination);
            if ($jsonAPI["rows"][0]["elements"][0]["status"] == "NOT_FOUND") {
                // itineraries not found
                $travel = null;
            } else {
            $travel["distance"] = $jsonAPI["rows"][0]["elements"][0]["distance"]["value"];
            $travel["duration"] = $jsonAPI["rows"][0]["elements"][0]["duration"]["value"];
            $travel["fuelConsumption"] = $travel["distance"]/1000 * ($this->fuelConsumption/100); 
            // getFuelEmission() returns the CO2 emission factor of the fuel in kg CO2e/L
            $travel["carbonFootprint"] = $travel["fuelConsumption"] * $this->getFuelEmission();
            $travel["carbonFootprintPassenger"] = $travel["carbonFootprint"] / $this->passengers;
            $travel["travelCost"] = $travel["fuelConsumption"] * $this->getFuelPrice();
            }
        }
        else {
            // TO-DO: fetch the data from the DB
        }
        return $travel;
        //TO-DO: update the database with the data fetched
    }

}

class Train extends Transport {
     // TGV

     // TO-DO: change to real values those are fake
    public $fuel = "GAZOLE"; // TO-DO: wait for the form to be up to date to implement a function to fetch the fuel from the DB 
    public $fuelConsumption = 6.2; // in L/100km
    public $passengers = 200; // number of passengers in the vehicle

    function fetchDistance($origin, $destination) {
        $url = distanceMatrixRequestBuilder($origin, $destination, "transit", "rail");
        $response = file_get_contents($url);
        return json_decode($response, true);
    }


    function getTravel($origin, $destination) {
        $travel = array( "distance" => null,        // in m
                        "duration" => null,         // in seconds
                        "fuelConsumption" => null,  // in L
                        "carbonFootprint" => null,  // in kg CO2e
                        "carbonFootprintPassenger" => null,  // in kg CO2e / passenger
                        "travelCost" => null        // in €
                    );
        if (!alreadyInDB($origin, $destination)){
            $jsonAPI = $this->fetchDistance($origin, $destination);
            if ($jsonAPI["rows"][0]["elements"][0]["status"] == "NOT_FOUND") {
                // itineraries not found
                $travel = null;
            } else {
            $travel["distance"] = $jsonAPI["rows"][0]["elements"][0]["distance"]["value"];
            $travel["duration"] = $jsonAPI["rows"][0]["elements"][0]["duration"]["value"];
            $travel["fuelConsumption"] = $travel["distance"]/1000 * ($this->fuelConsumption/100); 
            // getFuelEmission() returns the CO2 emission factor of the fuel in kg CO2e/L
            $travel["carbonFootprint"] = $travel["fuelConsumption"] * $this->getFuelEmission();
            $travel["carbonFootprintPassenger"] = $travel["carbonFootprint"] / $this->passengers;
            $travel["travelCost"] = $travel["fuelConsumption"] * $this->getFuelPrice();
            }
        }
        else {
            // TO-DO: fetch the data from the DB
        }
        return $travel;
        //TO-DO: update the database with the data fetched
    }
}

class Plane extends Transport {

    public $model; // A81, A320, etc
    function __construct($model, $fuel, $fuelConsumption, $passengers){
        $this->model = $model;
        $this->fuel = $fuel;
        $this->fuelConsumption = $fuelConsumption;
        $this->passengers = $passengers;
    }

    function fetchDistance($origin, $destination) {
        return null;
    }


    function getTravel($origin, $destination){
        $travel = array( "distance" => null,        // in m
                        "duration" => null,         // in seconds
                        "fuelConsumption" => null,  // in L
                        "carbonFootprint" => null,  // in kg CO2e
                        "carbonFootprintPassenger" => null,  // in kg CO2e / passenger
                        "travelCost" => null        // in €
                    );
    }

        function orthodromeDistance($origin, $destination) {
            $lat1 = deg2rad($origin['lat']);
            $lon1 = deg2rad($origin['lon']);
            $lat2 = deg2rad($destination['lat']);
            $lon2 = deg2rad($destination['lon']);
            
            $delta_lon = $lon2 - $lon1;
            $central_angle = acos(sin($lat1) * sin($lat2) + cos($lat1) * cos($lat2) * cos($delta_lon));
            // Earth's radius in kilometers
            $earth_radius = 6371.0;

            return $central_angle * $earth_radius;
        }

        function getCarboonFootprint($distance) {
            if ($distance < $DISTANCE_CEIL){
                return $distance;
            }
            else {
                return $distance;
            }
        }

}