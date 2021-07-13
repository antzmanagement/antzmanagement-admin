

<script>
import { validationMixin } from "vuelidate";
import {
  required,
  minLength,
  maxLength,
  decimal,
} from "vuelidate/lib/validators";
import { mapActions } from "vuex";
import moment from "moment";
export default {
  props: {
    uid: {
      type: String,
      default: "",
    },
  },
  data() {
    return {
      checkoutDateMenu: false,
      data: {},
    };
  },

  computed: {
    isLoading() {
      return this.$store.getters.isLoading;
    },
  },
  watch: {
    uid: function (val) {
      this.getRoomContract();
    },
  },
  mounted() {
    this.getRoomContract();
  },
  methods: {
    ...mapActions({
      getRoomContractAction: "getRoomContract",
      checkoutRoomContractAction: "checkoutRoomContract",
      showLoadingAction: "showLoadingAction",
      endLoadingAction: "endLoadingAction",
    }),
    close() {
      this.$emit("close");
    },
    getRoomContract() {
      this.showLoadingAction();
      this.$Progress.start();
      this.getRoomContractAction({ uid: this.uid })
        .then((data) => {
          this.data = data.data;
          this.data.checkout_date = moment().format("YYYY-MM-DD");
          this.data.return_deposit = this.data.deposit;
          this.$Progress.finish();
          this.endLoadingAction();
        })
        .catch((error) => {
          Toast.fire({
            icon: "warning",
            title: "Fail to retrieve the room contract!!!!! ",
          });
          this.$Progress.finish();
          this.endLoadingAction();
        });
    },
    checkout() {
      this.checkoutRoomContractAction(this.data)
        .then((data) => {
          Toast.fire({
            icon: "success",
            title: "Checked out! ",
          });
          this.$Progress.finish();
          this.endLoadingAction();
          location.reload();
          this.$emit("close");
        })
        .catch((error) => {
          Toast.fire({
            icon: "warning",
            title:
              "Fail to check out. Please make sure all the data is valid and rentals are paid .  ",
          });
          this.$Progress.finish();
          this.endLoadingAction();
        });
    },
  },
};
</script>

<template>
  <v-card>
    <v-card-text>
      <v-container>
        <v-row>
          <v-col cols="12">
            <v-menu
              ref="menu"
              v-model="checkoutDateMenu"
              :close-on-content-click="false"
              transition="scale-transition"
              offset-y
            >
              <template v-slot:activator="{ on }">
                <v-text-field
                  v-model="data.checkout_date"
                  label="Checkout Date"
                  prepend-icon="event"
                  readonly
                  v-on="on"
                  :error-messages="
                    helpers.isEmpty(data.checkout_date)
                      ? 'Date is required'
                      : ''
                  "
                ></v-text-field>
              </template>
              <v-date-picker
                v-model="data.checkout_date"
                no-title
                scrollable
              ></v-date-picker>
            </v-menu>
          </v-col>
          <v-col cols="12">
            <v-text-field
              label="Checkout Charge"
              type="number"
              step="0.01"
              v-model="data.checkout_charges"
            ></v-text-field>
          </v-col>
          <v-col cols="12">
            <v-text-field
              label="Return Deposit"
              type="number"
              step="0.01"
              v-model="data.return_deposit"
            ></v-text-field>
          </v-col>
          <v-col cols="12">
            <v-textarea
              label="Remark"
              :maxlength="2500"
              v-model="data.checkout_remark"
            ></v-textarea>
          </v-col>
        </v-row>
      </v-container>
    </v-card-text>
    <v-card-actions>
      <v-spacer></v-spacer>
      <v-btn color="blue darken-1" text @click="close()">Close</v-btn>
      <v-btn color="blue darken-1" text @click="checkout()">Submit</v-btn>
    </v-card-actions>
  </v-card>
</template>