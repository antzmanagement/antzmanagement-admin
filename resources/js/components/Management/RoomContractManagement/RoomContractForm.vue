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
            <v-col cols="12" v-show="!roomId">
              <v-autocomplete
                v-model="data.rooms"
                item-text="name"
                item-value="id"
                :items="rooms"
                label="Room"
                chips
                :deletable-chips="this.editMode ? false : true"
                :multiple="this.editMode ? false : true"
                :error-messages="roomsErrors"
              >
                <template v-slot:append>
                  <room-form
                    :editMode="false"
                    :dialogStyle="roomFormDialogConfig.dialogStyle"
                    :buttonStyle="roomFormDialogConfig.buttonStyle"
                    @created="appendRoomList($event)"
                  ></room-form>
                </template>
              </v-autocomplete>
            </v-col>
            <v-col cols="12">
              <v-autocomplete
                v-model="data.properties"
                :item-text="item => helpers.capitalizeFirstLetter(item.name)"
                item-value="id"
                :items="properties"
                label="Property"
                chips
                :deletable-chips="this.editMode ? false : true"
                :multiple="this.editMode ? false : true"
              >
                <template v-slot:append>
                  <property-form
                    :editMode="false"
                    :dialogStyle="propertyFormDialogConfig.dialogStyle"
                    :buttonStyle="propertyFormDialogConfig.buttonStyle"
                    @created="appendPropertyList($event)"
                  ></property-form>
                </template>
              </v-autocomplete>
            </v-col>
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

    roomId: {
      type: Number,
      default: () => null
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
        isIcon: false
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
      rooms: [],
      properties: [],
      data: new Form({
        remark: "",
        price: "",
        rooms: [],
        properties: []
      }),
      roomFormDialogConfig: {
        dialogStyle: {
          persistent: true,
          maxWidth: "600px",
          fullscreen: false,
          hideOverlay: true
        },
        buttonStyle: {
          block: false,
          color: "primary",
          class: "ma-4",
          text: "Add New Room",
          icon: "mdi-plus"
        }
      },
      propertyFormDialogConfig: {
        dialogStyle: {
          persistent: true,
          maxWidth: "600px",
          fullscreen: false,
          hideOverlay: true
        },
        buttonStyle: {
          block: false,
          color: "primary",
          class: "ma-4",
          text: "Add New Property",
          icon: "mdi-plus"
        }
      }
    };
  },

  validations() {
    return {
      data: {
        remark: { required, maxLength: maxLength(2500) },
        price: { required, decimal }
      }
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
    roomsErrors() {
      const errors = [];

      if (this.helpers.isEmpty(this.data.rooms)) {
        errors.push("Room is required");
      }
      return errors;
    }
  },
  watch: {
    dialog: function(val) {
      if (val) {
        this.data.reset();
        this.$v.$reset();

        if (this.roomId) {
          this.data.rooms.push(this.roomId);
        }
      }
    }
  },
  mounted() {
    console.log("form created");

    this.showLoadingAction();
    this.getRoomsAction({ pageNumber: -1, pageSize: -1 }).then(data => {
      this.rooms = data.data;

      this.getPropertiesAction({ pageNumber: -1, pageSize: -1 }).then(data => {
        this.properties = data.data;

        if (this.editMode) {
          this.getRoomContractAction({ uid: this.uid })
            .then(data => {
              //Should assign data first before creating form because the form will reset after triggered
              //Create the form before assigning the data, the form will not keep track the original/default value of data

              Object.assign(data.data, {
                rooms: data.data.room.id,
                properties: data.data.property.id
              });
              this.data = new Form(data.data);
              this.endLoadingAction();
            })
            .catch(error => {
              this.endLoadingAction();
              Toast.fire({
                icon: "warning",
                title: "Something went wrong..."
              });
            });
        } else {
          this.endLoadingAction();
        }
      });
    });
  },
  methods: {
    ...mapActions({
      getRoomsAction: "getRooms",
      getPropertiesAction: "getProperties",
      getRoomContractAction: "getRoomContract",
      createRoomContractAction: "createRoomContract",
      updateRoomContractAction: "updateRoomContract",
      showLoadingAction: "showLoadingAction",
      endLoadingAction: "endLoadingAction"
    }),

    customValidate() {
      return !this.helpers.isEmpty(this.data.rooms);
    },
    createRoomContract() {
      this.$v.$touch(); //it will validate all fields

      if (this.$v.$invalid || !this.customValidate()) {
        Toast.fire({
          icon: "warning",
          title: "Please make sure all the data is valid. "
        });
      } else {
        this.$Progress.start();
        this.showLoadingAction();
        this.createRoomContractAction(this.data)
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

    updateRoomContract() {
      this.$v.$touch(); //it will validate all fields

      if (this.$v.$invalid) {
        Toast.fire({
          icon: "warning",
          title: "Please make sure all the data is valid. "
        });
      } else {
        this.$Progress.start();
        this.showLoadingAction();
        this.updateRoomContractAction(this.data)
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
    appendRoomList($data) {
      this.rooms.push($data);
      if (!this.editMode) {
        this.data.rooms.push($data.id);
      } else {
        this.data.rooms = $data.id;
      }
    },
    appendPropertyList($data) {
      this.properties.push($data);
      if (!this.editMode) {
        this.data.properties.push($data.id);
      } else {
        this.data.properties = $data.id;
      }
    }
  }
};
</script>