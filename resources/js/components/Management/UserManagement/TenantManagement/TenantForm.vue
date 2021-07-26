

<script>
import moment from "moment";
import { validationMixin } from "vuelidate";
import {
  required,
  minLength,
  maxLength,
  sameAs,
  email,
} from "vuelidate/lib/validators";
import { mapActions } from "vuex";
import { calculateAge, _ } from "../../../../common/common-function";
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
      birthdayMenu: false,
      roomTypes: [],
      rooms: [],
      contracts: [],
      staffs: [],
      approachmethods: ["fb", "friend", "banner", "others"],
      genders: ["male", "female"],
      religions: [
        "islam",
        "buddhism",
        "christianity",
        "hinduism",
        "taoism",
        "no-region",
        "others",
      ],
      roomContractHeaders: [
        {
          text: "Room",
        },
        {
          text: "Rental",
          width: "150px",
        },
        {
          text: "Deposit",
          width: "150px",
        },
        {
          text: "Agreement Fees",
          width: "150px",
        },
        {
          text: "Partial Payment (Deposit)",
          width: "150px",
        },
        {
          text: "Outstanding Deposit",
          width: "150px",
        },
        {
          text: "Contract",
          width: "150px",
        },
        {
          text: "Contract Start Date",
          width: "150px",
        },
        {
          text: "Contract End Date",
          width: "150px",
        },
        {
          text: "Rental Start Date",
          width: "150px",
        },
        {
          text: "Services",
          width: "150px",
        },
      ],
      data: new Form({
        name: "",
        icno: "",
        tel1: "",
        tel2: "",
        tel3: "",
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
        age: 0,
        birthday: "",
        gender: "male",
        religion: "no-region",
        approach_method: "others",
        occupation: "",
        address: "",
        postcode: "",
        state: "",
        city: "",
        pic: "",
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
          tel1: {},
          tel2: {},
          tel3: {},
          email: { email },
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
          tel1: {},
          tel2: {},
          tel3: {},
          email: { email },
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
    authUser() {
      return this.$store.getters.authUser;
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

      // if (!this.helpers.isIcFormat(this.$v.data.icno.$model)) {
      //   errors.push("IC must be in format XXXXXX-XX-XXXX");
      //   return errors;
      // }
    },
    tel1Errors() {
      const errors = [];
      if (!this.$v.data.tel1.$dirty) {
        return errors;
      }

      // if (
      //   !this.helpers.isPhoneFormat(this.$v.data.tel1.$model) &&
      //   this.$v.data.tel1.$model
      // ) {
      //   errors.push("Phone must be in format 012-XXXXXXX");
      //   return errors;
      // }
    },
    tel2Errors() {
      const errors = [];
      if (!this.$v.data.tel2.$dirty) {
        return errors;
      }

      // if (
      //   !this.helpers.isPhoneFormat(this.$v.data.tel2.$model) &&
      //   this.$v.data.tel2.$model
      // ) {
      //   errors.push("Phone must be in format 012-XXXXXXX");
      //   return errors;
      // }
    },
    tel3Errors() {
      const errors = [];
      if (!this.$v.data.tel3.$dirty) {
        return errors;
      }

      // if (
      //   !this.helpers.isPhoneFormat(this.$v.data.tel3.$model) &&
      //   this.$v.data.tel3.$model
      // ) {
      //   errors.push("Phone must be in format 012-XXXXXXX");
      //   return errors;
      // }
    },
    emailErrors() {
      const errors = [];
      if (!this.$v.data.email.$dirty) {
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
        this.rooms = (data.data || []).map(function (room) {
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
          room.deposit = 700;
          room.booking_fees = 200;
          room.outstanding_deposit = room.deposit - room.booking_fees;
          room.agreement_fees = 50;
          room.autorenew = false;
          return room;
        });
        this.getContractsAction({
          pageNumber: -1,
          pageSize: -1,
        })
          .then((data) => {
            this.contracts = data.data;

            this.getStaffsAction({
              pageNumber: -1,
              pageSize: -1,
            })
              .then((data) => {
                this.staffs = data.data;
                if (this.editMode && this.uid) {
                  this.getTenantAction({ uid: this.uid })
                    .then((data) => {
                      this.data = new Form(data.data);
                      this.endLoadingAction();
                    })
                    .catch((error) => {
                      console.log(error);
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
                console.log(error);
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
      })
      .catch((error) => {
        console.log(error);
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
      getStaffsAction: "getStaffs",
      getRoomsAction: "getRooms",
      filterRoomsAction: "filterRooms",
      getRoomTypesAction: "getRoomTypes",
      getTenantAction: "getTenant",
      createTenantAction: "createTenant",
      updateTenantAction: "updateTenant",
      showLoadingAction: "showLoadingAction",
      endLoadingAction: "endLoadingAction",
    }),
    customValidate() {

      if(!this._.isEmpty(this.data.room)){
        return (this.data.room.contract_id &&
          this.data.room.startdate &&
          this.data.room.enddate &&
          this.data.room.deposit &&
          this.data.room.agreement_fees &&
          this.data.room.booking_fees)
      }

      return true;
    },

    createTenant() {
      this.$v.$touch(); //it will validate all fields

      console.log(this.customValidate());
      console.log(this.data);
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
            console.log(error);
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
            console.log(error);
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
          console.log(error);
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
    updateContractInfo(room) {
      let self = this;
      let contract = _.find(this.contracts, function (contract) {
        return contract.id == room.contract_id;
      });

      if (_.isPlainObject(contract) && !_.isEmpty(contract)) {
        if (_.get(room, ["startdate"]) && _.get(room, ["contract_id"])) {
          let duration = contract.duration || 1;
          room.enddate = moment(room.startdate)
            .add(
              duration - 1,
              contract.rental_type == "month" ? "months" : "days"
            )
            .format("YYYY-MM-DD");
        }

        if (_.get(room, ["autorenew"])) {
          room.autorenew = contract.autorenew;
        }
      }
    },
    updateOutstanding() {
      let deposit = !_.isNaN(parseFloat(_.get(this.data.room, `deposit`)))
        ? parseFloat(_.get(this.data.room, `deposit`))
        : 0;
      let agreement_fees = !_.isNaN(
        parseFloat(_.get(this.data.room, `agreement_fees`))
      )
        ? parseFloat(_.get(this.data.room, `agreement_fees`))
        : 0;
      let booking_fees = !_.isNaN(parseFloat(_.get(this.data.room, `booking_fees`)))
        ? parseFloat(_.get(this.data.room, `booking_fees`))
        : 0;
      let outstanding_deposit = deposit + agreement_fees - booking_fees;
      this.data.room = {
        ...this.data.room,
        deposit,
        agreement_fees,
        booking_fees,
        outstanding_deposit,
      };
    },
    updateAge(){
      console.log(new Date(this.data.birthday));
      console.log(calculateAge(new Date(this.data.birthday)));
      this.data.age = calculateAge(new Date(this.data.birthday))
    }
  },
};
</script>

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
        <v-icon left>{{ buttonStyle.icon }}</v-icon>
        {{ buttonStyle.text }}
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
            >Save</v-btn
          >
        </v-toolbar-items>
      </v-toolbar>
      <v-card-text>
        <v-container>
          <v-row>
            <v-col cols="12">
              <v-autocomplete
                v-model="data.pic"
                :item-text="(item) => helpers.capitalizeFirstLetter(item.name)"
                item-value="id"
                :items="staffs || []"
                label="Person In Charge"
                chips
                deletable-chips
              >
                <!-- <template v-slot:append>
                  <room-type-form
                    :editMode="false"
                    :dialogStyle="roomFormDialogConfig.dialogStyle"
                    :buttonStyle="roomFormDialogConfig.buttonStyle"
                    @created="appendRoomTypeList($event)"
                  ></room-type-form>
                </template>-->
              </v-autocomplete>
            </v-col>
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
              <v-menu
                ref="birthdayMenu"
                v-model="birthdayMenu"
                :close-on-content-click="false"
                transition="scale-transition"
                offset-y
                min-width="290px"
              >
                <template v-slot:activator="{ on, attrs }">
                  <v-text-field
                    v-model="data.birthday"
                    label="Birthday"
                    prepend-icon="mdi-calendar"
                    readonly
                    v-bind="attrs"
                    v-on="on"
                  ></v-text-field>
                </template>
                <v-date-picker v-model="data.birthday" no-title scrollable @change="updateAge">
                </v-date-picker>
              </v-menu>
            </v-col>
            <v-col cols="12" md="6">
              <v-text-field
                label="Age"
                v-model="data.age"
                type="number"
                step="1"
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="6">
              <v-autocomplete
                v-model="data.gender"
                :items="genders || []"
                label="Gender"
              ></v-autocomplete>
            </v-col>
            <v-col cols="12" md="6">
              <v-text-field
                label="Phone No 1"
                persistent-hint
                v-model="data.tel1"
                :maxlength="20"
                @input="$v.data.tel1.$touch()"
                @blur="$v.data.tel1.$touch()"
                :error-messages="tel1Errors"
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="6">
              <v-text-field
                label="Phone No 2"
                persistent-hint
                v-model="data.tel2"
                :maxlength="20"
                @input="$v.data.tel2.$touch()"
                @blur="$v.data.tel2.$touch()"
                :error-messages="tel2Errors"
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="6">
              <v-text-field
                label="Phone No 3"
                persistent-hint
                v-model="data.tel3"
                :maxlength="20"
                @input="$v.data.tel3.$touch()"
                @blur="$v.data.tel3.$touch()"
                :error-messages="tel3Errors"
              ></v-text-field>
            </v-col>
            <v-col cols="6">
              <v-text-field
                label="Email"
                persistent-hint
                required
                :maxlength="255"
                v-model="data.email"
                @input="$v.data.email.$touch()"
                @blur="$v.data.email.$touch()"
                :error-messages="emailErrors"
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="6">
              <v-autocomplete
                v-model="data.religion"
                :items="religions || []"
                label="Religion"
              ></v-autocomplete>
            </v-col>
            <v-col cols="12" md="6">
              <v-text-field
                label="Occupation"
                v-model="data.occupation"
                :maxlength="300"
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="12">
              <v-text-field
                label="Address"
                v-model="data.address"
                :maxlength="300"
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="6">
              <v-text-field
                label="Postcode"
                v-model="data.postcode"
                :maxlength="300"
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="6">
              <v-text-field
                label="City"
                v-model="data.city"
                :maxlength="300"
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="6">
              <v-text-field
                label="State"
                v-model="data.state"
                :maxlength="300"
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="6"> </v-col>
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
            <v-col cols="12" md="6">
              <v-autocomplete
                v-model="data.approach_method"
                :items="approachmethods || []"
                label="How you know us?"
              ></v-autocomplete>
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

            <v-col cols="12" md="12"  v-if="!editMode">
              <v-autocomplete
                v-model="data.room"
                :items="rooms || []"
                item-text="name"
                label="Room"
                return-object
                :disabled="editMode"
              >
                <template v-slot:append-outer>
                  <room-filter-dialog
                    :buttonStyle="roomFilterDialogConfig.buttonStyle"
                    :dialogStyle="roomFilterDialogConfig.dialogStyle"
                    @submitFilter="initRoomFilter($event)"
                  ></room-filter-dialog>
                </template>
              </v-autocomplete>
            </v-col>
            <v-col cols="12" v-if="!_.isEmpty(data.room)">
              <v-card>
                <v-data-table
                  :headers="roomContractHeaders"
                  :items="data.room ? [data.room] : []"
                  fixed-header
                  height="300px"
                  disable-sort
                  hide-default-footer
                >
                  <template v-slot:item="props">
                    <tr>
                      <td class="text-truncate">
                        {{ props.item.name }}
                      </td>
                      <td class="text-truncate">
                        <v-text-field
                          v-model="props.item.price"
                          prefix="RM"
                          type="number"
                          step="0.01"
                          :error-messages="
                            !_.isNumber(props.item.price)
                              ? 'Rental is required'
                              : ''
                          "
                        ></v-text-field>
                      </td>
                      <td class="text-truncate">
                        <v-text-field
                          v-model="props.item.deposit"
                          prefix="RM"
                          type="number"
                          step="0.01"
                          :error-messages="
                            !_.isNumber(props.item.deposit)
                              ? 'Deposit is required'
                              : ''
                          "
                          @change="updateOutstanding"
                        ></v-text-field>
                      </td>
                      <td class="text-truncate">
                        <v-text-field
                          v-model="props.item.agreement_fees"
                          prefix="RM"
                          type="number"
                          step="0.01"
                          :error-messages="
                            !_.isNumber(props.item.agreement_fees)
                              ? 'Agreement is required'
                              : ''
                          "
                          @change="updateOutstanding"
                        ></v-text-field>
                      </td>
                      <td class="text-truncate">
                        <v-text-field
                          v-model="props.item.booking_fees"
                          prefix="RM"
                          type="number"
                          step="0.01"
                          :error-messages="
                            !_.isNumber(props.item.booking_fees)
                              ? 'Booking fees is required'
                              : ''
                          "
                          @change="updateOutstanding"
                        ></v-text-field>
                      </td>
                      <td class="text-truncate">
                        <v-text-field
                          v-model="props.item.outstanding_deposit"
                          prefix="RM"
                          type="number"
                          step="0.01"
                          :error-messages="
                            helpers.isEmpty(props.item.outstanding_deposit)
                              ? 'Outstanding deposit is required'
                              : ''
                          "
                        ></v-text-field>
                      </td>
                      <td class="text-truncate">
                        <v-autocomplete
                          v-model="props.item.contract_id"
                          :items="contracts || []"
                          item-text="name"
                          item-value="id"
                          label="Contract"
                          :error-messages="
                            helpers.isEmpty(props.item.contract_id)
                              ? 'Contract is required'
                              : ''
                          "
                          @change="updateContractInfo"
                          :disabled="editMode"
                        ></v-autocomplete>
                      </td>
                      <td class="text-truncate">
                        <v-menu
                          ref="menu"
                          v-model="props.item.menu"
                          :close-on-content-click="false"
                          transition="scale-transition"
                          offset-y
                        >
                          <template v-slot:activator="{ on }">
                            <v-text-field
                              v-model="props.item.startdate"
                              label="Start Date"
                              prepend-icon="event"
                              readonly
                              v-on="on"
                              :error-messages="
                                helpers.isEmpty(props.item.startdate)
                                  ? 'Date is required'
                                  : ''
                              "
                            ></v-text-field>
                          </template>
                          <v-date-picker
                            v-model="props.item.startdate"
                            no-title
                            scrollable
                          ></v-date-picker>
                        </v-menu>
                      </td>
                      <td class="text-truncate">
                        <v-menu
                          ref="menu"
                          v-model="props.item.enddatemenu"
                          :close-on-content-click="false"
                          transition="scale-transition"
                          offset-y
                        >
                          <template v-slot:activator="{ on }">
                            <v-text-field
                              v-model="props.item.enddate"
                              label="End Date"
                              prepend-icon="event"
                              readonly
                              v-on="on"
                              :error-messages="
                                helpers.isEmpty(props.item.enddate)
                                  ? 'Date is required'
                                  : ''
                              "
                            ></v-text-field>
                          </template>
                          <v-date-picker
                            v-model="props.item.enddate"
                            no-title
                            scrollable
                          ></v-date-picker>
                        </v-menu>
                      </td>
                      <td class="text-truncate">
                        <v-menu
                          ref="menu"
                          v-model="props.item.rentalstartdatemenu"
                          :close-on-content-click="false"
                          transition="scale-transition"
                          offset-y
                        >
                          <template v-slot:activator="{ on }">
                            <v-text-field
                              v-model="props.item.rental_payment_start_date"
                              label="Rental Start Date"
                              prepend-icon="event"
                              readonly
                              v-on="on"
                            ></v-text-field>
                          </template>
                          <v-date-picker
                            v-model="props.item.rental_payment_start_date"
                            no-title
                            scrollable
                          ></v-date-picker>
                        </v-menu>
                      </td>

                      <!-- <td class="text-truncate">
                          <v-switch v-model="props.item.autorenew"></v-switch>
                        </td> -->
                      <td class="text-truncate">
                        <services-dialog
                          :dialogStyle="servicesDialogConfig.dialogStyle"
                          :services="
                            pluckUid(
                              !_.isEmpty(props.item.services)
                                ? props.item.services
                                : []
                            )
                          "
                          :origServices="
                            pluckUid(
                              !_.isEmpty(props.item.origServices)
                                ? props.item.origServices
                                : []
                            )
                          "
                          editMode
                          @submit="
                            (e) => {
                              roomServiceUpdated(props.item, e);
                            }
                          "
                        ></services-dialog>
                      </td>
                    </tr>
                  </template>
                </v-data-table>
              </v-card>
            </v-col>
          </v-row>
        </v-container>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>