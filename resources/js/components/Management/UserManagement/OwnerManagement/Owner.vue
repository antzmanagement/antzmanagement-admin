

<script>
import moment from "moment";
import { mapActions } from "vuex";
export default {
  data: () => ({
    moment: moment,
    maintenancePayFormDialog: false,
    maintenanceFormDialog: false,
    maintenanceEditMode: false,
    selectedMaintenance: {},
    cleaningPayFormDialog: false,
    cleaningEditMode: false,
    selectedCleaning: {},
    cleaningFormDialog: false,
    maintenanceHeaders: [
      {
        text: "Property",
      },
      { text: "Repair Type" },
      { text: "Maintenance Status" },
      { text: "Price (RM)" },
      { text: "Room" },
      { text: "Maintenance Date" },
      { text: "Actions" },
    ],
    cleaningHeaders: [
      { text: "Cleaning Type" },
      { text: "Cleaning Status" },
      { text: "Price (RM)" },
      { text: "Room" },
      { text: "Cleaning Date" },
      { text: "Actions" },
    ],
    role: "",
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
        document.title = `Owner ${this.data.name || ""}`;
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
      updateMaintenanceAction: "updateMaintenance",
      deleteMaintenanceAction: "deleteMaintenance",
      deleteCleaningAction: "deleteCleaning",
      updateCleaningAction: "updateCleaning",
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
    showMaintenance($data) {
      this.$router.push("/maintenance/" + $data.uid);
    },
    showCleaning($data) {
      this.$router.push("/cleaning/" + $data.uid);
    },
    openMaintenanceDialog(item, editMode) {
      this.maintenanceEditMode = editMode == true;
      this.selectedMaintenance = item || {};
      this.maintenanceFormDialog = true;
    },
    deleteMaintenance(item) {
      this.showLoadingAction();
      this.deleteMaintenanceAction(item)
        .then((data) => {
          Toast.fire({
            icon: "success",
            title: "Successful Deleted Maintenance. ",
          });
          this.$Progress.finish();
          this.endLoadingAction();
          let newData =
            _.filter(_.cloneDeep(this.data.ownermaintenances), (maintenance) => {
              return data.data.uid != maintenance.uid;
            }) || [];
          this.data.ownermaintenances = newData;
        })
        .catch((error) => {
          Toast.fire({
            icon: "warning",
            title: "Something went wrong!!!!! ",
          });
          this.$Progress.finish();
          this.endLoadingAction();
        });
    },
    updateMaintenance($data) {
      try {
        console.log($data);
        if (_.get($data, "uid")) {
          let finalData = _.cloneDeep($data);
          this.updateMaintenanceAction(finalData)
            .then((data) => {
              Toast.fire({
                icon: "success",
                title: "Successful Updated Maintenance. ",
              });
              this.$Progress.finish();
              this.endLoadingAction();
              this.maintenanceFormDialog = false;
              this.updateMaintenances(data.data);
            })
            .catch((error) => {
              Toast.fire({
                icon: "warning",
                title: "Something went wrong!!!!! ",
              });
              this.$Progress.finish();
              this.endLoadingAction();
              this.maintenanceFormDialog = false;
            });
        } else {
          Toast.fire({
            icon: "error",
            title: "Maintenance Not Found!!!!! ",
          });
          this.$Progress.finish();
          this.endLoadingAction();
          this.maintenanceFormDialog = false;
        }
      } catch (error) {
        console.log(error);
      }
    },
    updateMaintenances($data) {
      try {
        this.maintenanceFormDialog = false;
        if (!_.get($data, "uid")) {
          $data.uid = new Date().getTime();
        }

        this.data.ownermaintenances = _.unionBy(
          [$data],
          this.data.ownermaintenances,
          "uid"
        );
      } catch (error) {
        console.log(error);
      }
    },
    openCleaningDialog(item, editMode) {
      this.cleaningEditMode = editMode == true;
      this.selectedCleaning = item || {};
      this.cleaningFormDialog = true;
    },
    deleteCleaning(item) {
      this.showLoadingAction();
      this.deleteCleaningAction(item)
        .then((data) => {
          Toast.fire({
            icon: "success",
            title: "Successful Deleted Cleaning. ",
          });
          this.$Progress.finish();
          this.endLoadingAction();
          let newData =
            _.filter(_.cloneDeep(this.data.ownercleanings), (cleaning) => {
              return data.data.uid != cleaning.uid;
            }) || [];
          this.data.ownercleanings = newData;
        })
        .catch((error) => {
          Toast.fire({
            icon: "warning",
            title: "Something went wrong!!!!! ",
          });
          this.$Progress.finish();
          this.endLoadingAction();
        });
    },
    updateCleaning($data) {
      try {
        console.log($data);
        if (_.get($data, "uid")) {
          let finalData = _.cloneDeep($data);
          this.updateCleaningAction(finalData)
            .then((data) => {
              Toast.fire({
                icon: "success",
                title: "Successful Updated Cleaning. ",
              });
              this.$Progress.finish();
              this.endLoadingAction();
              this.cleaningFormDialog = false;
              this.updateCleanings(data.data);
            })
            .catch((error) => {
              Toast.fire({
                icon: "warning",
                title: "Something went wrong!!!!! ",
              });
              this.$Progress.finish();
              this.endLoadingAction();
              this.cleaningFormDialog = false;
            });
        } else {
          Toast.fire({
            icon: "error",
            title: "Cleaning Not Found!!!!! ",
          });
          this.$Progress.finish();
          this.endLoadingAction();
          this.cleaningFormDialog = false;
        }
      } catch (error) {
        console.log(error);
      }
    },
    updateCleanings($data) {
      try {
        this.cleaningFormDialog = false;
        if (!_.get($data, "uid")) {
          $data.uid = new Date().getTime();
        }

        this.data.ownercleanings = _.unionBy([$data], this.data.ownercleanings, "uid");
      } catch (error) {
        console.log(error);
      }
    },
  },
};
</script>

<template>
  <v-app>
    <navbar
      :returnRole="
        (role) => {
          this.role = role;
        }
      "
    ></navbar>
    <v-content
      :class="helpers.managementStyles().backgroundClass"
      v-if="helpers.isAccessible(_.get(role, ['name']), 'owner', 'view')"
    >
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
              <!-- <v-col cols="12" md="4">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Reference No</label>
                  <div class="form-control-plaintext">
                    <h4>{{ data.referenceno }}</h4>
                  </div>
                </div>
              </v-col> -->
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
              <v-col cols="12" md="4"> </v-col>
              <v-col cols="12" md="4">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Bank</label>
                  <div class="form-control-plaintext">
                    <h4>{{ data.banktype }}</h4>
                  </div>
                </div>
              </v-col>
              <v-col cols="12" md="4">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Other Bank</label>
                  <div class="form-control-plaintext">
                    <h4>{{ data.otherbanktype }}</h4>
                  </div>
                </div>
              </v-col>
              <v-col cols="12" md="4">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Bank Account</label>
                  <div class="form-control-plaintext">
                    <h4>{{ data.bankaccount }}</h4>
                  </div>
                </div>
              </v-col>
              <v-col cols="12" md="4">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Bank Account Name</label>
                  <div class="form-control-plaintext">
                    <h4>{{ data.bankaccountname }}</h4>
                  </div>
                </div>
              </v-col>
              <v-col cols="12" md="4"> </v-col>
              <v-col cols="12" md="4">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Phone 1</label>
                  <div class="form-control-plaintext">
                    <h4>{{ data.tel1 }}</h4>
                  </div>
                </div>
              </v-col>
              <v-col cols="12" md="4">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Phone 2</label>
                  <div class="form-control-plaintext">
                    <h4>{{ data.tel2 }}</h4>
                  </div>
                </div>
              </v-col>
              <v-col cols="12" md="4">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Phone 3</label>
                  <div class="form-control-plaintext">
                    <h4>{{ data.tel3 }}</h4>
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
                      @click="
                        helpers.isAccessible(
                          _.get(role, ['name']),
                          'room',
                          'view'
                        )
                          ? showRoom(room)
                          : null
                      "
                    >
                      <h4 class="text-center ma-2">
                        {{ room.name | capitalizeFirstLetter }}
                      </h4>
                    </v-chip>
                  </div>
                </div>
              </v-col>
            </v-row>

            <v-divider
              class="mx-3"
              :color="helpers.managementStyles().dividerColor"
            ></v-divider>
            <v-row>
              <v-col
                cols="12"
                :class="helpers.managementStyles().centerWrapperClass"
              >
                <v-card raised width="100%">
                  <v-data-table
                    :headers="maintenanceHeaders"
                    :items="data.ownermaintenances"
                    fixed-header
                    height="300px"
                    :items-per-page="5"
                    disable-sort
                  >
                    <template v-slot:top>
                      <v-toolbar flat color="white">
                        <v-toolbar-title
                          :class="helpers.managementStyles().subtitleClass"
                          >Maintenance</v-toolbar-title
                        >
                        <v-spacer></v-spacer>
                      </v-toolbar>
                    </template>
                    <template v-slot:item="props">
                      <tr :key="props.item.uid">
                        <td class="text-truncate">
                          {{
                            _.get(props.item, ["property", "name"]) == "others"
                              ? `${
                                  _.get(props.item, ["property", "text"]) || ""
                                } - ${
                                  _.get(props.item, ["other_property"]) || ""
                                }`
                              : _.get(props.item, ["property", "text"]) || "N/A"
                          }}
                        </td>
                        <td class="text-truncate">
                          {{ props.item.maintenance_type }}
                        </td>
                        <td class="text-truncate">
                          {{ props.item.maintenance_status }}
                        </td>
                        <td class="text-truncate">{{ props.item.price }}</td>
                        <td class="text-truncate">
                          {{ _.get(props.item, `room.unit`) }}
                        </td>
                        <td class="text-truncate">
                          {{
                            _.get(props.item, ["maintenance_date"])
                              ? moment(props.item.maintenance_date).format(
                                  "YYYY-MM-DD HH:mm"
                                )
                              : "N/A" || "N/A"
                          }}
                        </td>
                        <td class="text-truncate">
                          <print-maintenance-button
                            :item="props.item"
                            v-if="
                              props.item.paid &&
                              helpers.isAccessible(
                                _.get(role, ['name']),
                                'roomMaintenance',
                                'print'
                              )
                            "
                          >
                            <v-icon small class="mr-2" color="success"
                              >mdi-printer</v-icon
                            >
                          </print-maintenance-button>

                          <v-icon
                            small
                            class="mr-2"
                            @click="
                              maintenancePayFormDialog = true;
                              selectedMaintenance = props.item;
                            "
                            color="warning"
                            v-else-if="
                              helpers.isAccessible(
                                _.get(role, ['name']),
                                'roomMaintenance',
                                'makePayment'
                              ) &&
                              _.get(props.item, 'maintenance_status') !=
                                'reject'
                            "
                            >mdi-currency-usd</v-icon
                          >
                          <v-icon
                            small
                            class="mr-2"
                            @click="openMaintenanceDialog(props.item, true)"
                            color="success"
                            v-if="
                              helpers.isAccessible(
                                _.get(role, ['name']),
                                'roomMaintenance',
                                'edit'
                              )
                            "
                            >mdi-pencil</v-icon
                          >

                          <confirm-dialog
                            v-if="
                              helpers.isAccessible(
                                _.get(role, ['name']),
                                'roomMaintenance',
                                'delete'
                              )
                            "
                            @confirmed="
                              $event ? deleteMaintenance(props.item) : null
                            "
                          >
                            <template v-slot="{ on }">
                              <v-icon color="error" size="small" v-on="on"
                                >mdi-trash-can-outline</v-icon
                              >
                            </template>
                          </confirm-dialog>
                        </td>
                      </tr>
                    </template>
                  </v-data-table>
                </v-card>
              </v-col>
            </v-row>
            <v-divider
              class="mx-3"
              :color="helpers.managementStyles().dividerColor"
            ></v-divider>
            <v-row>
              <v-col
                cols="12"
                :class="helpers.managementStyles().centerWrapperClass"
              >
                <v-card raised width="100%">
                  <v-data-table
                    :headers="cleaningHeaders"
                    :items="data.ownercleanings || []"
                    fixed-header
                    height="300px"
                    :items-per-page="5"
                    disable-sort
                  >
                    <template v-slot:top>
                      <v-toolbar flat color="white">
                        <v-toolbar-title
                          :class="helpers.managementStyles().subtitleClass"
                          >Cleaning</v-toolbar-title
                        >
                        <v-spacer></v-spacer>
                      </v-toolbar>
                    </template>
                    <template v-slot:item="props">
                      <tr :key="props.item.uid">
                        <td class="text-truncate">
                          {{ props.item.cleaning_type }}
                        </td>
                        <td class="text-truncate">
                          {{ props.item.cleaning_status }}
                        </td>
                        <td class="text-truncate">{{ props.item.price }}</td>
                        <td class="text-truncate">
                          {{ _.get(props.item, `room.unit`) }}
                        </td>
                        <td class="text-truncate">
                          {{
                            _.get(props.item, ["cleaning_date"])
                              ? moment(props.item.cleaning_date).format(
                                  "YYYY-MM-DD HH:mm"
                                )
                              : "N/A" || "N/A"
                          }}
                        </td>
                        <td class="text-truncate">
                          <print-cleaning-button
                            :item="props.item"
                            v-if="
                              props.item.paid &&
                              helpers.isAccessible(
                                _.get(role, ['name']),
                                'roomMaintenance',
                                'print'
                              )
                            "
                          >
                            <v-icon small class="mr-2" color="success"
                              >mdi-printer</v-icon
                            >
                          </print-cleaning-button>

                          <v-icon
                            small
                            class="mr-2"
                            @click="
                              cleaningPayFormDialog = true;
                              selectedCleaning = props.item;
                            "
                            color="warning"
                            v-else-if="
                              helpers.isAccessible(
                                _.get(role, ['name']),
                                'roomMaintenance',
                                'makePayment'
                              ) &&
                              _.get(props.item, ['cleaning_status']) != 'reject'
                            "
                            >mdi-currency-usd</v-icon
                          >
                          <v-icon
                            small
                            class="mr-2"
                            @click="openCleaningDialog(props.item, true)"
                            color="success"
                            v-if="
                              helpers.isAccessible(
                                _.get(role, ['name']),
                                'roomMaintenance',
                                'edit'
                              )
                            "
                            >mdi-pencil</v-icon
                          >

                          <confirm-dialog
                            v-if="
                              helpers.isAccessible(
                                _.get(role, ['name']),
                                'roomMaintenance',
                                'delete'
                              )
                            "
                            @confirmed="
                              $event ? deleteCleaning(props.item) : null
                            "
                          >
                            <template v-slot="{ on }">
                              <v-icon color="error" size="small" v-on="on"
                                >mdi-trash-can-outline</v-icon
                              >
                            </template>
                          </confirm-dialog>
                        </td>
                      </tr>
                    </template>
                  </v-data-table>
                </v-card>
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
                <print-owner-button :item="this.data">
                  <v-btn color="success">
                    <v-icon small class="mr-2" left>mdi-printer</v-icon>
                    Print
                  </v-btn>
                </print-owner-button>
              </v-col>
              <!-- <v-col cols="auto">
                <change-password-dialog
                  :uid="this.$route.params.uid"
                  @updated="refreshPage()"
                ></change-password-dialog>
              </v-col> -->
              <v-col
                cols="auto"
                v-if="
                  helpers.isAccessible(_.get(role, ['name']), 'owner', 'edit')
                "
              >
                <owner-form
                  :editMode="true"
                  :buttonStyle="editButtonStyle"
                  :uid="this.$route.params.uid"
                  @updated="refreshPage()"
                ></owner-form>
              </v-col>
              <v-col
                cols="auto"
                v-if="
                  helpers.isAccessible(_.get(role, ['name']), 'owner', 'delete')
                "
              >
                <confirm-dialog
                  :activatorStyle="deleteButtonConfig.activatorStyle"
                  @confirmed="deleteOwner($event, data.uid)"
                ></confirm-dialog>
              </v-col>
            </v-row>
          </v-container>
        </v-card>
      </v-container>

      <v-dialog
        v-model="maintenanceFormDialog"
        persistent
        hideOverlay
        max-width="600px"
      >
        <maintenance-form-1
          :selectedData="selectedMaintenance || {}"
          :reset="maintenanceFormDialog"
          :editMode="maintenanceEditMode"
          @cancel="maintenanceFormDialog = false"
          @submit="updateMaintenance"
        >
        </maintenance-form-1>
      </v-dialog>

      <v-dialog
        v-model="maintenancePayFormDialog"
        persistent
        hideOverlay
        max-width="600px"
      >
        <maintenance-pay-form
          :uid="_.get(this.selectedMaintenance, ['uid'])"
          @close="maintenancePayFormDialog = false"
          @updated="updateMaintenances"
        >
        </maintenance-pay-form>
      </v-dialog>

      <v-dialog
        v-model="cleaningFormDialog"
        persistent
        hideOverlay
        max-width="600px"
      >
        <cleaning-form-1
          :selectedData="selectedCleaning || {}"
          :reset="cleaningFormDialog"
          :editMode="cleaningEditMode"
          @cancel="cleaningFormDialog = false"
          @submit="updateCleaning"
        >
        </cleaning-form-1>
      </v-dialog>

      <v-dialog
        v-model="cleaningPayFormDialog"
        persistent
        hideOverlay
        max-width="600px"
      >
        <cleaning-pay-form
          :uid="_.get(this.selectedCleaning, ['uid'])"
          @close="cleaningPayFormDialog = false"
          @updated="updateCleanings"
        >
        </cleaning-pay-form>
      </v-dialog>
    </v-content>
  </v-app>
</template>