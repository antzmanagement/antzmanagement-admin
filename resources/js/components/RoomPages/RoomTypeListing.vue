<template>
  <v-container>
    <v-row justify="center" no-gutters>
      <v-col cols="12">
        <div class="display-2 ma-5 text-center">Our Rooms</div>
      </v-col>
    </v-row>
    <v-row justify="center" no-gutters>
      <v-col cols="5">
        <v-divider></v-divider>
      </v-col>
    </v-row>
    <v-row justify="center" class="ma-4" v-for="(roomType,x) in roomTypes" :key="x">
      <v-col cols="12" md="8">
        <!-- <v-btn @click="test()">TEst</v-btn> -->
        <v-card class="mx-auto" raised>
          <v-container>
            <v-row justify="center" no-gutters>
              <v-col cols="12">
                <v-carousel
                  cycle
                  show-arrows-on-hover
                  hide-delimiters
                  v-model="roomType.slide"
                  height="300px"
                  ref="slider"
                >
                  <v-carousel-item v-for="(img, y) in roomType.images" :key="y" class="pb-12">
                    <v-img contain height="100%" width="100%"  :src="img.imgpath"></v-img>
                  </v-carousel-item>
                </v-carousel>
              </v-col>
            </v-row>

            <v-row no-gutters class="ma-2" v-show="$vuetify.breakpoint.mdAndUp">
              <v-col
                cols="1"
                v-for="(img, z) in roomType.images"
                :key="z"
                class="grey lighten-1 ma-2 d-flex justify-center align-center"
              >
                <v-hover v-slot:default="{ hover }">
                  <v-card
                    class
                    img
                    flat
                    :width="hover ? '90%' : '100%'"
                    :height="hover ? '90%' : '100%'"
                    @click="roomType.slide = z"
                  >
                    <v-img :aspect-ratio="4/3" :src="img.imgpath"></v-img>
                  </v-card>
                </v-hover>
              </v-col>
            </v-row>
            <v-divider></v-divider>
            <v-row no-gutters>
              <v-col cols="12">
                <v-expansion-panels flat hover>
                  <v-expansion-panel>
                    <v-expansion-panel-header
                      class="display-1 ma-2"
                    >{{roomType.name | capitalizeFirstLetter}} - RM {{roomType.price | toDouble}}</v-expansion-panel-header>
                    <v-expansion-panel-content>
                      <v-container fluid>
                        <v-row no-gutters>
                          <v-col cols="12">
                            <div class="subtitle-2">{{roomType.desc}}</div>
                          </v-col>
                        </v-row>
                        <v-divider></v-divider>
                        <v-row no-gutters>
                          <v-col cols="12">
                            <p class="title">Properties :</p>
                          </v-col>
                        </v-row>
                        <v-row no-gutters>
                          <v-col
                            cols="auto"
                            class="mr-4"
                            v-for="(property, i) in roomType.properties"
                            :key="i"
                          >
                            <p
                              class="subtitle-2"
                            >{{property.text | capitalizeFirstLetter}} : {{property.pivot.qty}}</p>
                          </v-col>
                        </v-row>
                        <v-divider></v-divider>
                        <v-row no-gutters>
                          <v-col cols="12">
                            <p class="title">Services Included :</p>
                          </v-col>
                        </v-row>
                        <v-row no-gutters>
                          <v-col
                            cols="auto"
                            class="mr-4"
                            v-for="(service, i) in roomType.services"
                            :key="i"
                          >
                            <v-card flat width="100px" class="white lighten-3">
                              <v-card-title class="justify-center">
                                <v-icon>{{service.icon}}</v-icon>
                              </v-card-title>
                              <v-card-text>
                                <v-responsive
                                  class="text-center subtitle-2 font-weight-bold"
                                >{{service.text}}</v-responsive>
                              </v-card-text>
                            </v-card>
                          </v-col>
                        </v-row>
                        <v-divider></v-divider>
                        <v-row no-gutters>
                          <v-col cols="12">
                            <p class="title">Terms & Conditions :</p>
                          </v-col>
                        </v-row>
                        <v-row no-gutters>
                          <v-col cols="12">
                            <div class="subtitle-2">{{roomType.desc}}</div>
                          </v-col>
                        </v-row>
                      </v-container>
                    </v-expansion-panel-content>
                  </v-expansion-panel>
                </v-expansion-panels>
              </v-col>
            </v-row>

            <v-row>
              <v-col cols="12"></v-col>
            </v-row>
          </v-container>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import { mapActions } from "vuex";
export default {
  props: {
    roomTypes: {
      type: Array,
      default: () => []
    }
  },
  data: () => ({}),
  created() {
    this.$vuetify.theme.dark = false;
    this.showLoadingAction();
    this.getPublicRoomTypesAction({ pageNumber: -1, pageSize: -1 }).then(
      data => {
        this.roomTypes = data.data;
        this.endLoadingAction();
      }
    );
  },
  methods: {
    ...mapActions({
      getPublicRoomTypesAction: "getPublicRoomTypes",
      showLoadingAction: "showLoadingAction",
      endLoadingAction: "endLoadingAction"
    }),
    test() {
      console.log(this.$refs.slider);
    }
  }
};
</script>