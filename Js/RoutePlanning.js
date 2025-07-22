class RouteMap {

    // sets up properties
      constructor(destination, waypoints = [], options = {}) {
        this.destination = destination;
        this.waypoints = waypoints.map(loc => ({ location: loc, stopover: true }));
        this.options = options;
        this.map = null;
        this.directionsService = new google.maps.DirectionsService();
        this.directionsRenderer = new google.maps.DirectionsRenderer();
      }

      // creates a new map at the orgin
      initMap(origin) {
        this.map = new google.maps.Map(document.getElementById("map"), {
          center: origin,
          zoom: 7
        });
        this.directionsRenderer.setMap(this.map);
        this.calculateRoute(origin);
      }

      // calculates route using the inpured information
      calculateRoute(origin) {
        const request = {
          origin: origin,
          destination: this.destination,
          waypoints: this.waypoints,
          optimizeWaypoints: this.options.optimize || false,
          travelMode: google.maps.TravelMode.DRIVING,
          avoidTolls: this.options.avoidTolls || false,
          avoidHighways: this.options.avoidHighways || false
        };

        this.directionsService.route(request, (result, status) => {
          if (status === "OK") {
            this.directionsRenderer.setDirections(result);
            this.displayRouteInfo(result);
          } else {
            alert("Could not display directions due to: " + status);
          }
        });
      }
      // Info calculation
      displayRouteInfo(result) {
        const route = result.routes[0];
        let totalDistance = 0;
        let totalDuration = 0;

        route.legs.forEach(leg => {
          totalDistance += leg.distance.value;
          totalDuration += leg.duration.value;
        });

        document.getElementById("route-info").innerText =
          `Total Distance: ${(totalDistance / 1000).toFixed(2)} km | Total Time: ${(totalDuration / 60).toFixed(1)} mins`;
      }
    }
    // Converts address string to LatLng using Geocoder, returns Promise
    function geocodeAddress(address) {
    return new Promise((resolve, reject) => {
        geocoder.geocode({ address: address }, (results, status) => {
        if (status === "OK" && results[0]) {
            resolve(results[0].geometry.location);
        } else {
            reject(`Geocode failed for ${address}: ${status}`);
        }
        });
    });
    }

    function initMap() {
        geocoder = new google.maps.Geocoder();

        // Set up button click handler
        document.getElementById("showRouteBtn").addEventListener("click", async () => {
            const originInput = document.getElementById("origin").value.trim();
            const destinationInput = document.getElementById("destination").value.trim();
            const waypointsInput = document.getElementById("waypoints").value.trim();

            if (!originInput || !destinationInput) {
            alert("Please enter both origin and destination.");
            return;
            }

            try {
            // Geocode origin & destination to LatLng
            const originLatLng = await geocodeAddress(originInput);
            const destinationLatLng = await geocodeAddress(destinationInput);

            // For Waypoints, split by new lines and remove empty lines
            const stops = waypointsInput
                ? waypointsInput.split("\n").map(s => s.trim()).filter(s => s.length > 0)
                : [];

            // Create RouteMap and initialize
            routeMap = new RouteMap(destinationLatLng, stops, { optimize: true });
            routeMap.initMap(originLatLng);
            } catch (error) {
            alert(error);
            }
        });
    }