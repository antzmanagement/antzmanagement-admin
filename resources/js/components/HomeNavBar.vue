<template>
  <v-app-bar
    app
    :color="appBarConfig.color"
    :height="appBarConfig.height"
    :hide-on-scroll="appBarConfig.hideOnScroll"
  >
    <v-avatar class="mr-3" color="grey lighten-5" size="70">
      <v-img
        contain
        max-height="70%"
        src="https://res.cloudinary.com/ipoh-drum/image/upload/v1587403784/mountain-hostel-logo-design-template-vector-M5TFNF_vngjr3.jpg"
      ></v-img>
    </v-avatar>

    <v-toolbar-title class="font-weight-black headline">Antz Management</v-toolbar-title>
    <v-spacer></v-spacer>
    <v-btn-toggle tile group v-show="$vuetify.breakpoint.mdAndUp && navbarItems.length < 6">
      <v-btn
        v-for="(item, i) in navbarItems"
        :key="i"
        :to="{ name : item.name}"
      >{{ item.text | capitalizeFirstLetter }}</v-btn>
    </v-btn-toggle>

    <v-menu >
      <template v-slot:activator="{ on }">
        <v-btn icon v-on="on" v-show="$vuetify.breakpoint.mdAndDown || navbarItems.length >= 6" >
          <v-icon>mdi-menu</v-icon>
        </v-btn>
      </template>

      <v-list>
        <v-list-item v-for="(item, i) in navbarItems" :key="i" :to="{ name : item.name}">
          <v-list-item-title>{{ item.text | capitalizeFirstLetter }}</v-list-item-title>
        </v-list-item>
      </v-list>
    </v-menu>
  </v-app-bar>
</template>

<script>
export default {
  props: {
    source: String
  },
  data: () => ({
    appBarConfig: {
      color: "white",
      hideOnScroll: true,
      height: "100"
    },
    navbarItems: [
      { text: "Home", name: "home" },
      { text: "Services", name: "servicespage" },
      { text: "Rooms", name: "roompages" },
      { text: "Management", name: "management" },
      { text: "Profile", name: "profile" },
    ]
  }),
};
</script>