<template>
  <v-dialog
    v-model="dialog"
    :fullscreen="dialogStyle.fullscreen"
    :hide-overlay="dialogStyle.hideOverlay"
    :persistent="dialogStyle.persistent"
    :max-width="dialogStyle.maxWidth"
    transition="dialog-bottom-transition"
  >
    <template v-slot:activator="{ on }">
      <v-btn
        :class="buttonStyle.class"
        tile
        :color="buttonStyle.color"
        :block="buttonStyle.block"
        :elevation="buttonStyle.elevation"
        v-on="on"
        :icon="buttonStyle.isIcon"
        :disabled="isLoading"
      >
        <v-icon>{{buttonStyle.icon}}</v-icon>
        {{buttonStyle.text}}
      </v-btn>
    </template>
    <v-card>
      <v-toolbar dark color="primary">
        <v-btn icon dark @click="dialog = false">
          <v-icon>mdi-close</v-icon>
        </v-btn>
        <v-toolbar-title v-if="!editMode">Add RoomContract</v-toolbar-title>
        <v-toolbar-title v-else>Edit RoomContract</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-toolbar-items>
          <v-btn
            dark
            text
            :disabled="isLoading"
            @click="editMode ? updateRoomContract() : createRoomContract()"
          >Save</v-btn>
        </v-toolbar-items>
      </v-toolbar>
      <v-card-text>
        <v-container>
          <v-row>
            <v-col cols="12" md="12">
              <v-autocomplete
                v-model="data.tenant"
                :items="tenants"
                item-value="id"
                item-text="name"
                label="Tenant"
                :error-messages="helpers.isEmpty(data.tenant) ? 'Tenant is required' : ''"
              >
                <!-- <template v-slot:append>
                  <room-form
                    :editMode="false"
                    :dialogStyle="roomFormDialogConfig.dialogStyle"
                    :buttonStyle="roomFormDialogConfig.buttonStyle"
                    @created="appendRoomList($event)"
                  ></room-form>
                </template>-->
                <template v-slot:append-outer>
                  <tenant-filter-dialog
                    :buttonStyle="roomFilterDialogConfig.buttonStyle"
                    :dialogStyle="roomFilterDialogConfig.dialogStyle"
                    @submitFilter="initTenantFilter($event)"
                  ></tenant-filter-dialog>
                </template>
              </v-autocomplete>
            </v-col>
            <v-col cols="12" md="12">
              <v-autocomplete
                v-model="data.room"
                :items="rooms"
                item-text="name"
                label="Room"
                :error-messages="isEmpty(data.room) ? 'Room is required' : ''"
                return-object
              >
                <!-- <template v-slot:append>
                  <room-form
                    :editMode="false"
                    :dialogStyle="roomFormDialogConfig.dialogStyle"
                    :buttonStyle="roomFormDialogConfig.buttonStyle"
                    @created="appendRoomList($event)"
                  ></room-form>
                </template>-->
                <template v-slot:append-outer>
                  <room-filter-dialog
                    :buttonStyle="roomFilterDialogConfig.buttonStyle"
                    :dialogStyle="roomFilterDialogConfig.dialogStyle"
                    @submitFilter="initRoomFilter($event)"
                  ></room-filter-dialog>
                </template>
              </v-autocomplete>
            </v-col>
            <v-col cols="12">
              <v-card>
                <v-simple-table fixed-header height="300px">
                  <template v-slot:default>
                    <thead>
                      <tr>
                        <th class="text-left">Room</th>
                        <th class="text-left">Rental</th>
                        <th class="text-left">Deposit</th>
                        <th class="text-left">Booking Fees</th>
                        <th class="text-left" v-if="editMode">Outstanding Deposit</th>
                        <th class="text-left">Contract</th>
                        <th class="text-left">Contract Start Date</th>
                        <th class="text-left">Services</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-if="!isEmpty(data.room)">
                        <td>{{ data.room.name }}</td>
                        <td>
                          <v-text-field
                            v-model="data.room.price"
                            prefix="RM"
                            type="number"
                            step="0.01"
                            :error-messages="helpers.isEmpty(data.room.price) ? 'Rental is required' : ''"
                          ></v-text-field>
                        </td>
                        <td>
                          <v-text-field
                            v-model="data.room.deposit"
                            prefix="RM"
                            type="number"
                            step="0.01"
                            :error-messages="helpers.isEmpty(data.room.deposit) ? 'Deposit is required' : ''"
                          ></v-text-field>
                        </td>
                        <td>
                          <v-text-field
                            v-model="data.room.booking_fees"
                            prefix="RM"
                            type="number"
                            step="0.01"
                            :error-messages="helpers.isEmpty(data.room.booking_fees) ? 'Booking fees is required' : ''"
                          ></v-text-field>
                        </td>
                        <td v-if="editMode">
                          <v-text-field
                            v-model="data.room.outstanding_deposit"
                            prefix="RM"
                            type="number"
                            step="0.01"
                            :error-messages="helpers.isEmpty(data.room.outstanding_deposit) ? 'Outstanding deposit is required' : ''"
                          ></v-text-field>
                        </td>
                        <td>
                          <v-autocomplete
                            v-model="data.room.contract_id"
                            :items="contracts"
                            item-text="name"
                            item-value="id"
                            label="Contract"
                            :error-messages="helpers.isEmpty(data.room.contract_id) ? 'Contract is required' : ''"
                          ></v-autocomplete>
                        </td>
                        <td>
                          <v-menu
                            ref="menu"
                            v-model="data.room.menu"
                            :close-on-content-click="true"
                            transition="scale-transition"
                            :disabled="editMode"
                            offset-y
                          >
                            <template v-slot:activator="{ on }">
                              <v-text-field
                                v-model="data.room.contractstartdate"
                                label="Start Date"
                                prepend-icon="event"
                                readonly
                                v-on="on"
                                :error-messages="helpers.isEmpty(data.room.contractstartdate) ? 'Date is required' : ''"
                              ></v-text-field>
                            </template>
                            <v-date-picker
                              v-model="data.room.contractstartdate"
                              no-title
                              scrollable
                            ></v-date-picker>
                          </v-menu>
                        </td>
                        <td>
                          <services-dialog
                            :dialogStyle="servicesDialogConfig.dialogStyle"
                            :buttonStyle="servicesDialogConfig.buttonStyle"
                            :services="pluckUid(!isEmpty(data.room.services) ? data.room.services : [])"
                            :origServices="pluckUid(!isEmpty(data.room.origServices) ? data.room.origServices : [])"
                            editMode
                            @submit="(e) => {roomServiceUpdated(data.room , e)}"
                          ></services-dialog>
                        </td>
                      </tr>
                    </tbody>
                  </template>
                </v-simple-table>
              </v-card>
            </v-col>
          </v-row>

          <v-row>
            <v-col cols="12" md="12">
              <v-textarea
                label="Remark"
                :maxlength="2500"
                v-model="data.remark"
                @input="$v.data.remark.$touch()"
                @blur="$v.data.remark.$touch()"
                :error-messages="remarkErrors"
              ></v-textarea>
            </v-col>
          </v-row>
        </v-container>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>

<script>
import { validationMixin } from "vuelidate";
import {
  required,
  minLength,
  maxLength,
  decimal,
} from "vuelidate/lib/validators";
import { mapActions } from "vuex";
export default {
  props: {
    editMode: {
      type: Boolean,
      default: false,
    },
    uid: {
      type: String,
      default: "",
    },

    roomId: {
      type: Number,
      default: () => null,
    },
    buttonStyle: {
      type: Object,
      default: () => ({
        block: true,
        color: "primary",
        class: "ma-1",
        text: "Add RoomContract",
        icon: "",
        elevation: 5,
        isIcon: false,
      }),
    },
    dialogStyle: {
      type: Object,
      default: () => ({
        persistent: true,
        maxWidth: "",
        fullscreen: true,
        hideOverlay: true,
      }),
    },
  },
  data() {
    return {
      dialog: false,
      rooms: [],
      tenants: [],
      contracts: [],
      properties: [],
      data: new Form({
        tenant: "",
        remark: "",
        room: {},
      }),
      roomFormDialogConfig: {
        dialogStyle: {
          persistent: true,
          maxWidth: "600px",
          fullscreen: false,
          hideOverlay: true,
        },
        buttonStyle: {
          block: false,
          color: "primary",
          class: "ma-4",
          text: "Add New Room",
          icon: "mdi-plus",
        },
      },
      roomFilterGroup: new Form({
        roomTypes: [],
        pageNumber: -1,
        pageSize: -1,
      }),
      tenantFilterGroup: new Form({
        keyword: "",
        roomTypes: [],
        pageNumber: -1,
        pageSize: -1,
      }),
      roomFilterDialogConfig: {
        buttonStyle: {
          class: "ma-1",
          text: "",
          icon: "mdi-magnify",
          isIcon: true,
        },
        dialogStyle: {
          persistent: true,
          maxWidth: "1200px",
          fullscreen: false,
          hideOverlay: true,
        },
      },
      servicesDialogConfig: {
        buttonStyle: {
          class: "ma-1",
          text: "",
          icon: "mdi-filter-menu",
          isIcon: true,
        },
        dialogStyle: {
          persistent: true,
          maxWidth: "1200px",
          fullscreen: false,
          hideOverlay: true,
        },
      },
    };
  },

  validations() {
    return {
      data: {
        tenant: { required },
        room: { required },
        remark: { maxLength: maxLength(2500) },
      },
    };
  },

  computed: {
    isLoading() {
      return this.$store.getters.isLoading;
    },
    tenantErrors() {
      const errors = [];
      if (!this.$v.data.price.$dirty) {
        return errors;
      }

      if (!this.$v.data.tenant.required) {
        errors.push("Tenant is required");
        return errors;
      }
    },
    remarkErrors() {
      const errors = [];
      if (!this.$v.data.remark.$dirty) {
        return errors;
      }

      if (!this.$v.data.remark.maxLength) {
        errors.push("Remark should be less than 2500 characters");
        return errors;
      }
    },
    roomsErrors() {
      const errors = [];

      if (this.helpers.isEmpty(this.data.room)) {
        errors.push("Room is required");
      }
      return errors;
    },
  },
  watch: {
    dialog: function (val) {
      if (val) {
        this.data.reset();
        this.$v.$reset();

        if (this.roomId) {
          this.data.room.push(this.roomId);
        }
      }
    },
  },
  mounted() {
    this.showLoadingAction();
    let promises = [];
    promises.push(this.getRoomsAction({ pageNumber: -1, pageSize: -1 }));
    promises.push(this.getContractsAction({ pageNumber: -1, pageSize: -1 }));
    promises.push(this.getTenantsAction({ pageNumber: -1, pageSize: -1 }));

    Promise.all(promises)
      .then(([rooms, contracts, tenants]) => {
        this.rooms = rooms.data.map(function (room) {
          if (
            room.room_types.length > 0 &&
            room.room_types[0].services.length > 0
          ) {
            room.services = room.room_types[0].services;
            room.origServices = room.room_types[0].services;
          } else {
            room.services = [];
            room.origServices = [];
          }
          room.origPrice = parseFloat(room.price);
          room.price = parseFloat(room.price);
          room.deposit = parseFloat(room.price) * 2.5;
          room.booking_fees = 200;
          return room;
        });

        this.contracts = contracts.data;
        this.tenants = tenants.data;
        this.endLoadingAction();

        if (this.editMode && this.uid) {
          this.showLoadingAction();
          this.getRoomContractAction({ uid: this.uid })
            .then((data) => {
              data.data.tenant = data.data.tenant.id;
              data.data.room.origServices = data.data.origservices;
              data.data.room.services = this._.concat(data.data.origservices , data.data.addonservices);
              data.data.room.contract_id = data.data.contract.id;
              data.data.contractstartdate = data.data.startdate;
              data.data.room.deposit = parseFloat(data.data.deposit);
              data.data.room.booking_fees = parseFloat(data.data.booking_fees);
              data.data.room.outstanding_deposit = parseFloat(data.data.outstanding_deposit);
              data.data.room.origPrice = parseFloat(data.data.room.price)
              data.data.room.price = parseFloat(data.data.rental);
              data.data.room.contractstartdate = data.data.startdate;
              this.data = new Form(data.data);
              this.endLoadingAction();
            })
            .catch((error) => {
              Toast.fire({
                icon: "warning",
                title: "Something went wrong... ",
              });
              this.endLoadingAction();
            });
        }
      })
      .catch((error) => {
        this.endLoadingAction();
        Toast.fire({
          icon: "warning",
          title: "Something went wrong... ",
        });
      });
  },
  methods: {
    ...mapActions({
      getRoomsAction: "getRooms",
      getTenantsAction: "getTenants",
      getContractsAction: "getContracts",
      getRoomContractAction: "getRoomContract",
      filterRoomsAction: "filterRooms",
      filterTenantsAction: "filterTenants",
      createRoomContractAction: "createRoomContract",
      updateRoomContractAction: "updateRoomContract",
      showLoadingAction: "showLoadingAction",
      endLoadingAction: "endLoadingAction",
    }),
    isEmpty(data) {
      return this._.isEmpty(data);
    },
    customValidate() {
      return (
        !this._.isEmpty(this.data.room) ||
        !this._.isEmpty(this.data.room.contract_id) ||
        !this.data.room.contractstartdate ||
        !this.data.room.deposit ||
        !this.data.room.booking_fees
      );
    },
    createRoomContract() {
      this.$v.$touch(); //it will validate all fields

      if (this.$v.$invalid || !this.customValidate()) {
        Toast.fire({
          icon: "warning",
          title: "Please make sure all the data is valid. ",
        });
      } else {
        this.$Progress.start();
        this.showLoadingAction();
        this.createRoomContractAction(this.data)
          .then((data) => {
            Toast.fire({
              icon: "success",
              title: "Successful Created. ",
            });
            this.$Progress.finish();
            this.endLoadingAction();
            this.$emit("created", data.data);
            this.dialog = false;
          })
          .catch((error) => {
            Toast.fire({
              icon: "error",
              title: "Something went wrong. ",
            });
            this.$Progress.finish();
            this.endLoadingAction();
          });
      }
    },

    updateRoomContract() {
      this.$v.$touch(); //it will validate all fields

      if (this.$v.$invalid) {
        Toast.fire({
          icon: "warning",
          title: "Please make sure all the data is valid. ",
        });
      } else {
        this.$Progress.start();
        this.showLoadingAction();
        this.updateRoomContractAction(this.data)
          .then((data) => {
            Toast.fire({
              icon: "success",
              title: "Successful Updated. ",
            });
            this.$Progress.finish();
            this.endLoadingAction();
            this.$emit("updated", data.data);
            this.dialog = false;
          })
          .catch((error) => {
            Toast.fire({
              icon: "error",
              title: "Something went wrong. ",
            });
            this.$Progress.finish();
            this.endLoadingAction();
          });
      }
    },
    // appendRoomList($data) {
    //   this.rooms.push($data);
    //   if (!this.editMode) {
    //     this.data.room.push($data.id);
    //   } else {
    //     this.data.room = $data.id;
    //   }
    // },
    roomServiceUpdated(room, event) {
      room.services = event.services;
      let roomServices = event.services;
      let roomOrigServices = room.origServices;

      let newAddedServices = roomServices.filter(function (service) {
        let existedService = roomOrigServices.some(function (origService) {
          return origService.uid == service.uid;
        });

        return !existedService;
      });

      let price = parseFloat(room.origPrice);
      newAddedServices.forEach((service) => {
        price += parseFloat(service.price);
      });

      room.price = price;
    },
    initRoomFilter(filterGroup) {
      this.roomFilterGroup.reset();
      //Clear selection
      this.data.room = {};
      if (filterGroup) {
        this.roomFilterGroup.roomTypes = filterGroup.roomTypes.map(function (
          roomType
        ) {
          return roomType.id;
        });
      }
      this.applyRoomFilter();
    },

    applyRoomFilter() {
      this.showLoadingAction();
      this.filterRoomsAction(this.roomFilterGroup)
        .then((data) => {
          if (data.data) {
            this.rooms = data.data;
          } else {
            this.rooms = [];
          }
          this.endLoadingAction();
        })
        .catch((error) => {
          Toast.fire({
            icon: "error",
            title: "Something went wrong. ",
          });
          this.$Progress.finish();
          this.endLoadingAction();
        });
    },
    initTenantFilter(filterGroup) {
      this.tenantFilterGroup.reset();
      //Clear selection
      this.data.room = {};
      if (filterGroup) {
        this.tenantFilterGroup.roomTypes = filterGroup.roomTypes.map(function (
          roomType
        ) {
          return roomType.id;
        });
      }

      this.tenantFilterGroup.keyword = filterGroup.keyword;
      this.applyTenantFilter();
    },
    applyTenantFilter() {
      this.showLoadingAction();
      this.filterTenantsAction(this.tenantFilterGroup)
        .then((data) => {
          if (data.data) {
            this.tenants = data.data;
          } else {
            this.tenants = [];
          }
          this.endLoadingAction();
        })
        .catch((error) => {
          Toast.fire({
            icon: "error",
            title: "Something went wrong. ",
          });
          this.$Progress.finish();
          this.endLoadingAction();
        });
    },

    applyRoomFilter() {
      this.showLoadingAction();
      this.filterRoomsAction(this.roomFilterGroup)
        .then((data) => {
          if (data.data) {
            this.rooms = data.data;
          } else {
            this.rooms = [];
          }
          this.endLoadingAction();
        })
        .catch((error) => {
          Toast.fire({
            icon: "error",
            title: "Something went wrong. ",
          });
          this.$Progress.finish();
          this.endLoadingAction();
        });
    },

    pluckUid(data) {
      if (data.length > 0) {
        return data.map(function (item) {
          return item.uid;
        });
      } else {
        return [];
      }
    },
  },
};
</script>