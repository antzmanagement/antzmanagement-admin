
<script>
import moment from "moment";
import { mapActions } from "vuex";
import { _ } from "../../../common/common-function";
import PrintPaymentButton from "../PaymentManagement/PrintPaymentButton.vue";
export default {
  components: { PrintPaymentButton },
  data: () => ({
    editButtonStyle: {
      block: false,
      color: "success",
      class: "m-3",
      text: "Edit",
      icon: "mdi-pencil",
    },
    _: _,
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
      { text: "Claimed By" },
      { text: "Maintenance Date" },
      { text: "Actions" },
    ],
    cleaningHeaders: [
      { text: "Cleaning Type" },
      { text: "Cleaning Status" },
      { text: "Price (RM)" },
      { text: "Claimed By" },
      { text: "Cleaning Date" },
      { text: "Actions" },
    ],
    deleteButtonConfig: {
      buttonStyle: {
        block: false,
        color: "error",
        class: "m-3",
        text: "Delete",
        icon: "mdi-trash-can-outline",
      },
    },
    data: new Form({
      remark: "",
      room: [],
    }),
  }),

  computed: {
    isLoading() {
      return this.$store.getters.isLoading;
    },
  },
  watch: {},
  created() {
    this.$Progress.start();
    this.showLoadingAction();
    this.getRoomCheckAction({ uid: this.$route.params.uid })
      .then((data) => {
        this.data = data.data;
        this.$Progress.finish();
        this.endLoadingAction();
      })
      .catch((error) => {
        Toast.fire({
          icon: "warning",
          title: "Fail to retrieve the Room Check!!!!! ",
        });
        this.$Progress.finish();
        this.endLoadingAction();
      });
  },

  methods: {
    ...mapActions({
      getRoomCheckAction: "getRoomCheck",
      deleteRoomCheckAction: "deleteRoomCheck",
      updateMaintenanceAction: "updateMaintenance",
      deleteMaintenanceAction: "deleteMaintenance",
      deleteCleaningAction: "deleteCleaning",
      updateCleaningAction: "updateCleaning",
      showLoadingAction: "showLoadingAction",
      endLoadingAction: "endLoadingAction",
    }),
    isEmpty(data) {
      return this._.isEmpty(data);
    },
    showRoom($data) {
      this.$router.push("/room/" + $data.uid);
    },
    deleteRoomCheck($isConfirmed, $uid) {
      if ($isConfirmed) {
        this.$Progress.start();
        this.showLoadingAction();
        this.deleteRoomCheckAction({ uid: $uid })
          .then((data) => {
            Toast.fire({
              icon: "success",
              title: "Successful Deleted. ",
            });
            this.$Progress.finish();
            this.endLoadingAction();
            this.$router.push("/roomchecks");
          })
          .catch((error) => {
            Toast.fire({
              icon: "warning",
              title: "Fail to delete the Room Check!!!!! ",
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
      if (!_.get(this.data, `room.id`)) {
        Toast.fire({
          icon: "error",
          title: "Please Select A Room First. ",
        });
      } else {
        this.maintenanceEditMode = editMode == true;
        this.selectedMaintenance = item || {};
        this.maintenanceFormDialog = true;
      }
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
            _.filter(_.cloneDeep(this.data.maintenances), (maintenance) => {
              return data.data.uid != maintenance.uid;
            }) || [];
          this.data.maintenances = newData;
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

        this.data.maintenances = _.unionBy(
          [$data],
          this.data.maintenances,
          "uid"
        );
      } catch (error) {
        console.log(error);
      }
    },
    openCleaningDialog(item, editMode) {
      if (!_.get(this.data, `room.id`)) {
        Toast.fire({
          icon: "error",
          title: "Please Select A Room First. ",
        });
      } else {
        this.cleaningEditMode = editMode == true;
        this.selectedCleaning = item || {};
        this.cleaningFormDialog = true;
      }
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
            _.filter(_.cloneDeep(this.data.cleanings), (cleaning) => {
              return data.data.uid != cleaning.uid;
            }) || [];
          this.data.cleanings = newData;
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

        this.data.cleanings = _.unionBy([$data], this.data.cleanings, "uid");
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
      v-if="helpers.isAccessible(_.get(role, ['name']), 'roomCheck', 'view')"
    >
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
            >RoomCheck - {{ data.uid }}</v-card-title
          >
          <v-container>
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
                      class="ma-2"
                      @click="
                        helpers.isAccessible(
                          _.get(role, ['name']),
                          'room',
                          'view'
                        )
                          ? showRoom(data.room)
                          : null
                      "
                    >
                      <h4 class="text-center ma-2">{{ data.room.name }}</h4>
                    </v-chip>
                  </div>
                </div>
              </v-col>
            </v-row>
            <v-divider
              class="mx-3"
              :color="helpers.managementStyles().dividerColor"
            ></v-divider>
            <v-row justify="start" align="center" class="pa-2">
              <v-col cols="12" md="4">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Category</label>
                  <div class="form-control-plaintext">
                    <h4>{{ data.category }}</h4>
                  </div>
                </div>
              </v-col>
              <v-col cols="12" md="4">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Checked Date</label>
                  <div class="form-control-plaintext">
                    <h4>{{ data.checked_date | formatDate }}</h4>
                  </div>
                </div>
              </v-col>
              <v-col cols="12">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Remark</label>
                  <div class="form-control-plaintext">
                    <h4>{{ data.remark }}</h4>
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
                    :items="data.maintenances"
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
                          {{ _.get(props.item, `property.text`) || "N/A" }}
                        </td>
                        <td class="text-truncate">
                          {{ props.item.maintenance_type }}
                        </td>
                        <td class="text-truncate">
                          {{ props.item.maintenance_status }}
                        </td>
                        <td class="text-truncate">{{ props.item.price }}</td>
                        <td class="text-truncate">
                          {{
                            _.get(props.item, ["claim_by_owner"])
                              ? _.get(props.item, ["owner", "name"]) || "N/A"
                              : _.get(props.item, ["claim_by_tenant"])
                              ? _.get(props.item, ["tenant", "name"]) || "N/A"
                              : "N/A"
                          }}
                        </td>
                        <td class="text-truncate">
                          {{ props.item.maintenance_date || "N/A" }}
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
                    :items="data.cleanings || []"
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
                          {{
                            _.get(props.item, ["claim_by_owner"])
                              ? _.get(props.item, ["owner", "name"]) || "N/A"
                              : _.get(props.item, ["claim_by_tenant"])
                              ? _.get(props.item, ["tenant", "name"]) || "N/A"
                              : "N/A"
                          }}
                        </td>
                        <td class="text-truncate">
                          {{ props.item.cleaning_date || "N/A" }}
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
                              )
                              &&_.get(props.item , ['cleaning_status']) != 'reject'
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

            <v-divider
              class="mx-3"
              :color="helpers.managementStyles().dividerColor"
            ></v-divider>
            <v-row class="pa-2" justify="end" align="center">
              <v-col
                cols="auto"
                v-if="
                  helpers.isAccessible(
                    _.get(role, ['name']),
                    'roomCheck',
                    'edit'
                  )
                "
              >
                <room-check-form
                  :editMode="true"
                  :uid="this.data.uid"
                  :buttonStyle="editButtonStyle"
                  @updated="refreshPage()"
                ></room-check-form>
              </v-col>
              <v-col
                cols="auto"
                v-if="
                  helpers.isAccessible(
                    _.get(role, ['name']),
                    'roomCheck',
                    'delete'
                  )
                "
              >
                <confirm-dialog
                  :activatorStyle="deleteButtonConfig.buttonStyle"
                  @confirmed="deleteRoomCheck($event, data.uid)"
                ></confirm-dialog>
              </v-col>
            </v-row>
          </v-container>
        </v-card>

        <v-dialog
          v-model="maintenanceFormDialog"
          persistent
          hideOverlay
          max-width="600px"
        >
          <maintenance-form-1
            :roomId="
              _.get(this.selectedMaintenance, `room.id`) ||
              _.get(this.selectedMaintenance, `room_id`) ||
              null
            "
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
            :roomId="
              _.get(this.selectedCleaning, `room.id`) ||
              _.get(this.selectedCleaning, `room_id`) ||
              null
            "
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
      </v-container>
    </v-content>
  </v-app>
</template>
