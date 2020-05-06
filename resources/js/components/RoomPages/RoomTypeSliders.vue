<template>
  <div>
    <v-carousel cycle show-arrows-on-hover height="90%" class="black lighten-3">
      <v-carousel-item v-for="(roomType, i) in roomTypes" :key="i" class="pb-12">
        <v-sheet class="px-12 black lighten-3" height="100%" v-show="$vuetify.breakpoint.mdAndUp">
          <v-row class="fill-height" align="center" justify="center">
            <v-card class="mx-5 black">
              <v-list-item three-line>
                <v-list-item-content>
                  <v-list-item-title
                    class="text-justify d-inline-block display-2 mx-12 my-10"
                  >{{roomType.name | capitalizeFirstLetter}} - RM {{roomType.price | toDouble}}</v-list-item-title>
                  <!-- <v-list-item-subtitle class="text-justify d-inline-block title mx-12">{{roomType.desc}}</v-list-item-subtitle> -->
                  <v-list-item-subtitle>
                    <v-responsive class="text-justify title mx-12">{{roomType.desc}}</v-responsive>
                  </v-list-item-subtitle>
                  <v-list-item-subtitle class="mx-10 my-8">
                    <v-btn text :to="{ name : 'roompages'}">View More</v-btn>
                  </v-list-item-subtitle>
                </v-list-item-content>

                <v-list-item-avatar tile size="400" color="grey" class="mx-8 my-10">
                  <v-img contain :aspect-ratio="4/3" :src="roomType.images[0].imgpath"></v-img>
                </v-list-item-avatar>
              </v-list-item>
            </v-card>
          </v-row>
        </v-sheet>

        <v-card
          class="mx-auto black"
          width="100%"
          height="100%"
          v-show="$vuetify.breakpoint.smAndDown"
        >
          <v-img
            class="white--text align-end"
            height="200px"
            contain
            :aspect-ratio="4/3"
            :src="roomType.images[0].imgpath"
          ></v-img>

          <v-card-subtitle
            class="ma-2 headline"
          >{{roomType.name | capitalizeFirstLetter}} - RM {{roomType.price | toDouble}}</v-card-subtitle>

          <v-card-text class="ma-2">{{roomType.desc}}</v-card-text>

          <v-card-actions class="ma-2">
            <v-btn text :to="{ name : 'roompages'}">View More</v-btn>
          </v-card-actions>
        </v-card>
      </v-carousel-item>
    </v-carousel>
  </div>
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
    })
  }
};
</script>