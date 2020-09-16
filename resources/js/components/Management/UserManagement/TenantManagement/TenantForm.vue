<template>
  <v-dialog
    v-model="dialog"
    fullscreen
    hide-overlay
    persistent
    transition="dialog-bottom-transition"
  >
    <template v-slot:activator="{ on }">
      <v-btn
        :class="buttonStyle.class"
        tile
        :color="buttonStyle.color"
        :block="buttonStyle.block"
        v-on="on"
        :disabled="isLoading"
        :elevation="buttonStyle.elevation"
      >
        <v-icon left>{{buttonStyle.icon}}</v-icon>
        {{buttonStyle.text}}
      </v-btn>
    </template>
    <v-card>
      <v-toolbar dark color="primary">
        <v-btn icon dark @click="dialog = false">
          <v-icon>mdi-close</v-icon>
        </v-btn>
        <v-toolbar-title v-if="!editMode">Add Tenant</v-toolbar-title>
        <v-toolbar-title v-else>Edit Tenant</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-toolbar-items>
          <v-btn
            dark
            text
            :disabled="isLoading"
            @click="editMode ? updateTenant() : createTenant()"
          >Save</v-btn>
        </v-toolbar-items>
      </v-toolbar>
      <v-card-text>
        <v-container>
          <v-row>
            <v-col cols="12" md="6">
              <v-text-field
                label="Name*"
                required
                :maxlength="100"
                v-model="data.name"
                @input="$v.data.name.$touch()"
                @blur="$v.data.name.$touch()"
                :error-messages="nameErrors"
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="6">
              <v-text-field
                label="IC-No*"
                hint="Example of IC-No : 1234-56-7890 (With Dash)"
                persistent-hint
                required
                :maxlength="14"
                v-model="data.icno"
                @input="$v.data.icno.$touch()"
                @blur="$v.data.icno.$touch()"
                :error-messages="icnoErrors"
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="6">
              <v-text-field
                label="Phone No"
                hint="Example of Phone No : 014-12019231 (With Dash)"
                persistent-hint
                v-model="data.tel1"
                :maxlength="20"
                @input="$v.data.tel1.$touch()"
                @blur="$v.data.tel1.$touch()"
                :error-messages="tel1Errors"
              ></v-text-field>
            </v-col>
            <v-col cols="6">
              <v-text-field
                label="Email*"
                hint="Email that used to login the website"
                persistent-hint
                required
                :maxlength="255"
                v-model="data.email"
                @input="$v.data.email.$touch()"
                @blur="$v.data.email.$touch()"
                :error-messages="emailErrors"
              ></v-text-field>
            </v-col>
            <v-col cols="6">
              <v-text-field
                label="Mother Name"
                required
                :maxlength="255"
                v-model="data.mother_name"
                @input="$v.data.mother_name.$touch()"
                @blur="$v.data.mother_name.$touch()"
              ></v-text-field>
            </v-col>
            <v-col cols="6">
              <v-text-field
                label="Mother Contact"
                required
                :maxlength="255"
                v-model="data.mother_tel"
                @input="$v.data.mother_tel.$touch()"
                @blur="$v.data.mother_tel.$touch()"
              ></v-text-field>
            </v-col>
            <v-col cols="6">
              <v-text-field
                label="Father Name"
                required
                :maxlength="255"
                v-model="data.father_name"
                @input="$v.data.father_name.$touch()"
                @blur="$v.data.father_name.$touch()"
              ></v-text-field>
            </v-col>
            <v-col cols="6">
              <v-text-field
                label="Father Contact"
                required
                :maxlength="255"
                v-model="data.father_tel"
                @input="$v.data.father_tel.$touch()"
                @blur="$v.data.father_tel.$touch()"
              ></v-text-field>
            </v-col>
            <v-col cols="6">
              <v-text-field
                label="Emergency Contact Person"
                required
                :maxlength="255"
                v-model="data.emergency_name"
                @input="$v.data.emergency_name.$touch()"
                @blur="$v.data.emergency_name.$touch()"
              ></v-text-field>
            </v-col>
            <v-col cols="6">
              <v-text-field
                label="Emergency Contact"
                required
                :maxlength="255"
                v-model="data.emergency_contact"
                @input="$v.data.emergency_contact.$touch()"
                @blur="$v.data.emergency_contact.$touch()"
              ></v-text-field>
            </v-col>
            <v-col cols="6">
              <v-text-field
                label="Emergency Person Relationship"
                required
                :maxlength="255"
                v-model="data.emergency_relationship"
                @input="$v.data.emergency_relationship.$touch()"
                @blur="$v.data.emergency_relationship.$touch()"
              ></v-text-field>
            </v-col>
            <!-- <v-col cols="12" v-if="!editMode">
              <v-text-field
                label="Password*"
                hint="Password should be more than 8 characters."
                persistent-hint
                type="password"
                required
                :maxlength="255"
                v-model="data.password"
                @input="$v.data.password.$touch()"
                @blur="$v.data.password.$touch()"
                :error-messages="passwordErrors"
              ></v-text-field>
            </v-col>
            <v-col cols="12" v-if="!editMode">
              <v-text-field
                label="Password Confirmation*"
                type="password"
                required
                :maxlength="255"
                v-model="data.password_confirmation"
                @input="$v.data.password_confirmation.$touch()"
                @blur="$v.data.password_confirmation.$touch()"
                :error-messages="passwordConfirmErrors"
              ></v-text-field>
            </v-col>-->

            <v-col cols="12" md="12" v-if="!editMode">
              <v-autocomplete
                v-model="data.rooms"
                :items="rooms"
                item-text="name"
                label="Room"
                multiple
                chips
                deletable-chips
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
          </v-row>
          <v-row v-show="!helpers.isEmpty(data.rooms)">
            <v-col cols="12">
              <v-card>
                <v-simple-table fixed-header height="300px">
                  <template v-slot:default>
                    <thead>
                      <tr>
                        <th class="text-left">Room</th>
                        <th class="text-left">Price</th>
                        <th class="text-left">Deposit</th>
                        <th class="text-left">Booking Fees</th>
                        <th class="text-left">Contract</th>
                        <th class="text-left">Contract Start Date</th>
                        <th class="text-left">Services</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="(room) in data.rooms" :key="room.uid">
                        <td>{{ room.name }}</td>
                        <td>
                          <v-text-field v-model="room.price" prefix="RM" type="number" step="0.01"></v-text-field>
                        </td>
                        <td>
                          <v-text-field v-model="room.deposit" prefix="RM" type="number" step="0.01"></v-text-field>
                        </td>
                        <td>
                          <v-text-field v-model="room.booking_fees" prefix="RM" type="number" step="0.01"></v-text-field>
                        </td>
                        <td>
                          <v-autocomplete
                            v-model="room.contract_id"
                            :items="contracts"
                            item-text="name"
                            item-value="id"
                            label="Contract"
                            :error-messages="helpers.isEmpty(room.contract_id) ? 'Contract is required' : ''"
                          ></v-autocomplete>
                        </td>
                        <td>
                          <v-menu
                            ref="menu"
                            v-model="room.menu"
                            :close-on-content-click="true"
                            transition="scale-transition"
                            offset-y
                          >
                            <template v-slot:activator="{ on }">
                              <v-text-field
                                v-model="room.contractstartdate"
                                label="Start Date"
                                prepend-icon="event"
                                readonly
                                v-on="on"
                                :error-messages="helpers.isEmpty(room.contractstartdate) ? 'Date is required' : ''"
                              ></v-text-field>
                            </template>
                            <v-date-picker v-model="room.contractstartdate" no-title scrollable></v-date-picker>
                          </v-menu>
                        </td>
                        <td>
                          <services-dialog
                            :dialogStyle="servicesDialogConfig.dialogStyle"
                            :buttonStyle="servicesDialogConfig.buttonStyle"
                            :services="pluckUid(room.services)"
                            :origServices="pluckUid(room.origServices)"
                            editMode
                            @submit="(e) => {roomServiceUpdated(room , e)}"
                          ></services-dialog>
                        </td>
                      </tr>
                    </tbody>
                  </template>
                </v-simple-table>
              </v-card>
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
  sameAs,
  email,
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
    buttonStyle: {
      type: Object,
      default: () => ({
        block: true,
        color: "primary",
        class: "ma-1",
        text: "Add Tenant",
        icon: "mdi-plus",
        elevation: 5,
      }),
    },
  },
  data() {
    return {
      dialog: false,
      menu: false,
      roomTypes: [],
      rooms: [],
      contracts: [],
      data: new Form({
        name: "",
        icno: "",
        tel1: "",
        email: "",
        mother_name: "",
        mother_tel: "",
        father_name: "",
        father_tel: "",
        emergency_name: "",
        emergency_contact: "",
        emergency_relationship: "",
        roomTypes: [],
        rooms: [],
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
    if (!this.editMode) {
      return {
        data: {
          name: { required, maxLength: maxLength(100) },
          icno: { required, maxLength: maxLength(14) },
          tel1: {required},
          email: { required, email },
          // password: { required, minLength: minLength(8) },
          // password_confirmation: {
          //   required,
          //   sameAsPassword: sameAs("password"),
          // },
          mother_name: {},
          mother_tel: {},
          father_name: {},
          mother_tel: {},
          emergency_name: {},
          emergency_contact: {},
          emergency_relationship: {},
        },
      };
    } else {
      return {
        data: {
          name: { required, maxLength: maxLength(100) },
          icno: { required, maxLength: maxLength(14) },
          tel1: { required},
          email: { required, email },
          mother_name: {},
          mother_tel: {},
          father_name: {},
          mother_tel: {},
          emergency_name: {},
          emergency_contact: {},
          emergency_relationship: {},
        },
      };
    }
  },

  computed: {
    isLoading() {
      return this.$store.getters.isLoading;
    },
    nameErrors() {
      const errors = [];
      if (!this.$v.data.name.$dirty) {
        return errors;
      }

      if (!this.$v.data.name.required) {
        errors.push("Name is required");
        return errors;
      }
    },
    icnoErrors() {
      const errors = [];
      if (!this.$v.data.icno.$dirty) {
        return errors;
      }

      if (!this.$v.data.icno.required) {
        errors.push("Ic No is required");
        return errors;
      }

      if (!this.helpers.isIcFormat(this.$v.data.icno.$model)) {
        errors.push("IC must be in format XXXXXX-XX-XXXX");
        return errors;
      }
    },
    tel1Errors() {
      const errors = [];
      if (!this.$v.data.tel1.$dirty) {
        return errors;
      }
      if (!this.$v.data.tel1.required) {
        errors.push("Contact is required");
        return errors;
      }


      if (
        !this.helpers.isPhoneFormat(this.$v.data.tel1.$model) &&
        this.$v.data.tel1.$model
      ) {
        errors.push("Phone must be in format 012-XXXXXXX");
        return errors;
      }
    },
    emailErrors() {
      const errors = [];
      if (!this.$v.data.email.$dirty) {
        return errors;
      }
      if (!this.$v.data.email.required) {
        errors.push("E-mail is required");
        return errors;
      }

      if (!this.$v.data.email.email) {
        errors.push("Must be valid e-mail");
        return errors;
      }
    },
    passwordErrors() {
      const errors = [];
      if (!this.$v.data.password.$dirty) {
        return errors;
      }

      if (!this.$v.data.password.required) {
        errors.push("Password is required");
        return errors;
      }

      if (!this.$v.data.password.minLength) {
        errors.push("Password is too short");
        return errors;
      }

      if (!this.$v.data.password.minLength) {
        errors.push("Password should be more than 6 characters");
        return errors;
      }
    },

    passwordConfirmErrors() {
      const errors = [];
      if (!this.$v.data.password_confirmation.$dirty) {
        return errors;
      }

      if (!this.$v.data.password_confirmation.required) {
        errors.push("Password is required");
        return errors;
      }

      if (!this.$v.data.password_confirmation.sameAsPassword) {
        errors.push("Password Confirmation didn't match");
        return errors;
      }
    },
  },
  watch: {
    dialog: function (val) {
      if (val) {
        this.data.reset();
        this.$v.$reset();
      }
    },
  },
  created() {
    this.showLoadingAction();
    this.getRoomsAction({
      pageNumber: -1,
      pageSize: -1,
    })
      .then((data) => {
        this.rooms = data.data.map(function (room) {
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
          room.price = parseFloat(room.price);
          room.origPrice = parseFloat(room.price);
          room.deposit = parseFloat(room.price) * 2.5;
          room.booking_fees = 200;
          return room;
        });
        this.getContractsAction({
          pageNumber: -1,
          pageSize: -1,
        })
          .then((data) => {
            this.contracts = data.data;

            if (this.editMode && this.uid) {
              this.getTenantAction({ uid: this.uid })
                .then((data) => {
                  data.data.rooms = [];
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
            } else {
              this.endLoadingAction();
            }
          })
          .catch((error) => {
            this.endLoadingAction();
            Toast.fire({
              icon: "warning",
              title: "Something went wrong... ",
            });
          });
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
      getContractsAction: "getContracts",
      getRoomsAction: "getRooms",
      filterRoomsAction: "filterRooms",
      getRoomTypesAction: "getRoomTypes",
      getTenantAction: "getTenant",
      createTenantAction: "createTenant",
      updateTenantAction: "updateTenant",
      showLoadingAction: "showLoadingAction",
      endLoadingAction: "endLoadingAction",
    }),
    appendRoomList($data) {
      this.rooms.push($data);
      this.data.rooms.push($data);
    },
    customValidate() {
      for (var x = 0; x < this.data.rooms.length; x++) {
        if (
          this.helpers.isEmpty(this.data.rooms[x].contract_id) ||
          this.helpers.isEmpty(this.data.rooms[x].contractstartdate)
        ) {
          return false;
        }
      }
      if (
        (!this.data.tel1 &&!this.helpers.isPhoneFormat(this.data.tel1)) ||
        !this.helpers.isIcFormat(this.data.icno)
      ) {
        return false;
      }

      return true;
    },

    createTenant() {
      this.$v.$touch(); //it will validate all fields

      if (this.$v.$invalid || !this.customValidate()) {
        Toast.fire({
          icon: "warning",
          title: "Please make sure all the data is valid. ",
        });
      } else {
        this.$Progress.start();
        this.showLoadingAction();
        this.createTenantAction(this.data)
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

    updateTenant() {
      this.$v.$touch(); //it will validate all fields

      if (this.$v.$invalid || !this.customValidate()) {
        Toast.fire({
          icon: "warning",
          title: "Please make sure all the data is valid. ",
        });
      } else {
        this.$Progress.start();
        this.showLoadingAction();
        this.updateTenantAction(this.data)
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

      let price = room.origPrice;
      newAddedServices.forEach((service) => {
        price += parseFloat(service.price);
      });

      room.price = price;
    },

    initRoomFilter(filterGroup) {
      this.roomFilterGroup.reset();
      this.data.rooms = [];
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