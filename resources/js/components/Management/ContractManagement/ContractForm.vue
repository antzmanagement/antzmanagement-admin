<script>
import { validationMixin } from "vuelidate";
import {
  required,
  minLength,
  maxLength,
  decimal,
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
    reset: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      data: new Form({
        name: "",
        desc: "",
      }),
    };
  },

  validations() {
    return {
      data: {
        name: { required, maxLength: maxLength(300) },
        desc: { maxLength: maxLength(2500) },
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

    descErrors() {
      const errors = [];
      if (!this.$v.data.desc.$dirty) {
        return errors;
      }

      if (!this.$v.data.desc.maxLength) {
        errors.push("Name should be less than 300 characters");
        return errors;
      }
    },
  },
  watch: {
    reset: function (val) {
      if (val) {
        this.data.reset();
        this.$v.$reset();
      }
    },
  },
  mounted() {
    if (this.editMode) {
      this.showLoadingAction();
      this.getContractAction({ uid: this.uid })
        .then((data) => {
          this.data = new Form(data.data);
          this.endLoadingAction();
        })
        .catch((error) => {
          this.endLoadingAction();
          Toast.fire({
            icon: "warning",
            title: "Something went wrong...",
          });
        });
    }
  },
  methods: {
    ...mapActions({
      getContractAction: "getContract",
      createContractAction: "createContract",
      updateContractAction: "updateContract",
      showLoadingAction: "showLoadingAction",
      endLoadingAction: "endLoadingAction",
    }),
    close() {
      this.$emit("close");
    },
    createContract() {
      this.$v.$touch(); //it will validate all fields

      if (this.$v.$invalid) {
        Toast.fire({
          icon: "warning",
          title: "Please make sure all the data is valid. ",
        });
      } else {
        this.$Progress.start();
        this.showLoadingAction();
        this.createContractAction(this.data)
          .then((data) => {
            Toast.fire({
              icon: "success",
              title: "Successful Created. ",
            });
            this.$Progress.finish();
            this.endLoadingAction();
            this.$emit("created", data.data);
            this.close();
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

    updateContract() {
      this.$v.$touch(); //it will validate all fields

      if (this.$v.$invalid) {
        Toast.fire({
          icon: "warning",
          title: "Please make sure all the data is valid. ",
        });
      } else {
        this.$Progress.start();
        this.showLoadingAction();
        this.updateContractAction(this.data)
          .then((data) => {
            Toast.fire({
              icon: "success",
              title: "Successful Updated. ",
            });
            this.$Progress.finish();
            this.endLoadingAction();
            this.$emit("updated", data.data);
            this.close();
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
  <v-card>
    <v-toolbar dark color="primary">
      <v-btn icon dark @click="close()">
        <v-icon>mdi-close</v-icon>
      </v-btn>
      <v-toolbar-title v-if="!editMode">Add Contract</v-toolbar-title>
      <v-toolbar-title v-else>Edit Contract</v-toolbar-title>
      <v-spacer></v-spacer>
      <v-toolbar-items>
        <v-btn
          dark
          text
          :disabled="isLoading"
          @click="editMode ? updateContract() : createContract()"
          >Save</v-btn
        >
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
</template>
