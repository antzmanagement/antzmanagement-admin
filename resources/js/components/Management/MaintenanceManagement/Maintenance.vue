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
          >Maintenance - {{data.uid}}</v-card-title>
          <v-divider class="mx-3" :color="helpers.managementStyles().dividerColor"></v-divider>
          <v-container>
            <v-row justify="start" align="center" class="pa-2">
              <v-col cols="12">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Room</label>
                  <div class="form-control-plaintext">
                    <v-chip class="ma-2" @click="showRoom(data.room)">
                      <h4 class="text-center ma-2">{{data.room.name }}</h4>
                    </v-chip>
                  </div>
                </div>
              </v-col>
            </v-row>

            <v-divider class="mx-3" :color="helpers.managementStyles().dividerColor"></v-divider>

            <v-row justify="start" align="center" class="pa-2">
              <v-col cols="12">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Property</label>
                  <div class="form-control-plaintext">
                    <v-chip class="ma-2">
                      <h4 class="text-center ma-2">{{data.property.name | capitalizeFirstLetter }}</h4>
                    </v-chip>
                  </div>
                </div>
              </v-col>
            </v-row>

            <v-divider class="mx-3" :color="helpers.managementStyles().dividerColor"></v-divider>
            <v-row justify="start" align="center" class="pa-2">
              <v-col cols="12" md="12">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Price</label>
                  <div class="form-control-plaintext">
                    <h4>RM {{data.price | toDouble}}</h4>
                  </div>
                </div>
              </v-col>
              <v-col cols="12" md="12">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Remark</label>
                  <v-card tite class="pa-5 my-2">
                    <pre>
                      <div class="form-control-plaintext">{{data.remark}}</div>
                      </pre>
                  </v-card>
                </div>
              </v-col>
            </v-row>

            <v-divider class="mx-3" :color="helpers.managementStyles().dividerColor"></v-divider>
            <v-row class="pa-2" justify="end" align="center">
              <v-col cols="auto">
                <maintenance-form
                  :editMode="true"
                  :buttonStyle="editButtonStyle"
                  :uid="this.$route.params.uid"
                  @updated="refreshPage()"
                ></maintenance-form>
              </v-col>
              <v-col cols="auto">
                <confirm-dialog
                  :activatorStyle="deleteButtonConfig.buttonStyle"
                  @confirmed="deleteMaintenance($event,data.uid)"
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
      remark: "",
      price: "",
      room: [],
      property: []
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
    this.getMaintenanceAction({ uid: this.$route.params.uid })
      .then(data => {
        this.data = data.data;
        this.$Progress.finish();
        this.endLoadingAction();
      })
      .catch(error => {
        Toast.fire({
          icon: "warning",
          title: "Fail to retrieve the maintenance!!!!! "
        });
        this.$Progress.finish();
        this.endLoadingAction();
      });
  },

  methods: {
    ...mapActions({
      getMaintenanceAction: "getMaintenance",
      deleteMaintenanceAction: "deleteMaintenance",
      showLoadingAction: "showLoadingAction",
      endLoadingAction: "endLoadingAction"
    }),
    showRoom($data) {
      this.$router.push("/room/" + $data.uid);
    },
    deleteMaintenance($isConfirmed, $uid) {
      if ($isConfirmed) {
        this.$Progress.start();
        this.showLoadingAction();
        this.deleteMaintenanceAction({ uid: $uid })
          .then(data => {
            Toast.fire({
              icon: "success",
              title: "Successful Deleted. "
            });
            this.$Progress.finish();
            this.endLoadingAction();
            this.$router.push("/maintenances");
          })
          .catch(error => {
            Toast.fire({
              icon: "warning",
              title: "Fail to delete the maintenance!!!!! "
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