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
        <v-icon left>{{buttonStyle.icon}}</v-icon>
        {{buttonStyle.text}}
      </v-btn>
    </template>
    <v-card>
      <v-toolbar dark color="primary">
        <v-btn icon dark @click="dialog = false">
          <v-icon>mdi-close</v-icon>
        </v-btn>
        <v-toolbar-title v-if="!editMode">Add Property</v-toolbar-title>
        <v-toolbar-title v-else>Edit Property</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-toolbar-items>
          <v-btn
            dark
            text
            :disabled="isLoading"
            @click="editMode ? updateProperty() : createProperty()"
          >Save</v-btn>
        </v-toolbar-items>
      </v-toolbar>
      <v-card-text>
        <v-container>
          <v-row>
            <v-col cols="12" md="6">
              <v-text-field
                label="Name"
                required
                :maxlength="300"
                v-model="data.name"
                @input="$v.data.name.$touch()"
                @blur="$v.data.name.$touch()"
                :error-messages="nameErrors"
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="12">
              <v-textarea
                label="Description"
                :maxlength="2500"
                v-model="data.desc"
                @input="$v.data.desc.$touch()"
                @blur="$v.data.desc.$touch()"
                :error-messages="descErrors"
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
    buttonStyle: {
      type: Object,
      default: () => ({
        block: true,
        color: "primary",
        class: "ma-1",
        text: "Add Property",
        icon: "",
        elevation: 5
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
      data: new Form({
        name: "",
        desc: ""
      })
    };
  },

  validations() {
    return {
      data: {
        name: { required, maxLength: maxLength(300) },
        desc: { maxLength: maxLength(2500) }
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

    descErrors() {
      const errors = [];
      if (!this.$v.data.desc.$dirty) {
        return errors;
      }

      if (!this.$v.data.desc.maxLength) {
        errors.push("Name should be less than 300 characters");
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
    console.log("form created");

    if (this.editMode) {
      this.showLoadingAction();
      this.getPropertyAction({ uid: this.uid })
        .then(data => {
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
    }
  },
  methods: {
    ...mapActions({
      getPropertyAction: "getProperty",
      createPropertyAction: "createProperty",
      updatePropertyAction: "updateProperty",
      showLoadingAction: "showLoadingAction",
      endLoadingAction: "endLoadingAction"
    }),

    createProperty() {
      this.$v.$touch(); //it will validate all fields

      if (this.$v.$invalid) {
        Toast.fire({
          icon: "warning",
          title: "Please make sure all the data is valid. "
        });
      } else {
        this.$Progress.start();
        this.showLoadingAction();
        this.createPropertyAction(this.data)
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

    updateProperty() {
      this.$v.$touch(); //it will validate all fields

      if (this.$v.$invalid) {
        Toast.fire({
          icon: "warning",
          title: "Please make sure all the data is valid. "
        });
      } else {
        this.$Progress.start();
        this.showLoadingAction();
        this.updatePropertyAction(this.data)
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
      this.data.rooms.push($data.id);
    },
    appendPropertyList($data) {
      this.properties.push($data);
      this.data.properties.push($data.id);
    }
  }
};
</script>