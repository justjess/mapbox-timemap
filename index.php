
<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8' />
    <title>Jess' fun with maps yay yay yay</title>
    <meta name='viewport' content='initial-scale=1,maximum-scale=1,user-scalable=no' />
    <script src='https://api.tiles.mapbox.com/mapbox-gl-js/v0.39.0/mapbox-gl.js'></script>
    <link href='https://api.tiles.mapbox.com/mapbox-gl-js/v0.39.0/mapbox-gl.css' rel='stylesheet' />
    <link href='https://www.mapbox.com/base/latest/base.css' rel='stylesheet' />
    <script src='https://unpkg.com/cheap-ruler@2.5.0/cheap-ruler.js'></script>

    <script src='https://npmcdn.com/@turf/turf/turf.min.js'></script>
    <script src="https://d3js.org/d3.v4.min.js"></script>



    <style>


        ::-webkit-scrollbar {
            width: 0px;
            background: transparent;
        }

        body { margin:0; padding:0; overflow:hidden;}

        #timemap { 
            position:absolute; 
            top:0; 
            bottom:0; 
            left:0px; 
            right:300px; 
            transition: opacity 1s;
            overflow:hidden;
        }

        .bg-purple{
            background:#4e3656;            
        }

        .purple {
            color:#4e3656;            
        }


        #sidebar{
            background: rgba(255,255,255,0.9);
            height:100%;
            border-left:0.25px solid #4e3656;
            color:#4e3656;
            position:absolute;
            right:0;
            width:300px;
            overflow:scroll;
        }

        .timelabel{
            display:none;
        }

        #sidebar input{
            border:1px solid rgba(78, 54, 86, 0.25);
        }

        .rounded-toggle{
            background: rgba(78, 54, 86, 0.25);
        }

        .view{
            display:none;
        }
        
        .query #queryview,
        .venue #venueview {
            display: block;
        }

        .section {
            border-bottom: 1px solid rgba(78, 54, 86, 0.25);
            overflow:scroll;
        }

        .venuelisting, .timemarker {
            cursor:pointer;
        }

        .venuelisting:hover{
            cursor: pointer;
            background: rgba(78, 54, 86, 0.1);
        }
        .mapboxgl-marker{
        }

        .mapboxgl-marker:hover .circle{
            transform:scale(1.5);
            transform-origin:center center;
            background:#f9886c;
            border:1px solid #e55e5e;
            z-index:99;
        }
        .mapboxgl-marker:hover .locationName{
            color:#e55e5e;
        }
        .animating .mapboxgl-marker{
            transition:all 0.5s ease-out;
        }

        .circle{
            border:1px solid #aa48ce;
            background:#4e3656;
            width:24px;
            height:24px;
            margin:2px;
            border-radius:12px;
            transition:all 0.25s;
        }

        .locationName {
            position:absolute;
            display:inline-block;
            line-height:1em;
            width:120px;
            margin-top:50%;
            max-width:100px;
            text-shadow: -2px 0 white, 0 2px white, 2px 0 white, 0 -2px white;
            transform:translateY(-50%);
            outlin:1px solid green;

        }

        .leftText{
            transition:all 0.25s;
            text-align: left;
            left:30px;
            padding-left: 20px;
            margin-left:-20px;
        }

        .rightText{
            transition:all 0.25s;
            text-align: right;
            right:30px;
            padding-right: 20px;
            margin-right: -20px;
        }

        .mapboxgl-marker:hover .leftText{
            left:40px;
        }

        .mapboxgl-marker:hover .rightText{
            right:40px;
        }

        .mapboxgl-canvas-container.mapboxgl-interactive, .mapboxgl-ctrl-nav-compass{
            cursor:auto;
        }

        .roads .timemarker {
            display:none;
        }

        .roads .timelabel {
            display:block;
        }

        .timelabel{
            height:24px;
            border-radius:12px;
            padding:2px;
        }

        #duration{
            padding:6px;
        }
        .rounded-toggle *,
        .rounded-toggle *.active {
            color:rgba(78, 54, 86, 1);
        }

        .truncate{
            max-width:80%;
        }

        .time:after{
            content: 'MIN';
            display:block;
            font-size: 0.5em;
            line-height: 1em;
        }

        .step{
            min-height:60px;
        }

        .instruction {
            width:80%;
            display:inline-block;
        }

        .feet:after{
            content: 'FEET';
            display:block;
            font-size: 0.5em;
            line-height: 1em;
        }

        .loading div{
            opacity:0;
        }

        @media (max-width: 900px) {
            #timemap { 
                bottom:200px; 
                right:0px; 
            }   

            #sidebar{
                height:200px;
                border-left:none;
                border-top:0.25px solid #4e3656;
                position:absolute;
                width:100%;
                left:0;
                right:0;
                bottom:0;
            }   

            .mobile-hidden {
                display:none;
            }
        }
    </style>
</head>
<body>

<div id='timemap' class='loading'></div>
<div class='rounded-toggle pin-bottomleft centerlock z100 hidden'>
  <a class='active center'>CENTER LOCK</a>
  <a class='center'>FREE PAN</a>
</div>
<div id='sidebar' class='query scroll-styled z100'>
    <div id='queryview' class='view'>
        <div class='section pad1x'>
            <div class=' small space-top1'>QUERY</div>

            <fieldset class="with-icon  space-bottom1">
                <span class="icon search"></span>
                <input type="text" value='ice cream' class="col12 round" oninput='state.newSearch("query", this.value)' placeholder='Search for'>
            </fieldset>

            <fieldset class="with-icon clearfix space-bottom1">
                <div class='button pin-topright' onclick='getLocation()'>Find me</div>

                <span class="icon marker"></span>
                <input type="text" value = 'Meridian Hill Park, Washington, DC' id='geocoder' class="col12 round" oninput='state.newSearch("locationString", this.value)' placeholder='Near'>
            </fieldset>
              <div class='pad1 geocoder z100 pin-top'></div>
            <div class='small space-top1'>VIA</div>
            <div class='rounded-toggle col12 clearfix mode mobile-cols'>
              <a class='active icon col4 center'></a>
              <a class='icon col4 center'></a>
              <a class='icon col4 center block'></a>
            </div>

            <div class=' small space-top1 mobile-hidden'>RESULTS</div>

        </div>

        <div class='section mobile-hidden'>
            <div id='results'></div>
        </div>

    </div>
    <div id='venueview' class='view'>
        <div class='section pad1'>
            <div class='button col12' id='toggle' onclick="state.updateSidebar('query')">Back</div>
        </div>
            <div id='instructions'>
            </div>

    </div>
</div>
<script>

    mapboxgl.accessToken = 'pk.eyJ1IjoicGV0ZXJxbGl1IiwiYSI6ImpvZmV0UEEifQ._D4bRmVcGfJvo1wjuOpA1g';
    
    // @TODO Consider loading map with static locations, then allow search to extend map results
    
    var state = {
        freePan: true,
        sidebarMode: 'query',
        startingPosition:[-77.035713, 38.920822],
        mode:'walking',
        locationString:'washington',
        timeViewZoom:13,
        throttleDuration: 500,
        timeMarkers:[],
        timeLabel:undefined,
        directions: undefined,
        ruler: function(){
            return cheapRuler(state.startingPosition[1], 'meters')
        },
        lastQueryTime: Date.now(),
        baseIconEquivalent:{
            walking: 'walk',
            cycling: 'bike',
            driving: 'car'
        },
        locations:{
            physical: turf.featureCollection([]),
            time: turf.featureCollection([])
        },
        query:'tailor',
        queryGeocoder: function(cb){
            var query = state.locationString.replace(' ', '+');
            var queryURL = 'https://api.mapbox.com/geocoding/v5/mapbox.places/'
            + query
            + '.json?access_token='+ mapboxgl.accessToken;
            d3.json(queryURL, function(err, resp){
                state.startingPosition =  resp.features[0].center;
                state.queryFoursquare();
                setBackground();
            })
        },
        queryFoursquare: function(cb){

            var coords = state.startingPosition;
            var queryURL = 'https://api.foursquare.com/v2/venues/search?query='+state.query+'&limit=50&radius=50000&intent=browse&ll='+[coords[1], coords[0]]+'&client_id=MZDCFZGD22TREAJNG54DXPIBJXQPLQKAB54FPOQD0QKUPKWP&client_secret=1LJRQTLGZ31HL5YSKJCZNGJYOESNOOBBJDDWXA4VU5JTPZ2D&v=20171111';


            d3.json(queryURL, function (err, resp){
                var venues = resp.response.venues;

                var geojson = venues.map(function(venue){

                    var properties = {
                        'name': venue.name,
                        'url': venue.url,
                        'address': venue.location.address
                    }

                    if (venue.categories[0]) {
                        properties.icon = venue.categories[0].icon.prefix;
                    }
                    return turf.point([venue.location.lng, venue.location.lat], properties)     
                })

                state.locations.physical = turf.featureCollection(geojson);

                //get travel times
                state.queryDuration(function(err, resp){
                    state.updateMarkers(resp);
                })
            })
        },
        queryDuration: function(cb){
            
            var coords = state.locations.physical.features.map(
                function(item){
                    return item.geometry.coordinates
                }
            );
            var queryURL = 'https://api.mapbox.com/directions-matrix/v1/mapbox/'+state.mode+'/'+state.startingPosition+';'+coords.join(';')+'?sources=0&destinations=all&access_token='+ mapboxgl.accessToken
            d3.json(queryURL, cb)
        },

        queryDirections: function(coords, d){

            d.geometry.coordinates = d.properties.physicalLocation;

            map.getSource('locations')
                .setData(d);

            var strung = coords.join(';')
            var queryURL = 'https://api.mapbox.com/directions/v5/mapbox/'+state.mode+'/'+strung+'?geometries=geojson&steps=true&access_token='+ mapboxgl.accessToken;

            d3.json(queryURL, function(err, resp){
                state.directions = resp.routes[0];
                state.drawDirections(coords);
                state.updateSidebar('venue', resp);
            })
        },
        drawDirections: function(endpoints){

            //draw line to map
            var line = (state.directions.geometry.coordinates);
            
            line.push(endpoints[1]);

            line
                .unshift(endpoints[0])

            line = turf.lineString(line)
            
            map.getSource('route')
                .setData(line);
            map.setClasses(['roads'])

            //set the timelabel marker

            var midpointDistance = state.directions.distance/2;
            var midpoint = turf.along(turf.lineString(state.directions.geometry.coordinates), midpointDistance/1000, 'kilometers');

            document.querySelector('#duration')
                .innerHTML = (state.directions.duration/60).toFixed(0)+' min'

            state.timeLabel
                .setLngLat(midpoint.geometry.coordinates)

            //toggle map object class to fade out timelabels
            d3.select('#timemap')
                .classed('roads', true)

            // zoom in to frame the linestring properly
            var bbox = line.geometry.coordinates
                .reduce(function(bounds, coord) {
                    return bounds.extend(coord);
                }, new mapboxgl.LngLatBounds(state.startingPosition, state.startingPosition));

            map.fitBounds(bbox, {
                padding: {left:60, right:60, top:60, bottom:60},
                duration: 500
            });

        },

        updateSidebar: function(viewType, item){
            d3.select('#sidebar')
                .attr('class', viewType+' scroll-styled z100');

            if( viewType === 'venue'){

                var data = (item.routes[0].legs[0].steps)

                d3.selectAll('.step')
                    .remove();

                var instructions = d3.select('#instructions')
                    .selectAll('.step')
                    .data(data)
                    .enter()
                    .append('div')
                    .attr('class', 'step pad1 keyline-bottom');

                instructions
                    .append('div')
                    .attr('class', 'fr prose prose-big center')
                    .text(function(d){
                        if (d.duration === 0) return
                        else {
                            var seconds =  Math.round(d.duration % 60);
                            seconds = seconds< 10 ? '0' + seconds : seconds;
                            return Math.floor(d.duration/60) + ':' + seconds;
                        }
                    })

                // maneuver instruction
                instructions
                    .append('div')
                    .attr('class','instruction')
                    .text(function(step){
                        var abbr = step.maneuver.instruction
                            .replace('Avenue', 'Ave')
                            .replace('Place', 'Pl')
                            .replace('Street', 'St')
                            .replace('Boulevard', 'Blvd')

                        return abbr
                    });

                //distance reading
                instructions
                    .append('div')
                    .attr('class', 'quiet small')
                    .text(function(d){
                        var string = d.distance > 400 ? (d.distance/1609).toFixed(1) + ' mi' : Math.round(d.distance*3.28084) + ' ft'
                        return string
                    })
               
            }

            else {

                var sorted = state.locations.time.features
                    .sort(function(x,y){
                        return d3.ascending(x.properties.duration, y.properties.duration);
                    })

                d3.selectAll('.venuelisting')
                    .remove();

                var venues = d3.select('#results')
                    .selectAll('.venuelisting')
                    .data(sorted)
                    .enter()
                    .append('div')
                    .attr('class', 'venuelisting pad1 keyline-bottom')
                    .on('click', function(d){
                        var coords = [state.startingPosition, d.properties.physicalLocation];
                        state.queryDirections(coords, d);
                    });


                venues
                    .append('div')
                    .attr('class', 'fr prose prose-big center time')
                    .text(function(d){
                        return Math.round(d.properties.duration/60)
                    })

                venues
                    .append('div')
                    .attr('class', 'strong truncate')
                    .text(function(d){
                        return d.properties.name
                    })

                venues
                    .append('div')
                    .attr('class', 'quiet small')
                    .text(function(d){
                        var address = d.properties.address || 'Address unlisted'
                        return address
                    })


            }
        },
        updateMarkers: function(resp){

            state.locations.time.features = [];
            var durations = resp.durations[0];

            state.locations.physical.features.forEach(function(d,i){

                var bearing = (turf.bearing(turf.point(state.startingPosition), d))
                var pt = turf.destination(turf.point(state.startingPosition), durations[i+1]/60/10, bearing, 'kilometers');
                pt.properties = d.properties
                pt.properties.duration = durations[i+1];
                pt.properties.textOrientation = bearing<0 ? 'rightText' : 'leftText';
                pt.properties.bearing = bearing+180;
                pt.properties.physicalLocation = d.geometry.coordinates;

                state.locations.time.features.push(pt)
            })

            map.getSource('locations')
                .setData(state.locations.physical)

            if (state.timeMarkers.length === 0){

                state.timeMarkers = state.locations.time.features.map(function(item){

                    var el = document.createElement('div');

                    el.classList+= ' timemarker';

                    var truncatedName = item.properties.name.length>30 ? item.properties.name.substr(0,27)+'...' : item.properties.name;
                    el.innerHTML = '<img class="circle inline" src="'+item.properties.icon+'64.png"><div class="locationName '+
                        item.properties.textOrientation+'"><div class="vcenter">'+truncatedName+'</div>';

                    var marker = new mapboxgl.Marker(el);

                    marker
                        .setLngLat(item.geometry.coordinates)
                        .addTo(map);


                    //bind click handler to each marker
                    var coordinates = [state.startingPosition, item.properties.physicalLocation];

                    //when clicked, get directions for venue, and update sidebar in parallel
                    marker.getElement().addEventListener('click', function(){
                        state.queryDirections(coordinates, item)
                    })
                    return marker;
                })

            }

            else {
                state.timeMarkers.forEach(function(marker, index){
                    d3.select('#timemap')
                        .classed('animating', true);

                    map.once('movestart', function(){
                        d3.select('#timemap')
                            .classed('animating', false);                        
                    })
                    marker
                        .setLngLat(state.locations.time.features[index].geometry.coordinates)
                })
            }

            state.updateSidebar('query')

        },

        newSearch: function(property, value){

            state[property] = value;
            
            if (property === 'mode'){

                //get travel times
                state.queryDuration(function(err, resp){
                    state.updateMarkers(resp);
                });

                d3.select('.timelabel .icon')
                    .attr('class', function(){
                        return 'icon purple '+ state.baseIconEquivalent[state.mode]
                    })
            }

            else {

                state.lastQueryTime = Date.now();
                
                if (property === 'query') {

                    window.setTimeout(
                        function(){
                            throttler(state.queryFoursquare)
                        }, 
                        state.throttleDuration
                    );
                }

                if (property === 'locationString'){

                    window.setTimeout(
                        function(){
                            throttler(state.queryGeocoder);
                            setBackground();
                        }, 
                        state.throttleDuration
                    );                    
                }
            }


            function throttler(fn, cb){

                if (Date.now() < state.lastQueryTime+state.throttleDuration) return;

                //remove old markers
                state.timeMarkers.forEach(function(marker){
                    marker.remove();
                })

                state.timeMarkers = [];
                fn(cb)
            }

        }
    };

    var map = new mapboxgl.Map({
        container: 'timemap', // container id
        style: 'mapbox://styles/mapbox/light-v8', //stylesheet location
        dragPan: state.freePan,
        scrollZoom: state.freePan,
        minZoom:11,
        center: state.startingPosition, // starting position
        zoom: state.timeViewZoom // starting zoom
    });


    var mapObjs = [map];

    if (!state.freePan){
        var ids =['#timemap'];
        ids.forEach(function(id, i){
            var object = mapObjs[i];
            document.querySelector(id)
                .addEventListener('mousewheel', function(e){
                    var z = object.getZoom()-e.deltaY/100
                    object.setZoom(z)
                })
        })
    }

  function pointBuffer (pt, radius, units, resolution) {

    var buffer = turf.circle(turf.point(pt), radius, resolution, 'kilometers');

    return buffer

  }


    function setBackground(){

        var circles = [];
        var labelRotations = [0, 60, -60, 0, 60, -60];

        for (var s=120; s>=1; s--){

            var circle = pointBuffer(state.startingPosition, s/10, 'kilometers',180);

            // circles every 10 and 30 minutes (minor and major, respectively)

            if (s%10 === 0) {
                var primacy = s%30 === 0 ? 'major' : 'minor';
                var suffix = s%30 === 0 ? ' MIN' : '';

                circle.properties.primacy = primacy;
                circle.properties.label = s;
                circles.push(circle)

                for (var i=0; i<360; i+=60){

                    var label = turf.destination(turf.point(state.startingPosition), s/10, i, 'kilometers');

                    label.properties = {
                        'label': s,
                        'size': primacy,
                        'suffix': suffix,
                        'rotation': labelRotations[i/60]
                    }

                    circles.push(label)

                    //guide lines
                    if (s==120 && i<360){

                        var line = [label.geometry.coordinates];
                        line.push(state.startingPosition)

                        circles.push(turf.lineString(line, {label:s, primacy: 'guide'}))
                    }
                }

            }

            //small circles (per minute)
            else {

                circle.properties.label = s%10;
                circle.properties.primacy = 'tick';

                circles.push(circle)
            }


        }

        map.getSource('circles')
            .setData(turf.featureCollection(circles));

        map.getSource('origin')
            .setData(turf.point(state.startingPosition));
        map.setCenter(state.startingPosition);

    }

    map.on('load', function(){
        
        state.newSearch('query', 'ice cream')

        var el = document.createElement('div');

        el.classList+= ' timelabel strong pad0 bg-blue dark';
        el.innerHTML = '<span id="duration" class=""></span>';
        var marker = new mapboxgl.Marker(el)
        .setLngLat(state.startingPosition)
        .addTo(map);
    
        state.timeLabel =  marker;
        map
        .addSource('locations',
            {
                type:'geojson', 
                data: state.locations.time
            }
        )
        .addSource('route',
            {
                type:'geojson', 
                data: turf.featureCollection([])
            }
        )
        .addSource('circles',
            {
                type:'geojson', 
                data: turf.featureCollection([])
            }
        )

        map
            .addLayer({
                'id': 'cover',
                'type':'background',
                'paint':{
                    'background-color': '#fff',
                    'background-opacity':0.99
                },
                'paint.roads':{
                    'background-opacity': 0
                }
            })
            .addLayer({
                'id':'routearrows',
                'type':'symbol',
                'source':'route',
                'layout':{
                    'symbol-placement': 'line',
                    'text-field': '▶',
                    'text-size':{
                        base:1,
                        stops:[[12,18],[22,60]]
                    },
                    'symbol-spacing': {
                        base:1,
                        stops:[[12,30],[22,160]]
                    },
                    'text-keep-upright': false
                },
                'paint':{
                    'text-color': '#4e3656',
                    'text-halo-color':'hsl(55, 11%, 96%)',
                    'text-halo-width':3
                }
            }, 'waterway-label')
            .addLayer({
                'id': 'route',
                'type':'line',
                'source':'route',
                'paint':{
                    'line-width':{
                        'base':1,
                        'stops':[[10,1],[16,5]]
                    },
                    'line-color': '#4e3656'
                }
            }, 'cover')
            .addLayer({
                'id':'guide-lines',
                'type':'line',
                'source':'circles',
                'filter':['==', 'primacy', 'guide'],
                'layout':{
                    'line-cap':'round'
                },
                'paint':{
                    'line-color':'#4e3656',
                    'line-dasharray':[0,6],
                    'line-width':{
                        'base':1,
                        'stops':[[10,0.25],[16,2]]
                    }
                },
                'paint.roads':{
                    'line-opacity':0
                }
            })
            .addLayer({
                'id':'circles-tick',
                'type':'line',
                'source':'circles',
                'filter':['==', 'primacy', 'tick'],
                'paint':{
                    'line-color':{
                        "property": "label",
                        'type': 'exponential',
                        'base':0.95,
                        "stops": [
                            [-3, '#4e3656'],
                            [15, '#fff'],
                        ]
                    },
                    'line-width':{
                        'base':0.75,
                        'stops':[[9,0],[16,0.5]]
                    }
                },
                'paint.roads':{
                    'line-opacity':0
                }
            })
            .addLayer({
                'id':'circles-major ',
                'type':'line',
                'source':'circles',
                'filter':['in', 'primacy', 'major', 'minor'],
                'layout':{
                    'line-cap':'round'
                },
                'paint':{
                    'line-color':{
                        "property": "label",
                        'type': 'exponential',
                        'base':0.99,
                        "stops": [
                            [0, '#4e3656'],
                            [300, '#fff']
                        ]
                    },
                    'line-width':{
                        'base':1,
                        'stops':[[6,1],[18,1]]
                    },
                },
                'paint.roads':{
                    'line-opacity':0
                }
            })
            .addLayer({
                'id':'timelabel',
                'type':'symbol',
                'filter':["==", "$type", "Point"],
                'source':'circles',
                'layout':{
                    'text-padding':1,
                    'text-size': {
                        "property": "size",
                        "type": "categorical",
                        "stops": [
                          [{zoom: 10, value: 'minor'}, 0],
                          [{zoom: 10, value: 'major'}, 12],
                          [{zoom: 12, value: 'minor'}, 14],
                          [{zoom: 12, value: 'major'}, 16]
                        ]
                    },
                    'text-field': '{label}{suffix}',
                    'text-font': ['Open Sans Regular'],
                    'text-rotate': {
                        "property": "rotation",
                        "type": "identity",
                    }
                },
                'paint':{
                    'text-color':{
                        "property": "size",
                        "type": "categorical",
                        "stops": [
                            ['minor', '#aa48ce'],
                            ['major', '#4e3656']
                        ]
                    },
                    'text-opacity':1,
                    'text-halo-color':{
                        "property": "label",
                        'type': 'exponential',
                        'base':0.99,
                        "stops": [
                            [0, '#fff'],
                            [300, '#fff']
                        ]
                    },
                    'text-halo-width':2
                },
                'paint.roads':{
                    'text-opacity':0
                }
            })

            .addLayer({
                'id':'locationtext',
                'type':'symbol',
                'source':'locations',
                'layout':{
                    'text-field':'{name}',
                    'text-size': 12,
                    'text-offset': {
                        'property':'bearing', 
                        'type':'interval',
                        'stops':[[0,[-1,0]], [180, [1,0]]]
                    },
                    'text-justify':{
                        'property':'bearing', 
                        'type':'interval',
                        'stops':[[0,'right'], [180, 'left']]
                    },
                    'text-optional': true,
                    'text-padding':5,
                    'text-font': ['Open Sans Regular'],
                    'text-anchor':{
                        'property':'bearing', 
                        'type':'interval',
                        'stops':[[0,'right'], [180, 'left']]
                    }
                },
                'paint':{
                    'text-color':'#000',
                    'text-halo-color':'#fff',
                    'text-halo-width':2,
                    'text-opacity':0,
                    'icon-opacity':0
                },
                'paint.roads':{
                    'text-opacity':1,
                    'icon-opacity':1
                }
            }, 'cover')
            .addLayer({
                'id':'locationdot',
                'type':'circle',
                'source':'locations',
                'paint':{
                    'circle-opacity':0,
                    'circle-stroke-opacity':0,
                    'circle-color': '#4e3656',
                    'circle-stroke-color':'#fff',
                    'circle-stroke-width':{
                        'base':1,
                        'stops':[[10,1],[18,3]]
                    },
                    'circle-radius':{
                        'base':1,
                        'stops':[[10,1],[18,8]]
                    },
                    //'circle-opacity':0.25
                },
                'paint.roads':{
                    'circle-opacity':1,
                    'circle-stroke-opacity':1
                }
            },'cover') 
            .addLayer({
                'id':'origin',
                'type':'circle',
                'source':{
                    type:'geojson', 
                    data: turf.point(state.startingPosition)
                },
                'paint':{
                    'circle-color':'#fff',
                    'circle-stroke-color':'#4e3656',
                    'circle-stroke-width':3,
                    'circle-radius':6
                }
            })
            .addLayer({
                'id':'roaddot',
                'type':'circle',
                'source':{
                    type:'geojson', 
                    data: turf.featureCollection([])
                },
                'paint':{
                    'circle-color':'red',
                    'circle-stroke-color':'#ddd',
                    'circle-stroke-width':1,
                    'circle-radius':10
                }
            })
            .addLayer({
                'id':'roadpath',
                'type':'line',
                'source':{
                    type:'geojson', 
                    data: turf.featureCollection([])
                },
                'paint':{
                    'line-color':'red',
                    'line-width':2
                }
            }) 
            setBackground();
            

            d3.select('#toggle').on('click', function(e){

                var forward = map.getClasses().length === 0;
                map.setClasses((forward ? ['roads'] : []));

                d3.select('#timemap')
                    .classed('roads', forward);
                map.flyTo({zoom:state.timeViewZoom,center:state.startingPosition})

            })


                d3.select('#timemap')
                    .classed('loading', false);

    })



    function getLocation() {

        if (navigator.geolocation) {
            document.querySelector('#geocoder').setAttribute('value','Getting your location...')

            navigator.geolocation.getCurrentPosition(showPosition);
            d3.select('#timemap')
                .classed('loading', true);

            function showPosition(position) {

                //remove old markers
                state.timeMarkers.forEach(function(marker){
                    marker.remove();
                })

                state.timeMarkers = [];

                state.startingPosition = [position.coords.longitude, position.coords.latitude];
                state.queryFoursquare();

                d3.select('#timemap')
                    .classed('loading', false);

                setBackground();
                document.querySelector('#geocoder')
                    .setAttribute('value','Current location');
            }
        } else { 
            alert("Geolocation is not supported by this browser.");
        }
    }

    //getLocation()

    //wire up sidebar functionality

    var mode = d3.selectAll('.mode a')
        .data(['walk', 'bike', 'car'])
        .attr('class', function(d,i){
            var active = i===0 ?  'active' : ''
            return active + ' icon col4 center '+ d
        })

    mode
        .on('click', function(d,i){
            mode
                .classed('active', function(){
                    return d3.select(this).classed(d)
                })

            var modeTerms = ['walking', 'cycling', 'driving'];

            state.newSearch('mode', modeTerms[i])
        })

    var centerLock = d3.selectAll('.centerlock a')
        .data([false, true])
        .attr('class', function(d,i){
            var active = i===0 ?  'active' : ''
            return active + ' center '+ d
        })

    centerLock
        .on('click', function(d,i){
            centerLock
                .classed('active', function(){
                    return d3.select(this).classed(d)
                })

            state.freePan = d
        })
</script>

</body>
</html>
