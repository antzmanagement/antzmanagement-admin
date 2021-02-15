

<script>
import { mapActions } from "vuex";
export default {
  data: () => ({
    editButtonStyle: {
      block: false,
      color: "success",
      class: "m-3",
      text: "Edit",
      icon: "mdi-pencil",
    },
    deleteButtonConfig: {
      activatorStyle: {
        block: false,
        color: "error",
        class: "m-3",
        text: "Delete",
        icon: "mdi-trash-can-outline",
      },
    },
    data: new Form({
      uid: "",
      name: "",
      icno: "",
      tel1: "",
      email: "",
    }),
  }),

  computed: {
    isLoading() {
      return this.$store.getters.isLoading;
    },
  },
  created() {
    this.$Progress.start();
    this.showLoadingAction();
    this.getOwnerAction({ uid: this.$route.params.uid })
      .then((data) => {
        this.data = data.data;
        this.$Progress.finish();
        this.endLoadingAction();
      })
      .catch((error) => {
        Toast.fire({
          icon: "warning",
          title: "Fail to retrieve the owner!!!!! ",
        });
        this.$Progress.finish();
        this.endLoadingAction();
      });
  },

  methods: {
    ...mapActions({
      getOwnerAction: "getOwner",
      deleteOwnerAction: "deleteOwner",
      showLoadingAction: "showLoadingAction",
      endLoadingAction: "endLoadingAction",
    }),
    showRoom($data) {
      this.$router.push("/room/" + $data.uid);
    },
    deleteOwner($isConfirmed, $uid) {
      if ($isConfirmed) {
        this.$Progress.start();
        this.showLoadingAction();
        this.deleteOwnerAction({ uid: $uid })
          .then((data) => {
            Toast.fire({
              icon: "success",
              title: "Successful Deleted. ",
            });
            this.$Progress.finish();
            this.endLoadingAction();
            this.$router.push("/owners");
          })
          .catch((error) => {
            Toast.fire({
              icon: "warning",
              title: "Fail to delete the owner!!!!! ",
            });
            this.$Progress.finish();
            this.endLoadingAction();
          });
      }
    },
    refreshPage() {
      location.reload();
    },
  },
};
</script>

<template>
  <v-app>
    <navbar></navbar>
    <v-content :class="helpers.managementStyles().backgroundClass">
      <v-container class="pa-5">
        <loading></loading>
        <v-card
          class="ma-2"
          :color="helpers.managementStyles().formCardColor"
          raised
        >
          <v-card-title
            class="ma-2"
            :class="helpers.managementStyles().titleClass"
            >Owner - {{ data.uid }}</v-card-title
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
                  <label class="form-label mb-0">IC-NO</label>
                  <div class="form-control-plaintext">
                    <h4>{{ data.icno }}</h4>
                  </div>
                </div>
              </v-col>
              <v-col cols="12" md="4">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Phone</label>
                  <div class="form-control-plaintext">
                    <h4>{{ data.tel1 }}</h4>
                  </div>
                </div>
              </v-col>
              <v-col cols="12" md="4">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Email</label>
                  <div class="form-control-plaintext">
                    <h4>{{ data.email }}</h4>
                  </div>
                </div>
              </v-col>
              <v-col cols="12" md="4">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Occupation</label>
                  <div class="form-control-plaintext">
                    <h4>{{ data.occupation }}</h4>
                  </div>
                </div>
              </v-col>
              <v-col cols="12" md="12">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Address</label>
                  <div class="form-control-plaintext">
                    <h4>{{ data.address }}</h4>
                  </div>
                </div>
              </v-col>
              <v-col cols="12" md="4">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Postcode</label>
                  <div class="form-control-plaintext">
                    <h4>{{ data.postcode }}</h4>
                  </div>
                </div>
              </v-col>
              <v-col cols="12" md="4">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">City</label>
                  <div class="form-control-plaintext">
                    <h4>{{ data.city }}</h4>
                  </div>
                </div>
              </v-col>
              <v-col cols="12" md="4">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">State</label>
                  <div class="form-control-plaintext">
                    <h4>{{ data.state }}</h4>
                  </div>
                </div>
              </v-col>
            </v-row>

            <v-divider
              class="mx-3"
              :color="helpers.managementStyles().dividerColor"
            ></v-divider>

            <v-row justify="start" align="center" class="pa-2">
              <v-col cols="12">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Room</label>
                  <div class="form-control-plaintext">
                    <v-chip
                      v-for="room in data.ownrooms"
                      :key="room.uid"
                      class="mr-2 my-2"
                      @click="showRoom(room)"
                    >
                      <h4 class="text-center ma-2">
                        {{ room.name | capitalizeFirstLetter }}
                      </h4>
                    </v-chip>
                  </div>
                </div>
              </v-col>
            </v-row>

            <v-row>
              <v-col col="12">
                <v-divider
                  class="mx-3"
                  :color="helpers.managementStyles().dividerColor"
                ></v-divider>
              </v-col>
            </v-row>
            <v-row class="pa-2" justify="end" align="center">
              <v-col cols="auto">
                <change-password-dialog
                  :uid="this.$route.params.uid"
                  @updated="refreshPage()"
                ></change-password-dialog>
              </v-col>
              <v-col cols="auto">
                <owner-form
                  :editMode="true"
                  :buttonStyle="editButtonStyle"
                  :uid="this.$route.params.uid"
                  @updated="refreshPage()"
                ></owner-form>
              </v-col>
              <v-col cols="auto">
                <confirm-dialog
                  :activatorStyle="deleteButtonConfig.activatorStyle"
                  @confirmed="deleteOwner($event, data.uid)"
                ></confirm-dialog>
              </v-col>
            </v-row>
          </v-container>
        </v-card>
      </v-container>
    </v-content>
  </v-app>
</template>