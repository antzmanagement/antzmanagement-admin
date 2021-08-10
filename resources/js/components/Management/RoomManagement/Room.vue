<script>
import { mapActions } from "vuex";
import { sortByDateDesc, _ } from "../../../common/common-function";
export default {
  data: () => ({
    _: _,
    editButtonStyle: {
      block: false,
      color: "success",
      class: "m-3",
      text: "Edit",
      icon: "mdi-pencil",
    },
    deleteButtonConfig: {
      buttonStyle: {
        block: false,
        color: "error",
        class: "m-3",
        text: "Delete",
        icon: "mdi-trash-can-outline",
      },
    },

    maintenanceFormDialogConfig: {
      buttonStyle: {
        block: false,
        class: "",
        text: "Add New",
        icon: "mdi-plus",
        color: "primary",
        evalation: "5",
        isIcon: false,
      },
      dialogStyle: {
        persistent: true,
        maxWidth: "50%",
        fullscreen: false,
        hideOverlay: true,
      },
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
      room_types: [],
      maintenances: [],
    }),
    maintenancePayFormDialog: false,
    maintenanceFormDialog: false,
    maintenanceEditMode: false,
    selectedMaintenance: {},
    cleaningPayFormDialog: false,
    cleaningEditMode: false,
    selectedCleaning: {},
    cleaningFormDialog: false,
    roomCheckHeaders: [
      {
        text: "Check date",
      },
      {
        text: "Category",
      },
    ],
    maintenanceHeaders: [
      {
        text: "Property",
      },
      { text: "Repair Type" },
      { text: "Maintenance Status" },
      { text: "Price (RM)" },
      { text: "Claimed By" },
      { text: "Actions" },
    ],
    cleaningHeaders: [
      { text: "Cleaning Type" },
      { text: "Cleaning Status" },
      { text: "Price (RM)" },
      { text: "Claimed By" },
      { text: "Actions" },
    ],
    propertyHeaders: [{ text: "Name" }, { text: "Qty" }, { text: "Remark" }],
  }),

  computed: {
    isLoading() {
      return this.$store.getters.isLoading;
    },
  },
  created() {
    this.$Progress.start();
    this.showLoadingAction();
    this.getRoomAction({ uid: this.$route.params.uid })
      .then((data) => {
        
        this.data = data.data;
        this.$Progress.finish();
        this.endLoadingAction();
        document.title = `Room ${this.data.unit || ''}`;
      })
      .catch((error) => {
        Toast.fire({
          icon: "warning",
          title: "Fail to retrieve the room!!!!! ",
        });
        this.$Progress.finish();
        this.endLoadingAction();
      });
  },

  methods: {
    ...mapActions({
      updateMaintenanceAction: "updateMaintenance",
      deleteMaintenanceAction: "deleteMaintenance",
      deleteCleaningAction: "deleteCleaning",
      updateCleaningAction: "updateCleaning",
      getRoomAction: "getRoom",
      deleteRoomAction: "deleteRoom",
      showLoadingAction: "showLoadingAction",
      endLoadingAction: "endLoadingAction",
    }),

    showMaintenance($data) {
      this.$router.push("/maintenance/" + $data.uid);
    },
    showRoomCheck($data) {
      this.$router.push("/roomcheck/" + $data.uid);
    },
    deleteRoom($isConfirmed, $uid) {
      if ($isConfirmed) {
        this.$Progress.start();
        this.showLoadingAction();
        this.deleteRoomAction({ uid: $uid })
          .then((data) => {
            Toast.fire({
              icon: "success",
              title: "Successful Deleted. ",
            });
            this.$Progress.finish();
            this.endLoadingAction();
            this.$router.push("/rooms");
          })
          .catch((error) => {
            Toast.fire({
              icon: "warning",
              title: "Fail to delete the room!!!!! ",
            });
            this.$Progress.finish();
            this.endLoadingAction();
          });
      }
    },
    refreshPage() {
      location.reload();
    },
    goToOwner(uid) {
      if (uid) {
        console.log("uid");
        console.log(uid);
        this.$router.push("/owner/" + $data.uid);
      }
    },
    openMaintenanceDialog(item, editMode) {
      if (!_.get(this.data, `id`)) {
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
      if (!_.get(this.data, `id`)) {
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
      v-if="helpers.isAccessible(_.get(role, ['name']), 'room', 'view')"
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
            >Room - {{ data.uid }}</v-card-title
          >
          <v-divider
            class="mx-3"
            :color="helpers.managementStyles().dividerColor"
          ></v-divider>
          <v-container>
            <v-row justify="start" align="center" class="pa-2">
              <v-col cols="12">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">RoomType</label>
                  <div class="form-control-plaintext">
                    <v-chip
                      class="ma-2"
                      v-for="roomType in data.room_types"
                      :key="roomType.uid"
                      :to="
                        helpers.isAccessible(
                          _.get(role, ['name']),
                          'roomType',
                          'view'
                        )
                          ? { name: 'roomtype', params: { uid: roomType.uid } }
                          : {}
                      "
                    >
                      <h4 class="text-center ma-2">
                        {{ roomType.name | capitalizeFirstLetter }}
                      </h4>
                    </v-chip>
                  </div>
                </div>
              </v-col>
            </v-row>
            <v-row
              justify="start"
              align="center"
              class="pa-2"
              v-if="
                _.isArray(_.get(data, ['roomcontracts'])) &&
                !_.isEmpty(_.get(data, ['roomcontracts']))
              "
            >
              <v-divider
                :color="helpers.managementStyles().dividerColor"
              ></v-divider>
              <v-col cols="12">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Room Contracts</label>
                  <div class="form-control-plaintext">
                    <v-chip
                      class="ma-2"
                      v-for="roomcontract in _.sortBy(data.roomcontracts, [
                        'startdate',
                      ])"
                      :key="roomcontract.uid"
                      :to="
                        helpers.isAccessible(
                          _.get(role, ['name']),
                          'roomContract',
                          'view'
                        )
                          ? {
                              name: 'roomcontract',
                              params: { uid: roomcontract.uid },
                            }
                          : {}
                      "
                    >
                      <h4 class="text-center ma-2">
                        {{
                          _.get(roomcontract, ["name"]) ||
                          "" | capitalizeFirstLetter
                        }}
                      </h4>
                    </v-chip>
                  </div>
                </div>
              </v-col>
            </v-row>

            <v-row
              justify="start"
              align="center"
              class="pa-2"
              v-if="
                _.isArray(_.get(data, ['owners'])) &&
                !_.isEmpty(_.get(data, ['owners']))
              "
            >
              <v-divider
                :color="helpers.managementStyles().dividerColor"
              ></v-divider>
              <v-col cols="12">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Owners</label>
                  <div class="form-control-plaintext">
                    <v-chip
                      class="ma-2"
                      v-for="owner in data.owners"
                      :key="owner.uid"
                      :to="
                        helpers.isAccessible(
                          _.get(role, ['name']),
                          'owner',
                          'view'
                        )
                          ? { name: 'owner', params: { uid: owner.uid } }
                          : null
                      "
                    >
                      <h4 class="text-center ma-2" type="button">
                        {{
                          _.get(owner, ["name"]) || "" | capitalizeFirstLetter
                        }}
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
            <v-row justify="start" align="center" class="pa-2">
              <v-col cols="12" md="6">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Room Status</label>
                  <div class="form-control-plaintext">
                    <h4>{{ data.room_status }}</h4>
                  </div>
                </div>
              </v-col>
              <v-col cols="12" md="6">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Area Size</label>
                  <div class="form-control-plaintext">
                    <h4>{{ data.size }}</h4>
                  </div>
                </div>
              </v-col>

              <!-- <v-col cols="12" md="4">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Block</label>
                  <div class="form-control-plaintext">
                    <h4>{{ data.block }}</h4>
                  </div>
                </div>
              </v-col> -->
              <v-col cols="12" md="4">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Jalan</label>
                  <div class="form-control-plaintext">
                    <h4>{{ data.jalan }}</h4>
                  </div>
                </div>
              </v-col>
              <v-col cols="12" md="4">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Lot</label>
                  <div class="form-control-plaintext">
                    <h4>{{ data.lot }}</h4>
                  </div>
                </div>
              </v-col>
              <v-col cols="12" md="4">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Floor</label>
                  <div class="form-control-plaintext">
                    <h4>{{ data.floor }}</h4>
                  </div>
                </div>
              </v-col>
              <v-col cols="12" md="4">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Unit</label>
                  <div class="form-control-plaintext">
                    <h4>{{ data.unit }}</h4>
                  </div>
                </div>
              </v-col>
              <v-col cols="12">
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
                  <label class="form-label mb-0">State</label>
                  <div class="form-control-plaintext">
                    <h4>{{ data.state }}</h4>
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
              <!-- <v-col cols="12" md="4">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Country</label>
                  <div class="form-control-plaintext">
                    <h4>{{ data.country }}</h4>
                  </div>
                </div>
              </v-col> -->
              <v-col cols="12" md="4">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">TNB Account No</label>
                  <div class="form-control-plaintext">
                    <h4>{{ data.tnb_account_no }}</h4>
                  </div>
                </div>
              </v-col>
              <v-col
                cols="12"
                md="4"
                v-if="_.isArray(data.owners) && !_.isEmpty(data.owners)"
              >
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Is Sublet</label>
                  <div class="form-control-plaintext">
                    <h4>{{ data.sublet ? "yes" : "no" }}</h4>
                  </div>
                </div>
              </v-col>
              <v-col
                cols="12"
                md="4"
                v-if="_.isArray(data.owners) && !_.isEmpty(data.owners)"
              >
                <div class="form-group mb-0">
                  <label
                    class="form-label mb-0"
                    v-if="data.sublet ? true : false"
                    >Sublet Claim</label
                  >
                  <label class="form-label mb-0" v-else>Owner Claim</label>
                  <div class="form-control-plaintext">
                    <h4>
                      RM
                      {{ data.sublet ? data.sublet_claim : data.owner_claim }}
                    </h4>
                  </div>
                </div>
              </v-col>
            </v-row>
            <v-divider
              class="mx-3"
              :color="helpers.managementStyles().dividerColor"
            ></v-divider>
            <v-row justify="start" align="center" class="pa-2">
              <v-col cols="12" md="6">
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
                    :headers="propertyHeaders"
                    :items="
                      _.reverse(_.sortBy(data.properties || [], ['created_at']))
                    "
                    fixed-header
                    height="300px"
                    :items-per-page="5"
                    disable-sort
                  >
                    <template v-slot:top>
                      <v-toolbar flat color="white">
                        <v-toolbar-title
                          :class="helpers.managementStyles().subtitleClass"
                          >Property</v-toolbar-title
                        >
                        <v-spacer></v-spacer>
                      </v-toolbar>
                    </template>
                    <template v-slot:item="props">
                      <tr :key="props.item.uid">
                        <td class="text-truncate">
                          {{ props.item.text }}
                        </td>
                        <td class="text-truncate">
                          {{ _.get(props.item, "pivot.qty") || 0 }}
                        </td>
                        <td class="text-truncate">
                          {{ _.get(props.item, "pivot.remark") || 0 }}
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
                    :headers="roomCheckHeaders"
                    :items="
                      _.reverse(
                        _.sortBy(data.roomchecks || [], ['checked_date'])
                      )
                    "
                    fixed-header
                    height="300px"
                    :items-per-page="5"
                    disable-sort
                  >
                    <template v-slot:top>
                      <v-toolbar flat color="white">
                        <v-toolbar-title
                          :class="helpers.managementStyles().subtitleClass"
                          >Room Check</v-toolbar-title
                        >
                        <v-spacer></v-spacer>
                      </v-toolbar>
                    </template>
                    <template v-slot:item="props">
                      <tr
                        :key="props.item.uid"
                        @click="
                          helpers.isAccessible(
                            _.get(role, ['name']),
                            'roomCheck',
                            'view'
                          )
                            ? showRoomCheck(props.item)
                            : null
                        "
                      >
                        <td class="text-truncate">
                          {{ props.item.category }}
                        </td>
                        <td class="text-truncate">
                          {{ props.item.checked_date | formatDate }}
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
            <!-- <v-row class="pa-2" justify="end" align="center">
              <v-col cols="12">
                <div class="headline font-weight-bold">Maintenance Records</div>
              </v-col>
            </v-row>-->

            <v-row>
              <v-col
                cols="12"
                :class="helpers.managementStyles().centerWrapperClass"
              >
                <v-card raised width="100%">
                  <v-data-table
                    :headers="maintenanceHeaders"
                    :items="
                      _.reverse(_.sortBy(data.maintenances, ['created_at']))
                    "
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
                        <td
                          class="text-truncate"
                          @click="
                            helpers.isAccessible(
                              _.get(role, ['name']),
                              'roomMaintenance',
                              'view'
                            )
                              ? showMaintenance(props.item)
                              : null
                          "
                        >
                          {{ _.get(props.item, `property.text`) || "N/A" }}
                        </td>
                        <td
                          class="text-truncate"
                          @click="
                            helpers.isAccessible(
                              _.get(role, ['name']),
                              'roomMaintenance',
                              'view'
                            )
                              ? showMaintenance(props.item)
                              : null
                          "
                        >
                          {{ props.item.maintenance_type }}
                        </td>
                        <td
                          class="text-truncate"
                          @click="
                            helpers.isAccessible(
                              _.get(role, ['name']),
                              'roomMaintenance',
                              'view'
                            )
                              ? showMaintenance(props.item)
                              : null
                          "
                        >
                          {{ props.item.maintenance_status }}
                        </td>
                        <td
                          class="text-truncate"
                          @click="
                            helpers.isAccessible(
                              _.get(role, ['name']),
                              'roomMaintenance',
                              'view'
                            )
                              ? showMaintenance(props.item)
                              : null
                          "
                        >
                          {{ props.item.price }}
                        </td>
                        <td
                          class="text-truncate"
                          @click="
                            helpers.isAccessible(
                              _.get(role, ['name']),
                              'roomMaintenance',
                              'view'
                            )
                              ? showMaintenance(props.item)
                              : null
                          "
                        >
                          {{
                            _.get(props.item, ["claim_by_owner"])
                              ? _.get(props.item, ["owner", "name"]) || "N/A"
                              : _.get(props.item, ["claim_by_tenant"])
                              ? _.get(props.item, ["tenant", "name"]) || "N/A"
                              : "N/A"
                          }}
                        </td>
                        <td class="text-truncate">
                          <print-maintenance-button
                            :item="props.item"
                            v-if="props.item.paid"
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
                              )
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
                    :items="
                      _.reverse(_.sortBy(data.cleanings || [], ['created_at']))
                    "
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
                  helpers.isAccessible(_.get(role, ['name']), 'room', 'edit')
                "
              >
                <room-form
                  :editMode="true"
                  :buttonStyle="editButtonStyle"
                  :uid="this.$route.params.uid"
                  @updated="refreshPage()"
                ></room-form>
              </v-col>
              <v-col
                cols="auto"
                v-if="
                  helpers.isAccessible(_.get(role, ['name']), 'room', 'delete')
                "
              >
                <confirm-dialog
                  :activatorStyle="deleteButtonConfig.buttonStyle"
                  @confirmed="deleteRoom($event, data.uid)"
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
            :roomId="_.get(this.data, `id`) || null"
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
            :roomId="_.get(this.data, `id`) || null"
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
