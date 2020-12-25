

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
import { _ } from "../../../common/common-function";
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
      _: _,
      penaltyRate: 3,
      expiredDays: 10,
      origPrice: 0,
      data: new Form({
        penalty: 0,
        price: 0,
        service_fees : 0,
        processing_fees : 0,
      }),
      servicesDialogConfig: {
        dialogStyle: {
          persistent: true,
          maxWidth: "1200px",
          fullscreen: false,
          hideOverlay: true,
        },
      },
      services: [],
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
          this.data.processing_fees = 3;
          this.data.service_fees = 0;
          this.origPrice = data.data.price;
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
    pluckUid(data) {
      if (data.length > 0) {
        return data.map(function (item) {
          return item.uid;
        });
      } else {
        return [];
      }
    },
    calculatePenalty(data) {
      let rentaldate = moment(data.rentaldate);
      let diff = moment().diff(rentaldate, "days", false);
      console.log("diff");
      console.log(diff);
      let overDays = diff - parseInt(this.expiredDays);
      if (overDays > 0) {
        return overDays * this.penaltyRate;
      } else {
        return 0;
      }
    },
    roomServiceUpdated(event) {
      this.services = event.services;
      console.log("services");
      console.log(this.services);
      let price = 0;
      this.services.forEach((service) => {
        price += parseFloat(service.price);
      });

      this.data.service_fees = parseFloat(price);
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
            <v-text-field
              label="Rental"
              type="number"
              step="0.01"
              v-model="data.price"
            ></v-text-field>
          </v-col>
          <v-col cols="12">
            <v-text-field
              label="Service Fees"
              type="number"
              step="0.01"
              v-model="data.service_fees"
            ></v-text-field>
          </v-col>
          <v-col cols="12">
            <v-text-field
              label="Processing Fees"
              type="number"
              step="0.01"
              v-model="data.processing_fees"
            ></v-text-field>
          </v-col>
          <v-col cols="12">
            <v-text-field
              label="Penalty"
              type="number"
              step="0.01"
              v-model="data.penalty"
            ></v-text-field>
          </v-col>
        </v-row>
      </v-container>
    </v-card-text>
    <v-card-actions>
      <services-dialog
        :dialogStyle="servicesDialogConfig.dialogStyle"
        :services="pluckUid(!_.isEmpty(services) ? _.cloneDeep(services) : [])"
        editMode
        @submit="
          (e) => {
            roomServiceUpdated(e);
          }
        "
      >
      <v-btn color="green darken-1" text>Add On Services</v-btn>
      </services-dialog>
      <v-spacer></v-spacer>
      <v-btn color="blue darken-1" text @click="close()">Close</v-btn>
      <v-btn color="blue darken-1" text @click="makePayment()">Save</v-btn>
    </v-card-actions>
  </v-card>
</template>