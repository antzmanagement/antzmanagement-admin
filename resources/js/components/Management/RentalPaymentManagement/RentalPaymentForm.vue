

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
    editMode: {
      type: Boolean,
      default: false,
    },
    uid: {
      type: String,
      default: "",
    },
  },
  data() {
    return {
      penaltyRate: 10,
      expiredDays: 9,
      data: new Form({
        penalty: 0,
        price: 0,
      }),
    };
  },

  computed: {
    isLoading() {
      return this.$store.getters.isLoading;
    },
  },
  watch: {
    uid: function (val) {
      console.log("changed");
      this.getRentalPayment();
    },
  },
  mounted() {
    this.getRentalPayment();
  },
  methods: {
    ...mapActions({
      makePaymentAction: "makePayment",
      getRentalPaymentAction: "getRentalPayment",
      showLoadingAction: "showLoadingAction",
      endLoadingAction: "endLoadingAction",
    }),
    close() {
      this.$emit("close");
    },
    getRentalPayment() {
      this.showLoadingAction();
      this.$Progress.start();
      this.getRentalPaymentAction({ uid: this.uid })
        .then((data) => {
          this.data = data.data;
          if (!this.editMode) {
            this.data.penalty = this.calculatePenalty(data.data);
          } else {
            this.data.penalty = data.data.penalty;
          }
          this.$Progress.finish();
          this.endLoadingAction();
        })
        .catch((error) => {
          Toast.fire({
            icon: "warning",
            title: "Fail to retrieve the rental!!!!! ",
          });
          this.$Progress.finish();
          this.endLoadingAction();
        });
    },
    calculatePenalty(data) {
      let rentaldate = moment(data.rentaldate);
      let diff = moment().diff(rentaldate, 'days', false);
      console.log('diff');
      console.log(diff);
      let overDays = diff - parseInt(this.expiredDays);
      if (overDays > 0) {
        return overDays * this.penaltyRate;
      } else {
        return 0;
      }
    },
    makePayment() {
      this.showLoadingAction();
      this.$Progress.start();
      this.makePaymentAction(this.data)
        .then((data) => {
          Toast.fire({
            icon: "success",
            title: "Successful Paid. ",
          });
          this.$Progress.finish();
          this.endLoadingAction();
          this.$emit("makePayment", data.data);
          this.close();
        })
        .catch((error) => {
          Toast.fire({
            icon: "warning",
            title: "Something went wrong!!!!! ",
          });
          this.$Progress.finish();
          this.endLoadingAction();
          this.close();
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
            <v-text-field label="Rental" type="number" step="0.01" v-model="data.price"></v-text-field>
          </v-col>
          <v-col cols="12">
            <v-text-field label="Penalty" type="number" step="0.01" v-model="data.penalty"></v-text-field>
          </v-col>
        </v-row>
      </v-container>
    </v-card-text>
    <v-card-actions>
      <v-spacer></v-spacer>
      <v-btn color="blue darken-1" text @click="close()">Close</v-btn>
      <v-btn color="blue darken-1" text @click="makePayment()">Save</v-btn>
    </v-card-actions>
  </v-card>
</template>