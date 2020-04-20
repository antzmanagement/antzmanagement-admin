<template>
  <gmap-map
    :center="center"
    :zoom="zoom"
    map-type-id="terrain"
    style="width: 100%; height: 100%"
    :options="options"
  >
    <gmap-marker
      :key="index"
      v-for="(m, index) in markers"
      :position="m.position"
      @click="center=m.position"
    ></gmap-marker>
  </gmap-map>
</template>

<script>
export default {
  name: "GoogleMap",
  data() {
    return {
      // default to Montreal to keep it simple
      // change this to whatever makes sense
      zoom: 17,
      center: { lat: 4.321359, lng: 101.126428 },
      markers: [
        {
          lat: 4.321359,
          lng: 101.126428
        }
      ],
      options: {
        zoomControl: true,
        mapTypeControl: false,
        scaleControl: false,
        streetViewControl: true,
        rotateControl: false,
        fullscreenControl: true,
        disableDefaultUI: false
      }
    };
  },

  methods: {
    // addMarker() {
    //   const marker = {
    //     lat: 4.321471,
    //     lng: 101.126466
    //   };
    //   this.markers.push({ position: marker });
    //   this.center = marker;
    // }
    // geolocate: function() {
    //   navigator.geolocation.getCurrentPosition(position => {
    //     this.center = {
    //       lat: position.coords.latitude,
    //       lng: position.coords.longitude
    //     };
    //   });
    // }
  }
};
</script>