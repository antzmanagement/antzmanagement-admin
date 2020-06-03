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
          >Property - {{data.uid}}</v-card-title>
          <v-divider class="mx-3" :color="helpers.managementStyles().dividerColor"></v-divider>
          <v-container>
            <v-row justify="start" align="center" class="pa-2">
              <v-col cols="12">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Name</label>
                  <div class="form-control-plaintext">
                    <v-chip class="ma-2">
                      <h4 class="text-center ma-2">{{data.name | capitalizeFirstLetter }}</h4>
                    </v-chip>
                  </div>
                </div>
              </v-col>
            </v-row>

            <v-divider class="mx-3" :color="helpers.managementStyles().dividerColor"></v-divider>

            <v-row justify="start" align="center" class="pa-2">
              <v-col cols="12" md="12">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Description</label>
                  <v-card tite class="pa-5 my-2">
                    <pre style="height:200px;overflow:auto" class="d-inline-block form-control-plaintext">{{data.desc}}
                      </pre>
                  </v-card>
                </div>
              </v-col>
            </v-row>

            <v-divider class="mx-3" :color="helpers.managementStyles().dividerColor"></v-divider>
            <v-row class="pa-2" justify="end" align="center">
              <v-col cols="auto">
                <v-btn color="success" class="ma-3" @click="propertyFormDialog = true">
                  <v-icon left>mdi-pencil</v-icon>Edit
                </v-btn>
              </v-col>
              <v-col cols="auto">
                <confirm-dialog
                  :activatorStyle="deleteButtonConfig.buttonStyle"
                  @confirmed="deleteProperty($event,data.uid)"
                ></confirm-dialog>
              </v-col>
            </v-row>
          </v-container>
        </v-card>
      </v-container>

      <v-dialog v-model="propertyFormDialog" persistent fullscreen hideOverlay>
        <property-form
          :reset="propertyFormDialog"
          :editMode="true"
          :uid="this.$route.params.uid"
          @updated="refreshPage()"
          @close="propertyFormDialog = false"
        ></property-form>
      </v-dialog>
    </v-content>
  </v-app>
</template>

<script>
import { mapActions } from "vuex";
export default {
  data: () => ({
    propertyFormDialog : false,
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
    this.getPropertyAction({ uid: this.$route.params.uid })
      .then(data => {
        this.data = data.data;
        console.log(this.data);
        this.$Progress.finish();
        this.endLoadingAction();
      })
      .catch(error => {
        Toast.fire({
          icon: "warning",
          title: "Fail to retrieve the property!!!!! "
        });
        this.$Progress.finish();
        this.endLoadingAction();
      });
  },

  methods: {
    ...mapActions({
      getPropertyAction: "getProperty",
      deletePropertyAction: "deleteProperty",
      showLoadingAction: "showLoadingAction",
      endLoadingAction: "endLoadingAction"
    }),
    deleteProperty($isConfirmed, $uid) {
      if ($isConfirmed) {
        this.$Progress.start();
        this.showLoadingAction();
        this.deletePropertyAction({ uid: $uid })
          .then(data => {
            Toast.fire({
              icon: "success",
              title: "Successful Deleted. "
            });
            this.$Progress.finish();
            this.endLoadingAction();
            this.$router.push("/properties");
          })
          .catch(error => {
            Toast.fire({
              icon: "warning",
              title: "Fail to delete the property!!!!! "
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