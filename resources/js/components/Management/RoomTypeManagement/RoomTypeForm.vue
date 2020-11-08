

<script>
import { validationMixin } from "vuelidate";
import {
  required,
  minLength,
  maxLength,
  numeric,
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
      data: new Form({
        name: "",
        price: "",
      }),
    };
  },

  validations() {
    return {
      data: {
        name: { required, maxLength: maxLength(100) },
        price: { required, numeric },
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

      if (!this.$v.data.price.numeric) {
        errors.push("Please key in number only.");
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
        v-on="on"
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
                v-model="data.price"
                @input="$v.data.price.$touch()"
                @blur="$v.data.price.$touch()"
                :error-messages="priceErrors"
              ></v-text-field>
            </v-col>
          </v-row>
        </v-container>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>