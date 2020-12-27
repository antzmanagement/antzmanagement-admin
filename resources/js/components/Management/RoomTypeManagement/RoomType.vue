
<script>
import { mapActions } from "vuex";
import {
  notEmptyLength,
  objectRemoveEmptyValue,
  _,
} from "../../../common/common-function";
export default {
  data: () => ({
    editMode: false,
    data: {},
    _: _,
    deleteButtonConfig: {
      buttonStyle: {
        block: false,
        color: "error",
        class: "m-3",
        text: "Delete",
        icon: "mdi-trash-can-outline",
      },
    },
  }),

  computed: {
    isLoading() {
      return this.$store.getters.isLoading;
    },
  },
  created() {
    this.$Progress.start();
    this.showLoadingAction();
    this.getRoomTypeAction({ uid: this.$route.params.uid })
      .then((data) => {
        if (
          _.isPlainObject(data.data) &&
          notEmptyLength(objectRemoveEmptyValue(data.data))
        ) {
          this.data = data.data;
        } else {
          this.data = {};
        }
        this.$Progress.finish();
        this.endLoadingAction();
      })
      .catch((error) => {
        Toast.fire({
          icon: "warning",
          title: "Fail to retrieve the Room Type!!!!! ",
        });
        this.$Progress.finish();
        this.endLoadingAction();
      });
  },

  methods: {
    ...mapActions({
      getRoomTypeAction: "getRoomType",
      showLoadingAction: "showLoadingAction",
      endLoadingAction: "endLoadingAction",
      deleteRoomTypeAction: "deleteRoomType",
    }),
    showService($data) {
      this.$router.push("/service/" + $data.uid);
    },
    refreshPage() {
      location.reload();
    },
    deleteRoomType($uid) {
      this.$Progress.start();
      this.showLoadingAction();
      this.deleteRoomTypeAction({ uid: $uid })
        .then((data) => {
          Toast.fire({
            icon: "success",
            title: "Successful Deleted. ",
          });
          this.$Progress.finish();
          this.endLoadingAction();
            this.$router.push("/roomtypes");
        })
        .catch((error) => {
          Toast.fire({
            icon: "warning",
            title: "Fail to delete the room type!!!!! ",
          });
          this.$Progress.finish();
          this.endLoadingAction();
        });
    },
  },
};
</script>

<template>
  <v-app>
    <navbar></navbar>
    <v-content :class="helpers.managementStyles().backgroundClass">
      <v-container>
        <loading></loading>
        <v-card
          class="ma-2"
          :color="helpers.managementStyles().formCardColor"
          raised
        >
          <v-card-title
            class="ma-2"
            :class="helpers.managementStyles().titleClass"
            >RoomType - {{ data.uid }}</v-card-title
          >
          <v-divider
            class="mx-3"
            :color="helpers.managementStyles().dividerColor"
          ></v-divider>
          <v-container>
            <v-row justify="start" align="center" class="pa-2">
              <v-col cols="12" md="4">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Name</label>
                  <div class="form-control-plaintext">
                    <h4>{{ data.name }}</h4>
                  </div>
                </div>
              </v-col>
              <v-col cols="12" md="4">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Price</label>
                  <div class="form-control-plaintext">
                    <h4>RM {{ data.price | toDouble }}</h4>
                  </div>
                </div>
              </v-col>
              <v-col cols="12" md="4">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Area</label>
                  <div class="form-control-plaintext">
                    <h4>{{ data.area | toDouble }}</h4>
                  </div>
                </div>
              </v-col>
            </v-row>

            <v-divider
              class="mx-3"
              :color="helpers.managementStyles().dividerColor"
              v-if="_.isArray(data.services) && !_.isEmpty(data.services)"
            ></v-divider>
            <v-row
              justify="start"
              align="center"
              class="pa-2"
              v-if="_.isArray(data.services) && !_.isEmpty(data.services)"
            >
              <v-col cols="12">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Room Type Services</label>
                  <div class="form-control-plaintext">
                    <v-chip
                      class="ma-2"
                      v-for="service in data.services"
                      :key="service.uid"
                      @click="showService(service)"
                    >
                      <h4 class="text-center ma-2">{{ service.text }}</h4>
                    </v-chip>
                  </div>
                </div>
              </v-col>
            </v-row>
            <v-divider
              class="mx-3"
              :color="helpers.managementStyles().dividerColor"
            ></v-divider>
            <v-row class="pa-2" justify="end" align="center">
              <v-col cols="auto">
                <room-type-form
                  :editMode="true"
                  :uid="this.$route.params.uid"
                  @updated="refreshPage()"
                >
                  <v-btn color="success" class="ma-3"
                    ><v-icon left>mdi-pencil</v-icon>Edit</v-btn
                  >
                </room-type-form>
              </v-col>
              <v-col cols="auto">
                <confirm-dialog
                  :activatorStyle="deleteButtonConfig.buttonStyle"
                  @confirmed="
                    $event ? deleteRoomType(data.uid) : null
                  "
                ></confirm-dialog>
              </v-col>
            </v-row>
          </v-container>
        </v-card>
      </v-container>
    </v-content>
  </v-app>
</template>
