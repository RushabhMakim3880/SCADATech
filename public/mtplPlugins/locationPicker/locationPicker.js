class LocationPicker {
    constructor(input, options = {}) {
        this.input = input;
        this.defaultLocation = options.defaultLocation || [20.5937, 78.9629]; // Default to India
        this.zoomLevel = options.zoom || 5;
        this.mapContainer = null;
        this.map = null;
        this.marker = null;
        this.searchControl = null;
        this.provider = new GeoSearch.OpenStreetMapProvider();

        this.createMapContainer();
        this.initEvents();
    }

    createMapContainer() {
        this.mapContainer = document.createElement("div");
        this.mapContainer.className = "map-container";
        this.mapContainer.style.cssText = `display: none; opacity: 0; transition: opacity 0.3s ease-in-out; position: fixed; top: 10%; left: 10%; width: 80%; height: 80%; background: white; z-index: 1060; box-shadow: 0 0 10px rgba(0,0,0,0.5);`;
        document.body.appendChild(this.mapContainer);

        let closeButton = document.createElement("button");
        closeButton.innerText = "Close";
        closeButton.style.cssText = "position: absolute; top: 10px; right: 10px; z-index: 1061; background: red; color: white; border: none; padding: 5px 10px; cursor: pointer;";
        closeButton.addEventListener("click", () => this.closeMap());
        this.mapContainer.appendChild(closeButton);

        let mapDiv = document.createElement("div");
        mapDiv.className = "map";
        mapDiv.style.width = "100%";
        mapDiv.style.height = "100%";
        this.mapContainer.appendChild(mapDiv);
    }

    initEvents() {
        this.input.addEventListener("click", () => this.openMap());
    }

    openMap() {
        this.mapContainer.style.display = "block";
        setTimeout(() => { this.mapContainer.style.opacity = "1"; }, 10);

        if (!this.map) {
            this.map = L.map(this.mapContainer.querySelector(".map")).setView(this.defaultLocation, this.zoomLevel);
            L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", { attribution: "© OpenStreetMap" }).addTo(this.map);

            // Ensure GeoSearchControl is properly initialized
            this.addSearchControl();

            this.map.on("click", (e) => this.updateMarker(e.latlng.lat, e.latlng.lng));

            let savedCoords = this.input.value.split(",").map(Number);
            if (savedCoords.length === 2 && !isNaN(savedCoords[0]) && !isNaN(savedCoords[1])) {
                this.updateMarker(savedCoords[0], savedCoords[1]);
            }
        }
    }

    addSearchControl() {
        if (!this.searchControl) {
            this.searchControl = new GeoSearch.GeoSearchControl({
                provider: this.provider,
                style: 'bar',
                showMarker: false,
                autoClose: true,
                keepResult: true
            });

            this.map.addControl(this.searchControl);
        }
    }

    updateMarker(lat, lng) {
        if (this.marker) this.map.removeLayer(this.marker);
        this.marker = L.marker([lat, lng], { draggable: true }).addTo(this.map);
        this.marker.on("dragend", () => {
            let newLatLng = this.marker.getLatLng();
            this.input.value = `${newLatLng.lat}, ${newLatLng.lng}`;
        });
        this.input.value = `${lat}, ${lng}`;
    }

    closeMap() {
        this.mapContainer.style.opacity = "0";
        setTimeout(() => { this.mapContainer.style.display = "none"; }, 300);
    }
}