
<script>
import Axios from "axios";
import { validationMixin } from "vuelidate";
import {
  required,
  minLength,
  maxLength,
  sameAs,
  email,
} from "vuelidate/lib/validators";
import { mapActions } from "vuex";

import { commonConfig } from "../../../../common/config";
import { roleAccess } from '../../../../common/common-function';

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
        text: "Add Staff",
        icon: "mdi-plus",
        elevation: 5,
      }),
    },
  },
  data() {
    return {
      dialog: false,
      roles: [],
      data: new Form({
        name: "",
        icno: "",
        tel1: "",
        email: "",
        password: "",
        password_confirmation: "",
        role_id: "",
        img: null,
      }),
      imgpreview: null,
      origImgPreview: null,
      rules: [
        (value) =>
          !value ||
          value.size < 2000000 ||
          "Picture size should be less than 2 MB!",
      ],
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
            sameAsPassword: sameAs("password"),
          },
        },
      };
    } else {
      return {
        data: {
          name: { required, maxLength: maxLength(100) },
          icno: { required, maxLength: maxLength(14) },
          tel1: {},
          email: { required, email },
        },
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
    },
  },
  watch: {
    dialog: function (val) {
      if (val) {
        this.data.reset();
        this.imgpreview = this.origImgPreview;
        this.$v.$reset();
      }
    },
  },
  created() {
    this.showLoadingAction();
    this.getRolesAction({
      pageNumber: -1,
      pageSize: -1,
    })
      .then((res) => {
        this.endLoadingAction();

        let setRole = _.cloneDeep(roleAccess);
        delete setRole.superadmin;
        setRole = _.keys(setRole);
        console.log(setRole);
          console.log(res);
        this.roles = res.data.filter(function (role) {
          return _.indexOf(setRole, role.name) != -1;
        });
      })
      .catch((err) => {
        this.endLoadingAction();
        Toast.fire({
          icon: "warning",
          title: "Something went wrong... ",
        });
      });
    if (this.editMode) {
      this.getStaffAction({ uid: this.uid })
        .then((data) => {
          this.data = new Form(data.data);
          this.imgpreview = _.get(data.data, ["profile_img"]) || null;
          this.origImgPreview = _.get(data.data, ["profile_img"]) || null;
          this.endLoadingAction();
        })
        .catch((error) => {
          this.endLoadingAction();
          Toast.fire({
            icon: "warning",
            title: "Something went wrong... ",
          });
        });
    } else {
      this.endLoadingAction();
    }
  },
  methods: {
    ...mapActions({
      getStaffAction: "getStaff",
      createStaffAction: "createStaff",
      updateStaffAction: "updateStaff",
      getRolesAction: "getRoles",
      showLoadingAction: "showLoadingAction",
      endLoadingAction: "endLoadingAction",
    }),

    customValidate() {
      return (
        (!this.data.tel1 || this.helpers.isPhoneFormat(this.data.tel1)) &&
        this.helpers.isIcFormat(this.data.icno)
      );
    },

    createStaff() {
      this.$v.$touch(); //it will validate all fields

      if (this.$v.$invalid || !this.customValidate()) {
        Toast.fire({
          icon: "warning",
          title: "Please make sure all the data is valid. ",
        });
      } else {
        this.$Progress.start();
        this.showLoadingAction();

        let formData = new FormData();
        formData.append("name", this.data.name);
        formData.append("icno", this.data.icno);
        formData.append("tel1", this.data.tel1);
        formData.append("email", this.data.email);
        formData.append("password", this.data.password);
        formData.append(
          "password_confirmation",
          this.data.password_confirmation
        );
        formData.append("role_id", this.data.role_id);
        formData.append("img", this.data.img);
        this.createStaffAction(formData)
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

    updateStaff() {
      this.$v.$touch(); //it will validate all fields

      if (this.$v.$invalid || !this.customValidate()) {
        Toast.fire({
          icon: "warning",
          title: "Please make sure all the data is valid. ",
        });
      } else {
        this.$Progress.start();
        this.showLoadingAction();

        this.updateStaffAction(this.data)
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

    uploadPhoto(file) {
      let reader = new FileReader();

      if (file && file.name) {
        reader.readAsDataURL(file);
      } else {
        this.imgpreview = this.origImgPreview;
      }

      let self = this;
      reader.addEventListener("load", function () {
        self.imgpreview = reader.result;
      });

      this.data.img = file;
    },
  },
};
</script>

<template>
  <v-dialog
    v-model="dialog"
    fullscreen
    hide-overlay
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
        <v-toolbar-title v-if="!editMode">Add Staff</v-toolbar-title>
        <v-toolbar-title v-else>Edit Staff</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-toolbar-items>
          <v-btn
            dark
            text
            :disabled="isLoading"
            @click="editMode ? updateStaff() : createStaff()"
            >Save</v-btn
          >
        </v-toolbar-items>
      </v-toolbar>
      <v-card-text>
        <v-container>
          <form
            @submit="editMode ? updateStaff : createStaff"
            enctype="multipart/form-data"
          >
            <v-row>
              <v-col cols="12" v-if="imgpreview">
                <v-img :src="imgpreview" :height="200" :width="200" contain ></v-img>
              </v-col>
              <v-col cols="12">
                <v-file-input
                  :rules="rules"
                  v-model="data.img"
                  accept="image/png, image/jpeg, image/bmp"
                  placeholder="Pick a picture"
                  prepend-icon="mdi-camera"
                  label="Profile Picture"
                  @change="uploadPhoto"
                ></v-file-input>
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
              <v-col cols="12" md="6">
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
              <v-col cols="12">
                <v-autocomplete
                  v-model="data.role_id"
                  :items="roles || []"
                  item-value="id"
                  item-text="name"
                  label="Role"
                  :error-messages="
                    helpers.isEmpty(data.role_id) ? 'Role is required' : ''
                  "
                ></v-autocomplete>
              </v-col>
            </v-row>
          </form>
        </v-container>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>
