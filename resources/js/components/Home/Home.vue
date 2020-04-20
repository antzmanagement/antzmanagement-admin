<template>
  <v-app>
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
          src="https://cdn.vuetifyjs.com/images/logos/vuetify-logo-dark.png"
        ></v-img>
      </v-avatar>

      <v-toolbar-title class="font-weight-black headline">Antz Management</v-toolbar-title>
      <v-spacer></v-spacer>
      <v-btn-toggle tile group v-show="$vuetify.breakpoint.mdAndUp">
        <v-btn
          v-for="(item, i) in navbarItems"
          :key="i"
          :to="{ name : item.name}"
        >{{ item.text | capitalizeFirstLetter }}</v-btn>
      </v-btn-toggle>

      <v-menu v-show="$vuetify.breakpoint.smAndDown">
        <template v-slot:activator="{ on }">
          <v-btn icon v-on="on" v-show="$vuetify.breakpoint.smAndDown">
            <v-icon>mdi-dots-vertical</v-icon>
          </v-btn>
        </template>

        <v-list>
          <v-list-item v-for="(item, i) in navbarItems" :key="i" :to="{ name : item.name}">
            <v-list-item-title>{{ item.text | capitalizeFirstLetter }}</v-list-item-title>
          </v-list-item>
        </v-list>
      </v-menu>
    </v-app-bar>

    <v-content>
      <section id="head" :class="sectionConfig.head.class">
        <v-row no-gutters>
          <v-img
            :min-height="'calc(100vh - ' + $vuetify.application.top + 'px)'"
            src="https://images.unsplash.com/photo-1487017159836-4e23ece2e4cf?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1951&q=80"
          >
            <v-theme-provider dark>
              <v-container fill-height>
                <v-row align="center" class="white--text mx-auto" justify="center">
                  <v-col class="white--text text-center" cols="12" tag="h1">
                    <span
                      class="font-weight-light"
                      :class="[$vuetify.breakpoint.smAndDown ? 'display-1' : 'display-2']"
                    >WELCOME TO</span>

                    <br />

                    <span
                      :class="[$vuetify.breakpoint.smAndDown ? 'display-3': 'display-4']"
                      class="font-weight-black"
                    >Antz Management</span>
                  </v-col>

                  <v-btn class="align-self-end" fab outlined @click="$vuetify.goTo('#aboutMe')">
                    <v-icon>mdi-chevron-double-down</v-icon>
                  </v-btn>
                </v-row>
              </v-container>
            </v-theme-provider>
          </v-img>
        </v-row>
      </section>

      <section id="aboutMe" :class="sectionConfig.aboutMe.class">
        <div class="py-12"></div>

        <v-container class="text-center">
          <h2 class="display-2 font-weight-bold mb-3">ABOUT US</h2>

          <v-responsive class="mx-auto mb-12" width="56">
            <v-divider class="mb-1 black"></v-divider>

            <v-divider class="black"></v-divider>
          </v-responsive>

          <v-responsive
            class="mx-auto title font-weight-light mb-8"
            max-width="720"
          >Vuetify is the #1 component library for Vue.js and has been in active development since 2016. The goal of the project is to provide users with everything that is needed to build rich and engaging web applications using the Material Design specification. It accomplishes that with a consistent update cycle, Long-term Support (LTS) for previous versions, responsive community engagement, a vast ecosystem of resources and a dedication to quality components.</v-responsive>

          <v-avatar class="elevation-12 mb-12" size="128">
            <v-img src="https://cdn.vuetifyjs.com/images/john.png"></v-img>
          </v-avatar>

          <div></div>

          <v-btn color="grey" @click="$vuetify.goTo('#contact')" outlined large>
            <span class="grey--text text--darken-1 font-weight-bold">Contact Us Now !</span>
          </v-btn>
        </v-container>

        <div class="py-12"></div>
      </section>

      <section id="services" :class="sectionConfig.services.class">
        <div class="py-12"></div>

        <v-container>
          <h2 class="display-2 font-weight-bold mb-3 text-uppercase text-center">OUR SERVICES</h2>

          <v-responsive class="mx-auto mb-12" width="56">
            <v-divider class="mb-1 black"></v-divider>

            <v-divider class="black"></v-divider>
          </v-responsive>

          <v-row justify="center">
            <v-card
              flat
              hover
              width="150px"
              class="ma-2 white lighten-3"
              v-for="(service, i) in services"
              :key="i"
            >
              <v-card-title class="justify-center">
                <v-icon large>{{service.icon}}</v-icon>
              </v-card-title>
              <v-card-text>
                <v-responsive class="text-center font-weight-bold">{{service.text}}</v-responsive>
              </v-card-text>
            </v-card>
          </v-row>
        </v-container>

        <div class="py-12"></div>
      </section>

      <section id="rooms" :class="sectionConfig.rooms.class">
        <div class="py-12"></div>
        <v-container class="text-center">
          <h2
            class="display-2 font-weight-bold ma-5 text-uppercase text-center text-white"
          >OUR ROOMS</h2>

          <v-responsive class="mx-auto mb-12" width="56">
            <v-divider class="mb-1 white"></v-divider>

            <v-divider class="white"></v-divider>
          </v-responsive>
        </v-container>

        <room-type-sliders></room-type-sliders>
      </section>

      <section id="whyPickUs" :class="sectionConfig.whyPickUs.class">
        <div class="py-12"></div>

        <v-container>
          <h2 class="display-2 font-weight-bold mb-3 text-uppercase text-center">WHY PICK US?</h2>

          <v-responsive class="mx-auto mb-12" width="56">
            <v-divider class="mb-1 black"></v-divider>

            <v-divider class="black"></v-divider>
          </v-responsive>

          <v-row justify="center">
            <v-col cols="6">
              <v-card flat width="100% " height="100%" class="ma-2 grey lighten-3">
                <v-list-item dense three-line v-for="(item, i) in whyPickUsItems" :key="i">
                  <v-list-item-icon left  >
                    <v-icon x-large color="black">mdi-circle-small</v-icon>
                  </v-list-item-icon>
                  <v-list-item-content>
                    <v-list-item-title class="title">{{ item.text | capitalizeFirstLetter }}</v-list-item-title>
                    <v-list-item-subtitle>{{ item.desc | capitalizeFirstLetter }}</v-list-item-subtitle>
                  </v-list-item-content>
                </v-list-item>
              </v-card>
            </v-col>
            <v-col cols="6">
              <v-card flat width="100% " height="100%" class="ma-2 black lighten-3">
                <google-map></google-map>
              </v-card>
            </v-col>
          </v-row>
        </v-container>

        <div class="py-12"></div>
      </section>

      <section id="contactUs" :class="sectionConfig.contactUs.class">
        <v-sheet id="contact" color="#333333" dark tag="section" tile>
          <div class="py-12"></div>

          <v-container>
            <h2 class="display-2 font-weight-bold mb-3 text-uppercase text-center">Contact Us</h2>

            <v-responsive class="mx-auto mb-12" width="56">
              <v-divider class="mb-1"></v-divider>

              <v-divider></v-divider>
            </v-responsive>

            <v-theme-provider light>
              <v-row>
                <v-col cols="12">
                  <v-text-field flat label="Name*" solo></v-text-field>
                </v-col>

                <v-col cols="12">
                  <v-text-field flat label="Email*" solo></v-text-field>
                </v-col>

                <v-col cols="12">
                  <v-text-field flat label="Subject*" solo></v-text-field>
                </v-col>

                <v-col cols="12">
                  <v-textarea flat label="Message*" solo></v-textarea>
                </v-col>

                <v-col class="mx-auto" cols="auto">
                  <v-btn color="accent" x-large>Submit</v-btn>
                </v-col>
              </v-row>
            </v-theme-provider>
          </v-container>

          <div class="py-12"></div>
        </v-sheet>
      </section>
    </v-content>

    <v-footer class="justify-center" color="#292929" height="100">
      <div
        class="title font-weight-light grey--text text--lighten-1 text-center"
      >&copy; {{ (new Date()).getFullYear() }} — Vuetify, LLC — Made with 💜 by John Leider</div>
    </v-footer>
  </v-app>
</template>

<script>
export default {
  data: () => ({
    appBarConfig: {
      color: "white",
      hideOnScroll: true,
      height: "100"
    },
    sectionConfig: {
      head: {
        class: "grey lighten-3"
      },
      aboutMe: {
        class: "grey lighten-3"
      },
      services: {
        class: "white lighten-3"
      },
      rooms: {
        class: "black lighten-3"
      },
      whyPickUs: {
        class: "grey lighten-3"
      },
      contactUs: {
        class: "grey lighten-3"
      }
    },
    navbarItems: [
      { text: "Management", name: "management" },
      { text: "Management", name: "management" },
      { text: "Management", name: "management" },
      { text: "Management", name: "management" },
      { text: "Management", name: "management" },
      { text: "Management", name: "management" }
    ],
    services: [
      { text: "Free Wifi", name: "freeWifi", icon: "mdi-wifi" },
      {
        text: "Laundry Services",
        name: "laundry",
        icon: "mdi-washing-machine"
      },
      { text: "Cleaning Services", name: "cleaning", icon: "mdi-broom" },
      { text: "Gym", name: "gym", icon: "mdi-dumbbell" },
      { text: "Free Parking", name: "parking", icon: "mdi-parking" },
      { text: "Fully Furnished", name: "furnished", icon: "mdi-table-chair" },
      {
        text: "Van Transportation",
        name: "transportation",
        icon: "mdi-van-passenger"
      },
      { text: "Kitchen", name: "kitchen", icon: "mdi-chef-hat" },
      { text: "Air Cond", name: "aircon", icon: "mdi-air-conditioner" },
      {
        text: "Heater",
        name: "heater",
        icon: "mdi-hot-tub"
      }
    ],
    whyPickUsItems: [
      { text: "Cheapest double room price at the town", desc: "" },
      { text: "Friendly Housemate", desc: "" },
      { text: "Well-Handled Management", desc: "" },
      {
        text: "5 mins to UTAR Campus",
        desc: "Driving car with only few minutes"
      },
      {
        text: "Convenience Mart Around",
        desc: "Walking distance to nearest 99 speedmart"
      },
      { text: "24 Hours Secure", desc: "CCTV operates at the corners." },
      { text: "Quiet Environment", desc: "" }
    ]
  }),
  created() {
    this.$vuetify.theme.dark = false;
  },
  mounted() {
    console.log("Component mounted.");
  }
};
</script>
