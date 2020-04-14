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
        <v-toolbar-title v-if="!editMode">Add Room</v-toolbar-title>
        <v-toolbar-title v-else>Edit Room</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-toolbar-items>
          <v-btn
            dark
            text
            :disabled="isLoading"
            @click="editMode ? updateRoom() : createRoom()"
          >Save</v-btn>
        </v-toolbar-items>
      </v-toolbar>
      <v-card-text>
        <v-container>
          <v-row>
            <v-col cols="12">
              <v-select
                v-model="data.roomTypes"
                :item-text="item => helpers.capitalizeFirstLetter(item.name)"
                item-value="id"
                :items="roomTypes"
                label="Room Type"
                chips
                multiple
                @change="updateFormDetails()"
              >
                <template v-slot:append>
                  <room-type-form
                    :editMode="false"
                    :dialogStyle="formDialogStyle"
                    :buttonStyle="formButtonStyle"
                    @created="appendRoomTypeList($event)"
                  ></room-type-form>
                </template>
              </v-select>
            </v-col>
            <v-col cols="6">
              <v-text-field
                label="Unit No*"
                required
                :maxlength="300"
                v-model="data.name"
                @input="$v.data.name.$touch()"
                @blur="$v.data.name.$touch()"
                :error-messages="nameErrors"
              ></v-text-field>
            </v-col>
            <v-col cols="6">
              <v-text-field
                label="Price"
                type="number"
                step="0.01"
                required
                :maxlength="100"
                v-model="data.price"
                @input="$v.data.price.$touch()"
                @blur="$v.data.price.$touch()"
                :error-messages="priceErrors"
              ></v-text-field>
            </v-col>
            <v-col cols="12">
              <v-text-field
                label="Address"
                required
                :maxlength="300"
                v-model="data.address"
                @input="$v.data.address.$touch()"
                @blur="$v.data.address.$touch()"
                :error-messages="addressErrors"
              ></v-text-field>
            </v-col>
            <v-col cols="6">
              <v-text-field
                label="State"
                required
                :maxlength="300"
                v-model="data.state"
                @input="$v.data.state.$touch()"
                @blur="$v.data.state.$touch()"
                :error-messages="stateErrors"
              ></v-text-field>
            </v-col>
            <v-col cols="6">
              <v-text-field
                label="Postcode"
                v-model="data.postcode"
                :maxlength="300"
                @input="$v.data.postcode.$touch()"
                @blur="$v.data.postcode.$touch()"
                :error-messages="postcodeErrors"
              ></v-text-field>
            </v-col>
            <v-col cols="6">
              <v-text-field
                label="City"
                required
                :maxlength="300"
                v-model="data.city"
                @input="$v.data.city.$touch()"
                @blur="$v.data.city.$touch()"
                :error-messages="cityErrors"
              ></v-text-field>
            </v-col>
            <v-col cols="6">
              <v-text-field
                label="Country"
                required
                :maxlength="300"
                v-model="data.country"
                @input="$v.data.country.$touch()"
                @blur="$v.data.country.$touch()"
                :error-messages="countryErrors"
              ></v-text-field>
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
  decimal
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
        text: "Add Room",
        icon: ""
      })
    },
    dialogStyle: {
      type: Object,
      default: () => ({
        persistent: true,
        maxWidth: "",
        fullscreen: true,
        hideOverlay: true
      })
    }
  },
  data() {
    return {
      dialog: false,
      roomTypes: [],
      data: new Form({
        name: "",
        price: "",
        address: "",
        postcode: "",
        state: "",
        city: "",
        country: "",
        roomTypes: []
      }),
      formDialogStyle: {
        persistent: true,
        maxWidth: "600px",
        fullscreen: false,
        hideOverlay: true
      },
      formButtonStyle: {
        block: false,
        color: "primary",
        class: "ma-4",
        text: "Add New Room Type",
        icon: "mdi-plus"
      }
    };
  },

  validations() {
    return {
      data: {
        name: { required, maxLength: maxLength(100) },
        price: { required, decimal },
        address: { maxLength: maxLength(300) },
        postcode: { maxLength: maxLength(300) },
        state: { maxLength: maxLength(300) },
        city: { maxLength: maxLength(300) },
        country: { maxLength: maxLength(300) }
      }
    };
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

      if (!this.$v.data.name.maxLength) {
        errors.push("Name should be less than 300 characters");
        return errors;
      }
    },
    priceErrors() {
      const errors = [];
      if (!this.$v.data.price.$dirty) {
        return errors;
      }

      if (!this.$v.data.price.required) {
        errors.push("Price is required");
        return errors;
      }

      if (!this.$v.data.price.decimal) {
        errors.push("Please key in number only.");
        return errors;
      }
    },

    addressErrors() {
      const errors = [];
      if (!this.$v.data.address.$dirty) {
        return errors;
      }

      if (!this.$v.data.address.maxLength) {
        errors.push("Address should be less than 300 characters");
        return errors;
      }
    },
    stateErrors() {
      const errors = [];
      if (!this.$v.data.state.$dirty) {
        return errors;
      }

      if (!this.$v.data.state.maxLength) {
        errors.push("State should be less than 300 characters");
        return errors;
      }
    },
    postcodeErrors() {
      const errors = [];
      if (!this.$v.data.postcode.$dirty) {
        return errors;
      }

      if (!this.$v.data.postcode.maxLength) {
        errors.push("PostCode should be less than 300 characters");
        return errors;
      }
    },
    cityErrors() {
      const errors = [];
      if (!this.$v.data.city.$dirty) {
        return errors;
      }

      if (!this.$v.data.city.maxLength) {
        errors.push("City should be less than 300 characters");
        return errors;
      }
    },
    countryErrors() {
      const errors = [];
      if (!this.$v.data.country.$dirty) {
        return errors;
      }

      if (!this.$v.data.country.maxLength) {
        errors.push("Country should be less than 300 characters");
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
  mounted() {
    this.$vuetify.theme.dark = true;
    console.log("form created");

    this.getRoomTypesAction({ pageNumber: -1, pageSize: -1 }).then(data => {
      this.roomTypes = data.data;
      if (this.editMode) {
        this.showLoadingAction();
        this.getRoomAction({ uid: this.uid })
          .then(data => {
            var ids = data.data.room_types.map(function(roomType) {
              return roomType.id;
            });
            //Should assign data first before creating form because the form will reset after triggered
            //Create the form before assigning the data, the form will not keep track the original/default value of data
            Object.assign(data.data, { roomTypes: ids });
            this.data = new Form(data.data);
            console.log(this.data.roomTypes);
            this.endLoadingAction();
          })
          .catch(error => {
            this.endLoadingAction();
          });
      }
    });
  },
  methods: {
    ...mapActions({
      getRoomTypesAction: "getRoomTypes",
      getRoomAction: "getRoom",
      createRoomAction: "createRoom",
      updateRoomAction: "updateRoom",
      showLoadingAction: "showLoadingAction",
      endLoadingAction: "endLoadingAction"
    }),

    createRoom() {
      this.$v.$touch(); //it will validate all fields

      if (this.$v.$invalid) {
        Toast.fire({
          icon: "warning",
          title: "Please make sure all the data is valid. "
        });
      } else {
        this.$Progress.start();
        this.showLoadingAction();
        this.createRoomAction(this.data)
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

    updateRoom() {
      this.$v.$touch(); //it will validate all fields

      if (this.$v.$invalid) {
        Toast.fire({
          icon: "warning",
          title: "Please make sure all the data is valid. "
        });
      } else {
        this.$Progress.start();
        this.showLoadingAction();
        this.updateRoomAction(this.data)
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
    appendRoomTypeList($data) {
      this.roomTypes.push($data);
      this.data.roomTypes.push($data.id);
    },
    updateFormDetails() {
      if (this.data.roomTypes.length == 1) {
        var id = this.data.roomTypes[0];
        var roomType = this.roomTypes.find(function(roomType) {
          return roomType.id == id;
        });
        this.data.price = this.helpers.toDouble(roomType.price);
      } else {
        this.data.price = 0;
      }
    }
  }
};
</script>