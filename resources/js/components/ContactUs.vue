<template>
  <v-sheet id="contact" color="#333333" dark tag="section" tile>
    <div class="py-5"></div>

    <v-container>
      <h2 class="display-2 font-weight-bold mb-3 text-uppercase text-center">Contact Us</h2>

      <v-responsive class="mx-auto mb-5" width="56">
        <v-divider class="mb-1 white"></v-divider>

        <v-divider class="white"></v-divider>
      </v-responsive>

      <v-theme-provider dark>
        <v-row justify="space-between">
          <v-col cols="12" md="4" order-md="1">
            <v-card class="mx-4" flat color="#333333">
              <v-list-item three-line>
                <v-list-item-content>
                  <v-list-item-subtitle class="d-inline-block headline ma-2">
                    <v-icon class="mr-2">mdi-google-maps</v-icon>
                    {{address}}
                  </v-list-item-subtitle>
                  <v-list-item-subtitle class="d-inline-block subtitle-1 ma-2">
                    <v-icon class="mr-2">mdi-email</v-icon>
                    {{email}}
                  </v-list-item-subtitle>
                  <v-list-item-subtitle class="d-inline-block subtitle-1 ma-2">
                    <v-icon class="mr-2">mdi-phone</v-icon>
                    {{phone}}
                  </v-list-item-subtitle>
                </v-list-item-content>
              </v-list-item>
            </v-card>
          </v-col>
          <v-col cols="12" md="4" align-self="auto" order-md="3">
            <v-card class="mx-4" flat color="#333333">
              <v-list-item three-line>
                <v-list-item-content>
                  <v-list-item-title class="d-inline-block headline ma-4">Opening Hours :</v-list-item-title>
                  <v-list-item-subtitle
                    class="d-inline-block subtitle-1 ma-2"
                    v-for="(item, i) in openingHours"
                    :key="i"
                  >
                    <v-icon class="mr-2">mdi-circle-small</v-icon>
                    {{item.day}} : {{item.hours}}
                  </v-list-item-subtitle>
                </v-list-item-content>
              </v-list-item>
            </v-card>
          </v-col>
          <v-col cols="12" md="4" order-md="2">
            <v-card class="mx-4" flat color="#333333">
              <v-list-item three-line>
                <v-list-item-content>
                  <v-list-item-title class="d-inline-block title text-center">Contact</v-list-item-title>
                  <v-list-item-subtitle>
                    <v-text-field
                      :maxlength="300"
                      v-model="data.contact"
                      light
                      background-color="white"
                    ></v-text-field>
                  </v-list-item-subtitle>
                  <v-list-item-subtitle class="text-center">
                    <v-btn block dark outlined>Submit</v-btn>
                  </v-list-item-subtitle>
                  <v-list-item-subtitle class="text-center ma-2">
                    <v-btn
                      class="ma-2"
                      icon
                      v-for="(item, i) in socialLinks"
                      :key="i"
                      :href="item.link"
                      target="_blank"
                    >
                      <v-icon>{{item.icon}}</v-icon>
                    </v-btn>
                  </v-list-item-subtitle>
                </v-list-item-content>
              </v-list-item>
            </v-card>
          </v-col>
        </v-row>
      </v-theme-provider>
    </v-container>
  </v-sheet>
</template>

<script>
export default {
  props: {
    roomTypes: {
      type: Array,
      default: () => []
    }
  },
  data: () => ({
    openingHours: [
      { day: "Monday", hours: "9am-6pm" },
      { day: "Tuesday", hours: "9am-6pm" },
      { day: "Wednesday", hours: "9am-6pm" },
      { day: "Thursday", hours: "9am-6pm" },
      { day: "Friday", hours: "9am-6pm" },
      { day: "Saturday", hours: "Closed" },
      { day: "Sunday", hours: "Closed" }
    ],
    socialLinks: [
      { icon: "mdi-facebook", link: "#" },
      { icon: "mdi-instagram", link: "#" },
      { icon: "mdi-twitter", link: "#" }
    ],
    address:
      "47G, Pusat Perdagangan Jalan Kampar Barat 1, 31900 Kampar, Perak.",
    email: "antz.customerservice@gmail.com",
    phone: "010-2898012",

    data: {
      contact: ""
    }
  }),
  methods: {}
};
</script>