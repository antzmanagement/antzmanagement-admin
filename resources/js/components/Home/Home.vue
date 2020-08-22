<template>
  <v-app>
    <home-navbar></home-navbar>
    <v-content>
      <section id="head" :class="sectionConfig.head.class">
        <v-row no-gutters>
          <v-img
            :min-height="'calc(100vh - ' + $vuetify.application.top + 'px)'"
            src="https://res.cloudinary.com/ipoh-drum/image/upload/v1587403879/hostel-3_yztooy.jpg"
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
                      :class="[$vuetify.breakpoint.smAndDown ? 'display-3': 'display-5']"
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
        <div class="py-5"></div>

        <v-container class="text-center">
          <h2 class="display-2 font-weight-bold mb-3">ABOUT US</h2>

          <!-- <v-file-input show-size v-model="file" enctype="multipart/form-data" label="File input"></v-file-input>
          <v-btn @click="uploadFile()">Submit</v-btn>-->
          <v-responsive class="mx-auto mb-12" width="56">
            <v-divider class="mb-1 black"></v-divider>

            <v-divider class="black"></v-divider>
          </v-responsive>

          <v-responsive
            class="mx-auto title font-weight-light mb-8"
            max-width="720"
          >Vuetify is the #1 component library for Vue.js and has been in active development since 2016. The goal of the project is to provide users with everything that is needed to build rich and engaging web applications using the Material Design specification. It accomplishes that with a consistent update cycle, Long-term Support (LTS) for previous versions, responsive community engagement, a vast ecosystem of resources and a dedication to quality components.</v-responsive>

          <v-row justify="center">
            <v-avatar
              class="elevation-12 ma-12"
              size="128"
              v-for="(aboutPic, i) in aboutPics"
              :key="i"
            >
              <v-img :src="aboutPic.imgpath"></v-img>
            </v-avatar>
          </v-row>

          <div></div>

          <v-btn color="grey" @click="$vuetify.goTo('#contact')" outlined large>
            <span class="grey--text text--darken-1 font-weight-bold">Contact Us Now !</span>
          </v-btn>
        </v-container>

        <div class="py-5"></div>
      </section>

      <section id="services" :class="sectionConfig.services.class">
        <div class="py-5"></div>

        <v-container>
          <h2 class="display-2 font-weight-bold mb-3 text-uppercase text-center">OUR SERVICES</h2>

          <v-responsive class="mx-auto mb-5" width="56">
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

        <div class="py-5"></div>
      </section>

      <section id="rooms" :class="sectionConfig.rooms.class">
        <div class="py-5"></div>
        <v-container class="text-center">
          <h2
            class="display-2 font-weight-bold ma-5 text-uppercase text-center text-white"
          >OUR ROOMS</h2>

          <v-responsive class="mx-auto mb-5" width="56">
            <v-divider class="mb-1 white"></v-divider>

            <v-divider class="white"></v-divider>
          </v-responsive>
        </v-container>
        <room-type-sliders></room-type-sliders>
        <div class="py-5"></div>
      </section>

      <section id="whyPickUs" :class="sectionConfig.whyPickUs.class">
        <div class="py-5"></div>

        <v-container>
          <h2 class="display-2 font-weight-bold mb-3 text-uppercase text-center">WHY PICK US?</h2>

          <v-responsive class="mx-auto mb-5" width="56">
            <v-divider class="mb-1 black"></v-divider>

            <v-divider class="black"></v-divider>
          </v-responsive>

          <v-row justify="center">
            <v-col cols="12" md="6">
              <v-card flat width="100% " height="100%" class="ma-2 grey lighten-3">
                <v-list-item dense three-line v-for="(item, i) in whyPickUsItems" :key="i">
                  <v-list-item-icon left>
                    <v-icon x-large color="black">mdi-circle-small</v-icon>
                  </v-list-item-icon>
                  <v-list-item-content>
                    <v-list-item-title class="title">{{ item.text | capitalizeFirstLetter }}</v-list-item-title>
                    <v-list-item-subtitle>{{ item.desc | capitalizeFirstLetter }}</v-list-item-subtitle>
                  </v-list-item-content>
                </v-list-item>
              </v-card>
            </v-col>
            <v-col cols="12" md="6">
              <v-card
                flat
                width="100%"
                :height="$vuetify.breakpoint.mdAndUp ?'100%' : '500px' "
                class="ma-2 black lighten-3"
              >
                <google-map></google-map>
              </v-card>
            </v-col>
          </v-row>
        </v-container>

        <div class="py-5"></div>
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
  data: () => ({
    file: "",
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
    aboutPics: [
      {
        imgpath:
          "https://res.cloudinary.com/ipoh-drum/image/upload/v1587403963/CP-B1112-2_olvbdv.jpg"
      },
      {
        imgpath:
          "https://res.cloudinary.com/ipoh-drum/image/upload/v1587403962/maxresdefault_ra18v3.jpg"
      },
      {
        imgpath:
          "https://res.cloudinary.com/ipoh-drum/image/upload/v1587278991/Hostel_Dormitory_kp3ulz.jpg"
      }
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
    this.$vuetify.goTo(0);
  },
  mounted() {
    console.log("Component mounted.");
  },
  methods: {
    uploadFile() {
      console.log(this.file);
      axios
        .post(
          "/upload",
          { file: "" },
          {
            headers: {
              "Content-Type": "multipart/form-data"
            }
          }
        )
        .then(data => {
          console.log(data);
        })
        .catch(error => {
          console.log(error.response);
        });
    }
  }
};
</script>
