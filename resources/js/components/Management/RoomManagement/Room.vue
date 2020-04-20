<template>
  <v-app>
    <v-container>
      <navbar></navbar>
      <v-content>
        <loading></loading>
        <v-card class="mx-2" outlined color="#1b262c">
          <v-card-title class="headline">Room - {{data.uid}}</v-card-title>
          <v-divider class="mx-3" color="#ffffff"></v-divider>
          <v-container>
            <v-row>
              <v-col cols="12" class="m-3">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">RoomType</label>
                  <div class="form-control-plaintext ">
                    <v-chip
                      class="ma-2"
                      v-for="roomType in data.room_types"
                      :key="roomType.uid"
                    ><h4 class="text-center ma-2">{{roomType.name | capitalizeFirstLetter }}</h4></v-chip>
                  </div>
                </div>
              </v-col>
            </v-row>
            
          <v-divider class="mx-3" color="#ffffff"></v-divider>
            <v-row>
              <v-col cols="6" class="m-3">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Name</label>
                  <div class="form-control-plaintext">
                    <h4>{{data.name}}</h4>
                  </div>
                </div>
              </v-col>
              <v-col cols="4" class="m-3">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Price</label>
                  <div class="form-control-plaintext">
                    <h4>{{data.price}}</h4>
                  </div>
                </div>
              </v-col>
            </v-row>
            <v-row>
              <v-col cols="12" class="m-3">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Address</label>
                  <div class="form-control-plaintext">
                    <h4>{{data.address}}</h4>
                  </div>
                </div>
              </v-col>
            </v-row>
            <v-row>
              <v-col cols="4" class="m-3">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Postcode</label>
                  <div class="form-control-plaintext">
                    <h4>{{data.postcode}}</h4>
                  </div>
                </div>
              </v-col>
              <v-col cols="4" class="m-3">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">State</label>
                  <div class="form-control-plaintext">
                    <h4>{{data.state}}</h4>
                  </div>
                </div>
              </v-col>
              <v-col cols="4" class="m-3">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">City</label>
                  <div class="form-control-plaintext">
                    <h4>{{data.city}}</h4>
                  </div>
                </div>
              </v-col>
              <v-col cols="4" class="m-3">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Country</label>
                  <div class="form-control-plaintext">
                    <h4>{{data.country}}</h4>
                  </div>
                </div>
              </v-col>
            </v-row>
            <v-row>
              <v-col col="12">
                <v-divider class="mx-3" color="#ffffff"></v-divider>
              </v-col>
            </v-row>
            <v-row>
              <v-spacer></v-spacer>
              <room-form
                :editMode="true"
                :buttonStyle="editButtonStyle"
                :uid="this.$route.params.uid"
                @updated="refreshPage()"
              ></room-form>
              <confirm-dialog
                :activatorStyle="deleteButtonConfig.buttonStyle"
                @confirmed="deleteRoom($event,data.uid)"
              ></confirm-dialog>
            </v-row>
          </v-container>
        </v-card>
      </v-content>
    </v-container>
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
    this.$vuetify.theme.dark = true;

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