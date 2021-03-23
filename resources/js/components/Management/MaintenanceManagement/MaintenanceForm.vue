
<script>
import { validationMixin } from "vuelidate";
import {
  required,
  minLength,
  maxLength,
  decimal,
} from "vuelidate/lib/validators";
import { mapActions } from "vuex";
import { _ } from "../../../common/common-function";
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
        text: "Add Maintenance",
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
      propertyFormDialog: false,
      rooms: [],
      properties: [],
      owners: [],
      maintenanceTypes: ["repair", "renew"],
      maintenanceStatus: ["pending", "inprogress", "reject", "done"],
      data: new Form({
        remark: "",
        price: "",
        room: "",
        property: "",
        owner: "",
        maintenance_type: "repair",
        maintenance_status: "pending",
        claim_by_owner: false,
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
    };
  },

  validations() {
    return {
      data: {
        remark: { maxLength: maxLength(2500) },
        price: { required, decimal },
      },
    };
  },

  computed: {
    isLoading() {
      return this.$store.getters.isLoading;
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
        errors.push("Price should be decimal");
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
    console.log("call");
    this.showLoadingAction();
    let promises = [];
    promises.push(this.getRoomsAction({ pageNumber: -1, pageSize: -1 }));
    promises.push(this.getPropertiesAction({ pageNumber: -1, pageSize: -1 }));
    promises.push(this.getOwnersAction({ pageNumber: -1, pageSize: -1 }));

    if (this.editMode) {
      console.log(this.uid);
      promises.push(this.getMaintenanceAction({ uid: this.uid }));
    }

    Promise.all(promises)
      .then(([roomRes, propertyRes, ownerRes, maintenanceRes]) => {
        this.rooms = roomRes.data || [];
        this.owners = ownerRes.data || [];
        this.properties = propertyRes.data || [];

        if (maintenanceRes) {
          console.log(maintenanceRes);
          let maintenanceRoom = _.get(maintenanceRes, [
            "data",
            "room",
          ]) || {};
          maintenanceRes.data.room = _.find(this.rooms, ['id', maintenanceRoom.id]);
          maintenanceRes.data.property = _.get(maintenanceRes, [
            "data",
            "property",
            "id",
          ]);
          maintenanceRes.data.owner = _.get(maintenanceRes, [
            "data",
            "owner",
            "id",
          ]);

          console.log(maintenanceRes);
          this.data = new Form(maintenanceRes.data);
          console.log("this");
          console.log(this.data);
        }
        this.endLoadingAction();
      })
      .catch((error) => {
        this.endLoadingAction();
        console.log(error);
        Toast.fire({
          icon: "warning",
          title: "Something went wrong...",
        });
      });
  },
  methods: {
    ...mapActions({
      getRoomsAction: "getRooms",
      getOwnersAction: "getOwners",
      getPropertiesAction: "getProperties",
      getMaintenanceAction: "getMaintenance",
      createMaintenanceAction: "createMaintenance",
      updateMaintenanceAction: "updateMaintenance",
      showLoadingAction: "showLoadingAction",
      endLoadingAction: "endLoadingAction",
    }),

    createMaintenance() {
      this.$v.$touch(); //it will validate all fields

      if (this.$v.$invalid) {
        Toast.fire({
          icon: "warning",
          title: "Please make sure all the data is valid. ",
        });
      } else {
        this.$Progress.start();
        this.showLoadingAction();
        this.data.room = _.get(this.data, 'room.id') || null;
        this.createMaintenanceAction(this.data)
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

    updateMaintenance() {
      this.$v.$touch(); //it will validate all fields

      if (this.$v.$invalid) {
        Toast.fire({
          icon: "warning",
          title: "Please make sure all the data is valid. ",
        });
      } else {
        this.$Progress.start();
        this.showLoadingAction();
        this.data.room = _.get(this.data, 'room.id') || null;
        this.updateMaintenanceAction(this.data)
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
        :icon="buttonStyle.isIcon"
        :disabled="isLoading"
      >
        <v-icon>{{ buttonStyle.icon }}</v-icon>
        {{ buttonStyle.text }}
      </v-btn>
    </template>
    <v-card>
      <v-toolbar dark color="primary">
        <v-btn icon dark @click="dialog = false">
          <v-icon>mdi-close</v-icon>
        </v-btn>
        <v-toolbar-title v-if="!editMode">Add Maintenance</v-toolbar-title>
        <v-toolbar-title v-else>Edit Maintenance</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-toolbar-items>
          <v-btn
            dark
            text
            :disabled="isLoading"
            @click="editMode ? updateMaintenance() : createMaintenance()"
            >Save</v-btn
          >
        </v-toolbar-items>
      </v-toolbar>
      <v-card-text>
        <v-container>
          <v-row>
            <v-col cols="12" v-show="!roomId">
              <v-autocomplete
                v-model="data.room"
                item-text="name"
                :items="rooms || []"
                label="Room"
                chips
                return-object
              >
              </v-autocomplete>
            </v-col>
            <v-col cols="12" md="12" v-if="_.isArray(_.get(data , ['room', 'owners'])) && !_.isEmpty(_.get(data , ['room', 'owners']))">
              <v-switch v-model="data.claim_by_owner" :label="`Claimed By Owner (${_.get(data, ['room', 'owners',0 ,'name']) || 'N/A'})`"></v-switch>
            </v-col>
            <v-col cols="12">
              <v-autocomplete
                v-model="data.property"
                :item-text="(item) => helpers.capitalizeFirstLetter(item.name)"
                item-value="id"
                :items="properties || []"
                label="Property"
                chips
              >
              </v-autocomplete>
            </v-col>
            <!-- <v-col cols="12">
              <v-autocomplete
                v-model="data.owner"
                :item-text="(item) => helpers.capitalizeFirstLetter(item.name)"
                item-value="id"
                :items="owners || []"
                label="Claim By Owner"
                clearable
              >
              </v-autocomplete>
            </v-col> -->
            <v-col cols="12" md="12">
              <v-text-field
                label="Price"
                type="number"
                step="0.01"
                required
                :maxlength="300"
                v-model="data.price"
                @input="$v.data.price.$touch()"
                @blur="$v.data.price.$touch()"
                :error-messages="priceErrors"
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="6">
              <v-select
                :items="maintenanceTypes"
                v-model="data.maintenance_type"
                label="Maintenance Type"
              ></v-select>
            </v-col>
            <v-col cols="12" md="6">
              <v-select
                :items="maintenanceStatus"
                v-model="data.maintenance_status"
                label="Maintenance status"
              ></v-select>
            </v-col>
            <v-col cols="12">
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

    <v-dialog
      v-model="propertyFormDialog"
      persistent
      hideOverlay
      max-width="600px"
    >
      <property-form
        :reset="propertyFormDialog"
        :editMode="false"
        @created="appendPropertyList($event)"
        @close="propertyFormDialog = false"
      ></property-form>
    </v-dialog>
  </v-dialog>
</template>
