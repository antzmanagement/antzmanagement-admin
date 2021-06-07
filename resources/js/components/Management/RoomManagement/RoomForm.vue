

<script>
import { validationMixin } from "vuelidate";
import {
  required,
  minLength,
  maxLength,
  decimal,
} from "vuelidate/lib/validators";
import { mapActions } from "vuex";
import { notEmptyLength } from "../../../common/common-function";
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
        text: "Add Room",
        icon: "",
        elevation: 5,
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
      roomTypes: [],
      owners: [],
      properties: [],
      roomStatusOptions: [ "maintaining", "occupied", 'vacant', 'defect'],
      data: new Form({
        name: "",
        price: "",
        address: "",
        postcode: "",
        state: "",
        city: "",
        country: "",
        lot: "",
        jalan: "",
        block: "",
        floor: "",
        unit: "",
        size: 0,
        remark: "",
        sublet: false,
        sublet_claim: 0,
        owner_claim: 0,
        roomType: "",
        owner: "",
        room_status: "empty",
        properties: [],
        tnb_account_no: "",
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
          text: "Add New Room Type",
          icon: "mdi-plus",
        },
      },
    };
  },

  validations() {
    return {
      data: {
        unit: { required, maxLength: maxLength(100) },
        price: { required, decimal },
        address: { maxLength: maxLength(300) },
        postcode: { maxLength: maxLength(300) },
        state: { maxLength: maxLength(300) },
        city: { maxLength: maxLength(300) },
        country: { maxLength: maxLength(300) },
      },
    };
  },

  computed: {
    isLoading() {
      return this.$store.getters.isLoading;
    },
    unitErrors() {
      const errors = [];
      if (!this.$v.data.unit.$dirty) {
        return errors;
      }

      if (!this.$v.data.unit.required) {
        errors.push("Unit is required");
        return errors;
      }

      if (!this.$v.data.unit.maxLength) {
        errors.push("Unit should be less than 300 characters");
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
  mounted() {
    this.showLoadingAction();

    let promises = [];

    promises.push(this.getRoomTypesAction({ pageNumber: -1, pageSize: -1 }));
    promises.push(this.getRoomAction({ uid: this.uid }));
    promises.push(this.getOwnersAction({ pageNumber: -1, pageSize: -1 }));
    promises.push(this.getPropertiesAction({ pageNumber: -1, pageSize: -1 }));

    Promise.all(promises)
      .then(([roomTypesRes, roomRes, ownersRes, propertyRes]) => {
        //room types
        this.roomTypes =
          _.isArray(_.get(roomTypesRes, ["data"])) &&
          !_.isEmpty(_.get(roomTypesRes, ["data"]))
            ? roomTypesRes.data
            : [];

        //Property
        this.properties =
          _.isArray(_.get(propertyRes, ["data"])) &&
          !_.isEmpty(_.get(propertyRes, ["data"]))
            ? propertyRes.data
            : [];

        //owners
        this.owners =
          _.isArray(_.get(ownersRes, ["data"])) &&
          !_.isEmpty(_.get(ownersRes, ["data"]))
            ? ownersRes.data
            : [];

        //room
        if (this.editMode) {
          var roomTypeIds = (_.get(roomRes, ["data", "room_types"]) || []).map(
            function (roomType) {
              return roomType.id;
            }
          );

          roomTypeIds =
            _.isArray(roomTypeIds) && !_.isEmpty(roomTypeIds)
              ? roomTypeIds[0]
              : [];

          var ownerIds = (_.get(roomRes, ["data", "owners"]) || []).map(
            function (item) {
              return item.id;
            }
          );

          ownerIds =
            _.isArray(ownerIds) && !_.isEmpty(ownerIds) ? ownerIds[0] : "";

          var propertyIds = (_.get(roomRes, ["data", "properties"]) || []).map(
            function (item) {
              return item.id;
            }
          );

          propertyIds =
            _.isArray(propertyIds) && !_.isEmpty(propertyIds)
              ? propertyIds
              : [];

          roomRes.data = {
            ...roomRes.data,
            roomType: roomTypeIds,
            owner: ownerIds,
            properties: propertyIds,
          };
          this.data = new Form({
            ...(_.get(roomRes, ["data"]) || {}),
          });
        }

        this.endLoadingAction();
      })
      .catch((err) => {
        console.log(err);
        Toast.fire({
          icon: "warning",
          title: "Something went wrong...",
        });
        this.endLoadingAction();
      });
  },
  methods: {
    ...mapActions({
      getOwnersAction: "getOwners",
      getPropertiesAction: "getProperties",
      getRoomTypesAction: "getRoomTypes",
      getRoomAction: "getRoom",
      createRoomAction: "createRoom",
      updateRoomAction: "updateRoom",
      showLoadingAction: "showLoadingAction",
      endLoadingAction: "endLoadingAction",
    }),

    createRoom() {
      this.$v.$touch(); //it will validate all fields

      if (this.$v.$invalid) {
        Toast.fire({
          icon: "warning",
          title: "Please make sure all the data is valid. ",
        });
      } else {
        console.log("creating");
        console.log(this.data);
        this.$Progress.start();
        this.showLoadingAction();
        this.createRoomAction(this.data)
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

    updateRoom() {
      this.$v.$touch(); //it will validate all fields

      console.log("updating");
      console.log(this.data);
      if (this.$v.$invalid) {
        Toast.fire({
          icon: "warning",
          title: "Please make sure all the data is valid. ",
        });
      } else {
        this.$Progress.start();
        this.showLoadingAction();
        this.updateRoomAction(this.data)
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
    // appendRoomTypeList($data) {
    //   this.roomTypes.push($data);
    //   // this.data.roomType.push($data.id);
    // },
    updateFormDetails() {
      var roomType;
      var id = this.data.roomType;

      if (id) {
        roomType = this.roomTypes.find(function (roomType) {
          return roomType.id == id;
        });
        this.data.price = this.helpers.toDouble(
          roomType ? (roomType.price ? roomType.price : 0) : 0
        );

        this.data.size = roomType.area || 0;
      }

      if (!_.isNaN(parseFloat(this.data.price))) {
        if (!this.data.sublet) {
          this.data.owner_claim =
            parseFloat((parseFloat(this.data.price) * 0.1).toFixed(2)) < 30
              ? 30
              : parseFloat((parseFloat(this.data.price) * 0.1).toFixed(2));
        }
      } else {
        if (!this.data.sublet) {
          this.data.owner_claim = 0;
        }
      }
    },
  },
};
</script>

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
        :disabled="isLoading"
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
        <v-toolbar-title v-if="!editMode">Add Room</v-toolbar-title>
        <v-toolbar-title v-else>Edit Room</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-toolbar-items>
          <v-btn
            dark
            text
            :disabled="isLoading"
            @click="editMode ? updateRoom() : createRoom()"
            >Save</v-btn
          >
        </v-toolbar-items>
      </v-toolbar>
      <v-card-text>
        <v-container>
          <v-row>
            <v-col cols="12">
              <v-autocomplete
                v-model="data.roomType"
                :item-text="(item) => helpers.capitalizeFirstLetter(item.name)"
                item-value="id"
                :items="roomTypes || []"
                label="Room Type"
                chips
                deletable-chips
                @change="updateFormDetails()"
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
            <v-col cols="12">
              <v-autocomplete
                v-model="data.owner"
                :item-text="(item) => helpers.capitalizeFirstLetter(item.name)"
                item-value="id"
                :items="owners || []"
                label="Owner"
                chips
                deletable-chips
                @change="updateFormDetails()"
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
            <v-col cols="12">
              <v-autocomplete
                v-model="data.properties"
                :item-text="(item) => helpers.capitalizeFirstLetter(item.name)"
                item-value="id"
                :items="properties || []"
                label="Property"
                chips
                multiple
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
                label="Unit No*"
                required
                :maxlength="300"
                v-model="data.unit"
                @input="$v.data.unit.$touch()"
                @blur="$v.data.unit.$touch()"
                :error-messages="unitErrors"
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="6">
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
            <v-col cols="12" md="6">
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
            <v-col cols="12" md="6">
              <v-text-field
                label="Postcode"
                v-model="data.postcode"
                :maxlength="300"
                @input="$v.data.postcode.$touch()"
                @blur="$v.data.postcode.$touch()"
                :error-messages="postcodeErrors"
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="6">
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
            <!-- <v-col cols="12" md="6">
              <v-text-field
                label="Country"
                required
                :maxlength="300"
                v-model="data.country"
                @input="$v.data.country.$touch()"
                @blur="$v.data.country.$touch()"
                :error-messages="countryErrors"
              ></v-text-field>
            </v-col> -->
            <v-col cols="12" md="6">
              <v-text-field
                label="Lot"
                required
                :maxlength="300"
                v-model="data.lot"
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="6">
              <v-text-field
                label="Jalan"
                required
                :maxlength="300"
                v-model="data.jalan"
              ></v-text-field>
            </v-col>
            <!-- <v-col cols="12" md="6">
              <v-text-field
                label="Block"
                required
                :maxlength="300"
                v-model="data.block"
              ></v-text-field>
            </v-col> -->
            <v-col cols="12" md="6">
              <v-text-field
                label="Floor"
                required
                :maxlength="300"
                v-model="data.floor"
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="6">
              <v-text-field
                label="Area Size"
                required
                type="number"
                step="0.01"
                :maxlength="300"
                v-model="data.size"
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="6">
              <v-text-field
                label="TNB Account No"
                :maxlength="300"
                v-model="data.tnb_account_no"
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="6">
              <v-select
                :items="roomStatusOptions"
                v-model="data.room_status"
                label="Room Status"
              ></v-select>
            </v-col>
            <v-col cols="12" md="6" v-if="data.owner">
              <v-switch v-model="data.sublet" :label="`Is Sublet`"></v-switch>
            </v-col>
            <v-col cols="12" md="6" v-if="data.sublet == true && data.owner">
              <v-text-field
                label="Sublet Claim"
                required
                type="number"
                step="0.01"
                :maxlength="300"
                v-model="data.sublet_claim"
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="6" v-if="data.sublet == false && data.owner">
              <v-text-field
                label="Owner Claim"
                required
                type="number"
                step="0.01"
                :maxlength="300"
                v-model="data.owner_claim"
              ></v-text-field>
              <span>(By default based on 10% of rental)</span>
            </v-col>
            <v-col cols="12">
              <v-textarea
                name="input-7-1"
                label="Remark"
                v-model="data.remark"
              ></v-textarea>
            </v-col>
          </v-row>
        </v-container>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>