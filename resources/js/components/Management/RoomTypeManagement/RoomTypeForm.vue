

<script>
import { validationMixin } from "vuelidate";
import {
  required,
  minLength,
  maxLength,
  numeric,
  decimal,
} from "vuelidate/lib/validators";
import { mapActions } from "vuex";
import { notEmptyLength, _ } from "../../../common/common-function";
import ServicesDialog from "../ServiceManagement/ServicesDialog.vue";
export default {
  components: { ServicesDialog },
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
        text: "Add RoomType",
        icon: "",
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
      _: _,
      data: new Form({
        name: "",
        price: "",
        services: [],
      }),
    };
  },

  validations() {
    return {
      data: {
        name: { required, maxLength: maxLength(100) },
        price: { required },
      },
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
    if (this.editMode) {
      this.showLoadingAction();
      this.getRoomTypeAction({ uid: this.uid })
        .then((data) => {
          this.data = data.data;
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
  },

  methods: {
    ...mapActions({
      getRoomTypeAction: "getRoomType",
      createRoomTypeAction: "createRoomType",
      updateRoomTypeAction: "updateRoomType",
      showLoadingAction: "showLoadingAction",
      endLoadingAction: "endLoadingAction",
    }),

    createRoomType() {
      this.$v.$touch(); //it will validate all fields

      if (this.$v.$invalid) {
        Toast.fire({
          icon: "warning",
          title: "Please make sure all the data is valid. ",
        });
      } else {
        this.$Progress.start();
        this.showLoadingAction();
        this.createRoomTypeAction(this.data)
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

    updateRoomType() {
      this.$v.$touch(); //it will validate all fields

      if (this.$v.$invalid) {
        Toast.fire({
          icon: "warning",
          title: "Please make sure all the data is valid. ",
        });
      } else {
        this.$Progress.start();
        this.showLoadingAction();
        this.updateRoomTypeAction(this.data)
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
    setRoomTypeServices(e) {
      try {
        if (_.isArray(e.services) && notEmptyLength(e.services)) {
          this.data.services = e.services;
        } else {
          this.data.services = [];
        }
      } catch (error) {
        console.log(error);
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
      <a v-on="on" >
        <slot  >
          <v-btn tile isIcon :disabled="isLoading" v-if="editMode">
            <v-icon left>mdi-plus</v-icon>
            Edit New Room Type
          </v-btn>
          <v-btn tile isIcon :disabled="isLoading" v-else>
            <v-icon left>mdi-plus</v-icon>
            Add New Room Type
          </v-btn>
        </slot>
      </a>
    </template>
    <v-card>
      <v-toolbar dark color="primary">
        <v-btn icon dark @click="dialog = false">
          <v-icon>mdi-close</v-icon>
        </v-btn>
        <v-toolbar-title v-if="!editMode">Add RoomType</v-toolbar-title>
        <v-toolbar-title v-else>Edit RoomType</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-toolbar-items>
          <v-btn
            dark
            text
            :disabled="isLoading"
            @click="editMode ? updateRoomType() : createRoomType()"
            >Save</v-btn
          >
        </v-toolbar-items>
      </v-toolbar>
      <v-card-text>
        <v-container>
          <v-row>
            <v-col cols="6">
              <v-text-field
                label="Name*"
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
                required
                :maxlength="100"
                step="0.01"
                v-model="data.price"
                @input="$v.data.price.$touch()"
                @blur="$v.data.price.$touch()"
                :error-messages="priceErrors"
              ></v-text-field>
            </v-col>
            <v-col cols="6">
              <v-text-field
                label="Area"
                type="number"
                required
                :maxlength="100"
                step="0.01"
                v-model="data.area"
              ></v-text-field>
            </v-col>
            <v-col cols="12">
              <services-dialog
                @submit="(e) => setRoomTypeServices(e)"
                editMode
                :services="
                  _.isArray(data.services) && !_.isEmpty(data.services)
                    ? _.compact(
                        _.map(data.services, function (service) {
                          return _.isPlainObject(service)
                            ? service.uid || null
                            : service || null;
                        })
                      )
                    : []
                "
              >
                <v-btn> Modify Room Services </v-btn>
              </services-dialog>
            </v-col>
          </v-row>
        </v-container>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>