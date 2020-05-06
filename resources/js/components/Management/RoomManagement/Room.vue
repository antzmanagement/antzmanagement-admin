<template>
  <v-app>
    <navbar></navbar>
    <v-content :class="helpers.managementStyles().backgroundClass">
      <v-container>
        <loading></loading>
        <v-card class="ma-2" :color="helpers.managementStyles().formCardColor" raised>
          <v-card-title
            class="ma-2"
            :class="helpers.managementStyles().titleClass"
          >Room - {{data.uid}}</v-card-title>
          <v-divider class="mx-3" :color="helpers.managementStyles().dividerColor"></v-divider>
          <v-container>
            <v-row justify="start" align="center" class="pa-2">
              <v-col cols="12">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">RoomType</label>
                  <div class="form-control-plaintext">
                    <v-chip class="ma-2" v-for="roomType in data.room_types" :key="roomType.uid">
                      <h4 class="text-center ma-2">{{roomType.name | capitalizeFirstLetter }}</h4>
                    </v-chip>
                  </div>
                </div>
              </v-col>
            </v-row>

            <v-divider class="mx-3" color="#ffffff"></v-divider>
            <v-row justify="start" align="center" class="pa-2">
              <v-col cols="12" md="6">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Name</label>
                  <div class="form-control-plaintext">
                    <h4>{{data.name}}</h4>
                  </div>
                </div>
              </v-col>
              <v-col cols="12" md="6">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Price</label>
                  <div class="form-control-plaintext">
                    <h4>RM {{data.price}}</h4>
                  </div>
                </div>
              </v-col>

              <v-col cols="12">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Address</label>
                  <div class="form-control-plaintext">
                    <h4>{{data.address}}</h4>
                  </div>
                </div>
              </v-col>

              <v-col cols="12" md="4">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Postcode</label>
                  <div class="form-control-plaintext">
                    <h4>{{data.postcode}}</h4>
                  </div>
                </div>
              </v-col>
              <v-col cols="12" md="4">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">State</label>
                  <div class="form-control-plaintext">
                    <h4>{{data.state}}</h4>
                  </div>
                </div>
              </v-col>
              <v-col cols="12" md="4">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">City</label>
                  <div class="form-control-plaintext">
                    <h4>{{data.city}}</h4>
                  </div>
                </div>
              </v-col>
              <v-col cols="12" md="4">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Country</label>
                  <div class="form-control-plaintext">
                    <h4>{{data.country}}</h4>
                  </div>
                </div>
              </v-col>
            </v-row>

            <v-divider class="mx-3" color="#ffffff"></v-divider>
            <v-row class="pa-2" justify="end" align="center">
              <v-col cols="auto">
                <room-form
                  :editMode="true"
                  :buttonStyle="editButtonStyle"
                  :uid="this.$route.params.uid"
                  @updated="refreshPage()"
                ></room-form>
              </v-col>
              <v-col cols="auto">
                <confirm-dialog
                  :activatorStyle="deleteButtonConfig.buttonStyle"
                  @confirmed="deleteRoom($event,data.uid)"
                ></confirm-dialog>
              </v-col>
            </v-row>
          </v-container>
        </v-card>
      </v-container>
    </v-content>
  </v-app>
</template>

<script>
import { mapActions } from "vuex";
export default {
  data: () => ({
    editButtonStyle: {
      block: false,
      color: "success",
      class: "m-3",
      text: "Edit",
      icon: "mdi-pencil"
    },
    deleteButtonConfig: {
      buttonStyle: {
        block: false,
        color: "error",
        class: "m-3",
        text: "Delete",
        icon: "mdi-trash-can-outline"
      }
    },
    data: new Form({
      name: "",
      price: "",
      address: "",
      postcode: "",
      state: "",
      city: "",
      country: "",
      //Original is roomTypes but Laravel auto convert carmelCase to snake_case
      room_types: []
    })
  }),

  computed: {
    isLoading() {
      return this.$store.getters.isLoading;
    }
  },
  created() {
    this.$Progress.start();
    this.showLoadingAction();
    this.getRoomAction({ uid: this.$route.params.uid })
      .then(data => {
        this.data = data.data;
        console.log(this.data);
        this.$Progress.finish();
        this.endLoadingAction();
      })
      .catch(error => {
        Toast.fire({
          icon: "warning",
          title: "Fail to retrieve the room!!!!! "
        });
        this.$Progress.finish();
        this.endLoadingAction();
      });
  },

  methods: {
    ...mapActions({
      getRoomAction: "getRoom",
      deleteRoomAction: "deleteRoom",
      showLoadingAction: "showLoadingAction",
      endLoadingAction: "endLoadingAction"
    }),
    deleteRoom($isConfirmed, $uid) {
      if ($isConfirmed) {
        this.$Progress.start();
        this.showLoadingAction();
        this.deleteRoomAction({ uid: $uid })
          .then(data => {
            Toast.fire({
              icon: "success",
              title: "Successful Deleted. "
            });
            this.$Progress.finish();
            this.endLoadingAction();
            this.$router.push("/rooms");
          })
          .catch(error => {
            Toast.fire({
              icon: "warning",
              title: "Fail to delete the room!!!!! "
            });
            this.$Progress.finish();
            this.endLoadingAction();
          });
      }
    },
    refreshPage() {
      location.reload();
    }
  }
};
</script>