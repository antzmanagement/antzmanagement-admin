<template>
  <v-app>
    <v-container>
      <navbar></navbar>
      <v-content>
        <loading></loading>
        <v-card class="mx-2" outlined color="#1b262c">
          <v-card-title class="headline">Tenant - {{data.uid}}</v-card-title>
          <v-divider class="mx-3" color="#ffffff"></v-divider>
          <v-container>
            <v-row>
              <v-col cols="6" class="m-3">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Name</label>
                  <div class="form-control-plaintext">
                    <h4>{{data.name}}</h4>
                  </div>
                </div>
              </v-col>
              <v-col cols="6" class="m-3">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">IC-NO</label>
                  <div class="form-control-plaintext">
                    <h4>{{data.icno}}</h4>
                  </div>
                </div>
              </v-col>
            </v-row>
            <v-row>
              <v-col cols="4" class="m-3">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Phone</label>
                  <div class="form-control-plaintext">
                    <h4>{{data.tel1}}</h4>
                  </div>
                </div>
              </v-col>
              <v-col cols="4" class="m-3">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Email</label>
                  <div class="form-control-plaintext">
                    <h4>{{data.email}}</h4>
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
              <change-password-dialog :uid="this.$route.params.uid" @updated="refreshPage()"></change-password-dialog>
              <tenant-form
                :editMode="true"
                :buttonStyle="editButtonStyle"
                :uid="this.$route.params.uid" 
                @updated="refreshPage()"
              ></tenant-form>
              <confirm-dialog
                :activatorStyle="deleteButtonConfig.activatorStyle"
                @confirmed="deleteTenant($event,data.uid)"
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
    this.$vuetify.theme.dark = true;

    
    this.$Progress.start();
    this.showLoadingAction();
    this.getTenantAction({ uid: this.$route.params.uid })
      .then(data => {
        this.data = data.data;
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