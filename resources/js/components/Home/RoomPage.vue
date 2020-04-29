<template>
  <v-app>
    <home-navbar></home-navbar>

    <v-content>
      <section id="rooms" class="grey lighten-3">
        <v-img
          :aspect-ratio="4/3"
          src="https://res.cloudinary.com/ipoh-drum/image/upload/v1587403963/CP-B1112-2_olvbdv.jpg"
          height="400px"
        ></v-img>

        <room-type-listing></room-type-listing>
      </section>

       <section id="contactUs" class="grey lighten-3">
         <contact-us></contact-us>
      </section>
    </v-content>
    <home-footer></home-footer>
  </v-app>
</template>

<script>
export default {
  props: {
    roomTypes: {
      type: Array,
      default: () => [
      ]
    }
  },
  data: () => ({}),
  methods: {}
};
</script>