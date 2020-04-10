<template>
  <v-dialog v-model="dialog" :persistent="dialogStyle.persistent" :max-width="dialogStyle.maxWidth">
    <template v-slot:activator="{ on }">
      <v-btn
        :class="activatorStyle.class"
        tile
        :color="activatorStyle.color"
        :block="activatorStyle.block"
        v-on="on"
        :disabled="isLoading"
      >
        <v-icon left>{{activatorStyle.icon}}</v-icon>
        {{activatorStyle.text}}
      </v-btn>
    </template>
    <v-card>
      <v-card-title class="ma-1">Change Password</v-card-title>
      <v-card-text>
        <v-container>
          <v-row>
            <v-col cols="12">
              <v-text-field
                label="Old Password*"
                hint="Key in previous password"
                persistent-hint
                type="password"
                required
                :maxlength="255"
                v-model="data.oldpassword"
                @input="$v.data.oldpassword.$touch(); checkPassword(data.oldpassword);"
                @blur="$v.data.oldpassword.$touch()"
                :error-messages="oldPasswordErrors"
              ></v-text-field>
            </v-col>
            <v-col cols="12">
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
            <v-col cols="12">
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
          </v-row>
        </v-container>
      </v-card-text>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn
          :class="yesButtonStyle.class"
          text
          :color="yesButtonStyle.color"
          :block="yesButtonStyle.block"
          :disabled="isLoading"
          @click="changePassword()"
        >
          <v-icon left>{{yesButtonStyle.icon}}</v-icon>
          {{yesButtonStyle.text}}
        </v-btn>
        <v-btn
          :class="noButtonStyle.class"
          text
          :color="noButtonStyle.color"
          :block="noButtonStyle.block"
          :disabled="isLoading"
          @click="dialog = false"
        >
          <v-icon left>{{noButtonStyle.icon}}</v-icon>
          {{noButtonStyle.text}}
        </v-btn>
        <!-- <v-btn color="green darken-1" text @click="dialog = false">Disagree</v-btn>
        <v-btn color="green darken-1" text @click="dialog = false">Agree</v-btn>-->
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
import { mapActions } from "vuex";
import { validationMixin } from "vuelidate";
import {
  required,
  minLength,
  maxLength,
  sameAs
} from "vuelidate/lib/validators";
export default {
  props: {
    uid: {
      type: String,
      default: ""
    },
    dialogStyle: {
      type: Object,
      default: () => ({
        persistent: false,
        maxWidth: "600px"
      })
    },
    activatorStyle: {
      type: Object,
      default: () => ({
        block: false,
        color: "primary",
        class: "m-3",
        text: "Change Password",
        icon: "mdi-key"
      })
    },

    yesButtonStyle: {
      type: Object,
      default: () => ({
        block: false,
        color: "primary",
        class: "",
        text: "Save",
        icon: ""
      })
    },

    noButtonStyle: {
      type: Object,
      default: () => ({
        block: false,
        color: "red darken-1",
        class: "",
        text: "Cancel",
        icon: ""
      })
    }
  },
  data() {
    return {
      dialog: false,
      verified: false,
      data: {
        oldpassword: "",
        password: "",
        password_confirmation: ""
      }
    };
  },

  validations() {
    return {
      data: {
        oldpassword: {
          required
        },
        password: { required, minLength: minLength(8) },
        password_confirmation: {
          required,
          sameAsPassword: sameAs("password")
        }
      }
    };
  },
  computed: {
    isLoading() {
      return this.$store.getters.isLoading;
    },

    oldPasswordErrors() {
      const errors = [];
      if (!this.$v.data.oldpassword.$dirty) {
        return errors;
      }

      if (!this.$v.data.oldpassword.required) {
        errors.push("Password is required");
        return errors;
      }

      if (!this.verified && this.data.oldpassword) {
        errors.push("Password is incorrect");
        console.log(errors);
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
  created() {
    this.$vuetify.theme.dark = true;
  },
  methods: {
    ...mapActions({
      changePasswordAction: "changePassword",
      checkPasswordAction: "checkPassword",
      showLoadingAction: "showLoadingAction",
      endLoadingAction: "endLoadingAction"
    }),
    changePassword() {
      this.$Progress.start();
      this.showLoadingAction();
      this.checkPassword(this.data.oldpassword).then(verified => {
        if (this.verified && !this.$v.$invalid) {
          this.data.uid = this.uid;
          this.changePasswordAction(this.data)
            .then(data => {
              Toast.fire({
                icon: "success",
                title: "Successful Updated. "
              });
              console.log(data);
              this.$Progress.finish();
              this.endLoadingAction();
              this.$emit("updated");
            })
            .catch(error => {
              Toast.fire({
                icon: "warning",
                title: "Fail to change the password!!!!! "
              });
              console.log(error.response);
              this.$Progress.finish();
              this.endLoadingAction();
            });
        } else {
          Toast.fire({
            icon: "warning",
            title: "Fail to change the password!!!!! "
          });
          this.$Progress.finish();
          this.endLoadingAction();
        }
      });
    },
    async checkPassword(password) {
      this.checkPasswordAction({
        uid: this.uid,
        password: password
      })
        .then(data => {
          this.verified = data.data;
          return data.data;
        })
        .catch(error => {
          this.verified = false;
          return false;
        });
    }
  }
};
</script>