<template>
  <v-dialog v-model="dialog" fullscreen hide-overlay transition="dialog-bottom-transition">
    <template v-slot:activator="{ on }">
      <v-btn
        :class="buttonStyle.class"
        tile
        :color="buttonStyle.color"
        :block="buttonStyle.block"
        v-on="on"
        :disabled="isLoading"
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
            <v-col cols="6">
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
            <v-col cols="6">
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
            <v-col cols="6">
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
            <v-col cols="12">
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
            <v-col cols="12" v-if="!editMode">
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
            </v-col>

            <v-col cols="6">
              <v-autocomplete
                v-model="data.rooms"
                :items="rooms"
                item-text="name"
                item-value="id"
                chips
                deletable-chips
                label="Room"
                multiple
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
  email
} from "vuelidate/lib/validators";
import { mapActions } from "vuex";
export default {
  props: {
    editMode: {
      type: Boolean,
      default: false
    },
    uid: {
      type: String,
      default: ""
    },
    buttonStyle: {
      type: Object,
      default: () => ({
        block: true,
        color: "primary",
        class: "ma-1",
        text: "Add Tenant",
        icon: ""
      })
    }
  },
  data() {
    return {
      dialog: false,
      roomTypes: [],
      rooms: [],
      data: new Form({
        name: "",
        icno: "",
        tel1: "",
        email: "",
        password: "",
        password_confirmation: "",
        roomTypes: [],
        rooms: []
      }),
      roomFilterGroup: new Form({
        roomTypes: [],
        pageNumber: -1,
        pageSize: -1
      }),
      roomFilterDialogConfig: {
        buttonStyle: {
          class: "ma-1",
          text: "",
          icon: "mdi-magnify",
          isIcon: true
        },
        dialogStyle: {
          persistent: true,
          maxWidth: "1200px",
          fullscreen: false,
          hideOverlay: true
        }
      }
    };
  },

  validations() {
    if (!this.editMode) {
      return {
        data: {
          name: { required, maxLength: maxLength(100) },
          icno: { required, maxLength: maxLength(14) },
          tel1: {},
          email: { required, email },
          password: { required, minLength: minLength(8) },
          password_confirmation: {
            required,
            sameAsPassword: sameAs("password")
          }
        }
      };
    } else {
      return {
        data: {
          name: { required, maxLength: maxLength(100) },
          icno: { required, maxLength: maxLength(14) },
          tel1: {},
          email: { required, email }
        }
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
    }
  },
  watch: {
    dialog: function(val) {
      if (val) {
        this.data.reset();
        this.$v.$reset();
      }
    }
  },
  created() {
    this.$vuetify.theme.dark = true;

    this.showLoadingAction();
    this.getRoomsAction({
      pageNumber: -1,
      pageSize: -1
    }).then(data => {
      this.rooms = data.data;
      if (this.editMode) {
        this.getTenantAction({ uid: this.uid })
          .then(data => {
            var ids = data.data.rentrooms.map(function(room) {
              return room.id;
            });
            //Should assign data first before creating form because the form will reset after triggered
            //Create the form before assigning the data, the form will not keep track the original/default value of data
            Object.assign(data.data, { rooms: ids });
            this.data = new Form(data.data);
            this.endLoadingAction();
          })
          .catch(error => {
            this.endLoadingAction();
          });
      } else {
        this.endLoadingAction();
      }
    });
  },
  methods: {
    ...mapActions({
      getRoomsAction: "getRooms",
      filterRoomsAction: "filterRooms",
      getRoomTypesAction: "getRoomTypes",
      getTenantAction: "getTenant",
      createTenantAction: "createTenant",
      updateTenantAction: "updateTenant",
      showLoadingAction: "showLoadingAction",
      endLoadingAction: "endLoadingAction"
    }),

    customValidate() {
      return (
        (!this.data.tel1 || this.helpers.isPhoneFormat(this.data.tel1)) &&
        this.helpers.isIcFormat(this.data.icno)
      );
    },

    createTenant() {
      this.$v.$touch(); //it will validate all fields

      if (this.$v.$invalid || !this.customValidate()) {
        Toast.fire({
          icon: "warning",
          title: "Please make sure all the data is valid. "
        });
      } else {
        this.$Progress.start();
        this.showLoadingAction();
        this.createTenantAction(this.data)
          .then(data => {
            Toast.fire({
              icon: "success",
              title: "Successful Created. "
            });
            this.$Progress.finish();
            this.endLoadingAction();
            this.$emit("created", data.data);
            this.dialog = false;
          })
          .catch(error => {
            Toast.fire({
              icon: "error",
              title: "Something went wrong. "
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
          title: "Please make sure all the data is valid. "
        });
      } else {
        this.$Progress.start();
        this.showLoadingAction();
        this.updateTenantAction(this.data)
          .then(data => {
            Toast.fire({
              icon: "success",
              title: "Successful Updated. "
            });
            this.$Progress.finish();
            this.endLoadingAction();
            this.$emit("updated", data.data);
            this.dialog = false;
          })
          .catch(error => {
            Toast.fire({
              icon: "error",
              title: "Something went wrong. "
            });
            this.$Progress.finish();
            this.endLoadingAction();
          });
      }
    },

    initRoomFilter(filterGroup) {
      this.roomFilterGroup.reset();
      this.data.rooms = [];
      if (filterGroup) {
        this.roomFilterGroup.roomTypes = filterGroup.roomTypes.map(function(
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
        .then(data => {
          if (data.data) {
            this.rooms = data.data;
          } else {
            this.rooms = [];
          }
          this.endLoadingAction();
        })
        .catch(error => {
          Toast.fire({
            icon: "error",
            title: "Something went wrong. "
          });
          this.$Progress.finish();
          this.endLoadingAction();
        });
    }
  }
};
</script>