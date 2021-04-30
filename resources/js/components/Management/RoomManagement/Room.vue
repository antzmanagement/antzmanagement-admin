<script>
import { mapActions } from "vuex";
import { _ } from "../../../common/common-function";
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
    maintenanceHeaders: [
      {
        text: "ID",
        value: "uid",
      },
      {
        text: "Property",
        value: "property.name",
      },
      { text: "Price (RM)", value: "price" },
    ],
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
        console.log("room");
        console.log(this.data);
        this.$Progress.finish();
        this.endLoadingAction();
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
      getRoomAction: "getRoom",
      deleteRoomAction: "deleteRoom",
      showLoadingAction: "showLoadingAction",
      endLoadingAction: "endLoadingAction",
    }),

    showMaintenance($data) {
      this.$router.push("/maintenance/" + $data.uid);
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
  },
};
</script>

<template>
  <v-app>
    <navbar></navbar>
    <v-content :class="helpers.managementStyles().backgroundClass">
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
                      :to="{ name: 'roomtype', params: { uid: roomType.uid } }"
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
                _.isArray(_.get(data, ['properties'])) &&
                !_.isEmpty(_.get(data, ['properties']))
              "
            >
              <v-divider
                :color="helpers.managementStyles().dividerColor"
              ></v-divider>
              <v-col cols="12">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Properties</label>
                  <div class="form-control-plaintext">
                    <v-chip
                      class="ma-2"
                      v-for="property in data.properties"
                      :key="property.uid"
                    >
                      <h4 class="text-center ma-2">
                        {{
                          _.get(property, ["name"]) ||
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
                      v-for="roomcontract in data.roomcontracts"
                      :key="roomcontract.uid"
                      :to="{
                        name: 'roomcontract',
                        params: { uid: roomcontract.uid },
                      }"
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
                      :to="{ name: 'owner', params: { uid: owner.uid } }"
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
                    <h4>{{ data.name }}</h4>
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
              <v-col cols="12" md="4" v-if="_.isArray(data.owners) && !_.isEmpty(data.owners)">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Is Sublet</label>
                  <div class="form-control-plaintext">
                    <h4>{{ data.sublet ? "yes" : "no" }}</h4>
                  </div>
                </div>
              </v-col>
              <v-col cols="12" md="4"  v-if="_.isArray(data.owners) && !_.isEmpty(data.owners)">
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
            <!-- <v-row class="pa-2" justify="end" align="center">
              <v-col cols="12">
                <div class="headline font-weight-bold">Maintenance Records</div>
              </v-col>
            </v-row>-->
            <v-row class="pa-2" justify="end" align="center">
              <v-col cols="12">
                <v-card class="pa-8" raised>
                  <v-data-table
                    :headers="maintenanceHeaders"
                    :items="data.maintenances"
                    items-per-page="5"
                    item-key="uid"
                    disable-sort
                  >
                    <template v-slot:top>
                      <v-container>
                        <v-row>
                          <v-col cols="auto">
                            <div
                              class="headline font-weight-bold text-left mb-5 d-inline-block"
                            >
                              Maintenance Records
                            </div>
                          </v-col>
                          <v-spacer></v-spacer>
                          <v-col cols="auto">
                            <maintenance-form
                              :editMode="false"
                              :buttonStyle="
                                maintenanceFormDialogConfig.buttonStyle
                              "
                              :dialogStyle="
                                maintenanceFormDialogConfig.dialogStyle
                              "
                              :roomId="data.id"
                              @created="refreshPage()"
                            ></maintenance-form>
                          </v-col>
                        </v-row>
                      </v-container>
                    </template>
                    <template v-slot:item="props">
                      <tr @click="showMaintenance(props.item)">
                        <td>{{ props.item.uid }}</td>
                        <td>{{ props.item.property.name }}</td>
                        <td>{{ props.item.price }}</td>
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
                  @confirmed="deleteRoom($event, data.uid)"
                ></confirm-dialog>
              </v-col>
            </v-row>
          </v-container>
        </v-card>
      </v-container>
    </v-content>
  </v-app>
</template>
