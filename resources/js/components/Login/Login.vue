<template>
  <v-app id="inspire">
    <v-content>
      <v-container class="fill-height" fluid>
        <loading></loading>
        <v-row align="center" justify="center">
          <v-col cols="12" sm="8" md="4">
            <v-card class="elevation-12">
              <v-toolbar color="primary" dark flat>
                <v-toolbar-title>Login form</v-toolbar-title>
                <v-spacer />
                <!-- <v-tooltip bottom>
                  <template v-slot:activator="{ on }">
                    <v-btn href="#" icon large target="_blank" v-on="on">
                      <v-icon>mdi-code-tags</v-icon>
                    </v-btn>
                  </template>
                  <span>Source</span>
                </v-tooltip>
                <v-tooltip right>
                  <template v-slot:activator="{ on }">
                    <v-btn icon large href="#" target="_blank" v-on="on">
                      <v-icon>mdi-codepen</v-icon>
                    </v-btn>
                  </template>
                  <span>Codepen</span>
                </v-tooltip> -->
              </v-toolbar>
              <v-card-text>
                <v-form>
                  <v-text-field
                    label="email"
                    name="email"
                    prepend-icon="mdi-account-outline"
                    v-model="logindata.email"
                    type="text"
                    @input="$v.logindata.email.$touch()"
                    @blur="$v.logindata.email.$touch()"
                    :error-messages="emailErrors"
                  />
                  <has-error :form="logindata" field="email"></has-error>

                  <v-text-field
                    id="password"
                    label="Password"
                    name="password"
                    prepend-icon="mdi-lock-outline"
                    v-model="logindata.password"
                    type="password"
                    @input="$v.logindata.password.$touch()"
                    @blur="$v.logindata.password.$touch()"
                    :error-messages="passwordErrors"
                  />
                  <has-error :form="logindata" field="password"></has-error>
                </v-form>
              </v-card-text>
              <v-card-actions>
                <v-spacer />
                <v-btn color="primary" @click="login" :disabled="isLoading">Login</v-btn>
              </v-card-actions>
            </v-card>
          </v-col>
        </v-row>
      </v-container>
    </v-content>
  </v-app>
</template>

<script>
import { validationMixin } from "vuelidate";
import {
  required,
  minLength,
  maxLength,
  email
} from "vuelidate/lib/validators";
import { mapActions } from "vuex";

export default {
  // mixins: [validationMixin],

  data: () => ({
    logindata: new Form({
      email: "",
      password: ""
    })
  }),
  created() {
    
  },
  validations: {
    logindata: {
      password: { required, minLength: minLength(6) },
      email: { required, email }
    }
  },

  computed: {
    
    isLoading() {
      return this.$store.getters.isLoading;
    },
    emailErrors() {
      const errors = [];
      if (!this.$v.logindata.email.$dirty) {
        return errors;
      }

      if (!this.$v.logindata.email.required) {
        errors.push("E-mail is required");
        return errors;
      }

      if (!this.$v.logindata.email.email) {
        errors.push("Must be valid e-mail");
        return errors;
      }
    },
    passwordErrors() {
      const errors = [];
      if (!this.$v.logindata.password.$dirty) {
        return errors;
      }

      if (!this.$v.logindata.password.required) {
        errors.push("Password is required");
        return errors;
      }

      if (!this.$v.logindata.password.minLength) {
        errors.push("Password should be more than 6 characters");
        return errors;
      }
    }
  },
  methods: {
    ...mapActions({
      loginAction: "login",
      showLoadingAction: "showLoadingAction",
      endLoadingAction: "endLoadingAction"
    }),
    login() {
      this.$v.$touch(); //it will validate all fields
      if (this.$v.$invalid) {
        Toast.fire({
          icon: "warning",
          title: "Please make sure all the data is valid. "
        });
      } else {
        this.$Progress.start();
        this.showLoadingAction();
        this.loginAction(this.logindata)
          .then(data => {
            Toast.fire({
              icon: "success",
              title: "Login Successful. "
            });
            this.$Progress.finish();

            this.endLoadingAction();
            this.$router.push("management");
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
    }
  }
};
</script>