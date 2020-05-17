<template>
  <v-app>
    <navbar></navbar>
    <v-content :class="helpers.managementStyles().backgroundClass">
      <v-container class="pa-5">
        <loading></loading>
        <v-card class="ma-2" :color="helpers.managementStyles().formCardColor" raised>
          <v-card-title
            class="ma-2"
            :class="helpers.managementStyles().titleClass"
          >Tenant - {{data.uid}}</v-card-title>
          <v-divider class="mx-3" :color="helpers.managementStyles().dividerColor"></v-divider>
          <v-container>
            <v-row justify="start" align="center" class="pa-2">
              <v-col cols="12" md="4">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Name</label>
                  <div class="form-control-plaintext">
                    <h4>{{data.name}}</h4>
                  </div>
                </div>
              </v-col>
              <v-col cols="12" md="4">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">IC-NO</label>
                  <div class="form-control-plaintext">
                    <h4>{{data.icno}}</h4>
                  </div>
                </div>
              </v-col>
              <v-col cols="12" md="4">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Phone</label>
                  <div class="form-control-plaintext">
                    <h4>{{data.tel1}}</h4>
                  </div>
                </div>
              </v-col>
              <v-col cols="12" md="4">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Email</label>
                  <div class="form-control-plaintext">
                    <h4>{{data.email}}</h4>
                  </div>
                </div>
              </v-col>
            </v-row>

            <v-divider class="mx-3" :color="helpers.managementStyles().dividerColor"></v-divider>

            <v-row justify="start" align="center" class="pa-2">
              <v-col cols="12">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Room</label>
                  <div class="form-control-plaintext">
                    <v-chip
                      v-for="roomcontract in data.roomcontracts"
                      :key="roomcontract.uid"
                      class="mr-2 my-2"
                      @click="showTenantRoom(roomcontract)"
                    >
                      <h4 class="text-center ma-2">{{roomcontract.room.name | capitalizeFirstLetter }}</h4>
                    </v-chip>
                  </div>
                </div>
              </v-col>
            </v-row>

            <v-row>
              <v-col col="12">
                <v-divider class="mx-3" :color="helpers.managementStyles().dividerColor"></v-divider>
              </v-col>
            </v-row>
            <v-row class="pa-2" justify="end" align="center">
              <v-col cols="auto">
                <change-password-dialog :uid="this.$route.params.uid" @updated="refreshPage()"></change-password-dialog>
              </v-col>
              <v-col cols="auto">
                <tenant-form
                  :editMode="true"
                  :buttonStyle="editButtonStyle"
                  :uid="this.$route.params.uid"
                  @updated="refreshPage()"
                ></tenant-form>
              </v-col>
              <v-col cols="auto">
                <confirm-dialog
                  :activatorStyle="deleteButtonConfig.activatorStyle"
                  @confirmed="deleteTenant($event,data.uid)"
                ></confirm-dialog>
              </v-col>
            </v-row>
          </v-container>
        </v-card>

        <v-dialog v-model="tenantRoomDialog" max-width="50%">
          <tenant-room :tenant="tenantRoomData.tenant" :room="tenantRoomData.room" :roomcontract="tenantRoomData" :rentalpayments="tenantRoomData.rentalpayments"></tenant-room>
        </v-dialog>
      </v-container>
    </v-content>
  </v-app>
</template>

<script>
import { mapActions } from "vuex";
export default {
  data: () => ({
    tenantRoomDialog : false,
    tenantRoomData : {
      tenant : {},
      room : {},
      contract : {},
    },
    editButtonStyle: {
      block: false,
      color: "success",
      class: "m-3",
      text: "Edit",
      icon: "mdi-pencil"
    },
    deleteButtonConfig: {
      activatorStyle: {
        block: false,
        color: "error",
        class: "m-3",
        text: "Delete",
        icon: "mdi-trash-can-outline"
      }
    },
    data: new Form({
      uid: "",
      name: "",
      icno: "",
      tel1: "",
      email: ""
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
    this.getTenantAction({ uid: this.$route.params.uid })
      .then(data => {
        this.data = data.data;
        console.log(data.data);
        this.$Progress.finish();
        this.endLoadingAction();
      })
      .catch(error => {
        Toast.fire({
          icon: "warning",
          title: "Fail to retrieve the tenant!!!!! "
        });
        this.$Progress.finish();
        this.endLoadingAction();
      });
  },

  methods: {
    ...mapActions({
      getTenantAction: "getTenant",
      deleteTenantAction: "deleteTenant",
      showLoadingAction: "showLoadingAction",
      endLoadingAction: "endLoadingAction"
    }),
    showTenantRoom($data) {
      this.tenantRoomDialog = true;
      this.tenantRoomData = $data;
    },
    deleteTenant($isConfirmed, $uid) {
      if ($isConfirmed) {
        this.$Progress.start();
        this.showLoadingAction();
        this.deleteTenantAction({ uid: $uid })
          .then(data => {
            Toast.fire({
              icon: "success",
              title: "Successful Deleted. "
            });
            this.$Progress.finish();
            this.endLoadingAction();
            this.$router.push("/tenants");
          })
          .catch(error => {
            Toast.fire({
              icon: "warning",
              title: "Fail to delete the tenant!!!!! "
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