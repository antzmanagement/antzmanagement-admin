

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
      menu: false,
      paymentMethods: ["cash", "online_transfer", "eWallet", "credit"],
      penaltyRate: 3,
      expiredDays: 10,
      origPrice: 0,
      data: new Form({
        paymentdate: moment().format("YYYY-MM-DD"),
        penalty: 0,
        price: 0,
        service_fees: 0,
        processing_fees: 0,
        referenceno: 0,
        paymentmethod: "cash",
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
      this.getRentalPayment();
    },
  },
  mounted() {
    this.getRentalPayment();
    this.authenticateAction().then((res) => {});
  },
  methods: {
    ...mapActions({
      authenticateAction: "authenticate",
      makePaymentAction: "makePayment",
      updateRentalPaymentAction: "updateRentalPayment",
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
          this.origPrice = data.data.price;
          if (!this.editMode) {
            this.data.penalty = this.calculatePenalty(data.data);
            this.data.paymentdate = moment().format("YYYY-MM-DD");
            this.data.processing_fees = 3;
            this.data.service_fees = 0;
            this.data.paymentmethod = this.paymentMethods[0];
          }
          this.$Progress.finish();
          this.endLoadingAction();
        })
        .catch((error) => {
          console.log(error);
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
      if (!_.isNumber(parseInt(this.expiredDays))) {
        this.expiredDays = 10;
      }
      let rentaldate = moment(data.rentaldate);
      console.log(rentaldate);
      let diff = moment().diff(rentaldate, "days", false);
      console.log(diff);
      let overDays = diff + 1 - (parseInt(this.expiredDays) || 10);
      console.log(overDays);
      if (overDays > 0) {
        return overDays * this.penaltyRate;
      } else {
        return 0;
      }
    },
    roomServiceUpdated(event) {
      this.services = event.services;
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
    updateRentalPayment() {
      this.showLoadingAction();
      this.$Progress.start();
      this.updateRentalPaymentAction(this.data)
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
            icon: "warning",
            title: "Something went wrong!!!!! ",
          });
          this.$Progress.finish();
          this.endLoadingAction();
          this.close();
        });
    },
    updateProcessingFees() {
      let price = !_.isNaN(parseFloat(this.data.price))
        ? parseFloat(this.data.price)
        : 0;
      let penalty = !_.isNaN(parseFloat(this.data.penalty))
        ? parseFloat(this.data.penalty)
        : 0;
      switch (this.data.paymentmethod) {
        case "cash":
          this.data.processing_fees = 3;
          break;
        case "online_transfer":
        case "eWallet":
          this.data.processing_fees = 0;
          break;
        case "credit":
          this.data.processing_fees = parseFloat(
            ((price + penalty) * 0.02).toFixed(2)
          );
          break;

        default:
          this.data.processing_fees = 0;
          break;
      }
    },
  },
};
</script>

<template>
  <v-card>
    <v-card-text>
      <v-container>
        <v-row>
          <v-col cols="12" v-if="!editMode || data.paid">
            <v-text-field
              label="Reference No"
              v-model="data.referenceno"
            ></v-text-field>
          </v-col>
          <!-- <v-col cols="12">
            <v-text-field
              label="Receive From"
              v-model="data.receive_from"
            ></v-text-field>
          </v-col> -->
          <!-- <v-col cols="12">
            <v-text-field
              label="Issue By"
              v-model="data.issue_by"
            ></v-text-field>
          </v-col> -->
          <v-col cols="12" v-if="!editMode || data.paid">
            <v-select
              :items="paymentMethods"
              v-model="data.paymentmethod"
              label="Payment Method"
              @change="updateProcessingFees"
              :disabled="data.paid == true"
            ></v-select>
          </v-col>
          <v-col cols="12" v-if="!editMode || data.paid">
            <v-menu
              ref="menu"
              v-model="menu"
              :close-on-content-click="false"
              transition="scale-transition"
              offset-y
              :disabled="data.paid == true"
            >
              <template v-slot:activator="{ on }">
                <v-text-field
                  :disabled="data.paid == true"
                  v-model="data.paymentdate"
                  label="Payment Date"
                  readonly
                  v-on="on"
                ></v-text-field>
              </template>
              <v-date-picker
                v-model="data.paymentdate"
                no-title
                scrollable
              ></v-date-picker>
            </v-menu>
          </v-col>
          <v-col cols="12">
            <v-text-field
              label="Rental"
              type="number"
              step="0.01"
              v-model="data.price"
              @change="updateProcessingFees"
              :disabled="data.paid == true"
            ></v-text-field>
          </v-col>
          <!-- <v-col cols="12">
            <v-text-field
              label="Service Fees"
              type="number"
              step="0.01"
              v-model="data.service_fees"
            ></v-text-field>
          </v-col> -->
          <v-col cols="12">
            <v-text-field
              label="Processing Fees"
              type="number"
              step="0.01"
              v-model="data.processing_fees"
              :disabled="data.paid == true"
            ></v-text-field>
          </v-col>
          <v-col cols="12">
            <v-text-field
              label="Penalty"
              type="number"
              step="0.01"
              v-model="data.penalty"
              @change="updateProcessingFees"
              :disabled="data.paid == true"
            ></v-text-field>
          </v-col>
          <v-col cols="12">
            <v-text-field label="Remark" v-model="data.remark"></v-text-field>
          </v-col>
          <!-- <v-col cols="12" v-if="editMode">
            <div>Paid Status</div>
            <v-radio-group v-model="data.paid" row>
              <v-radio label="Paid" :value="1"></v-radio>
              <v-radio label="Unpaid" :value="0"></v-radio>
            </v-radio-group>
          </v-col> -->
        </v-row>
      </v-container>
    </v-card-text>
    <v-card-actions>
      <!-- <services-dialog
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
      </services-dialog> -->
      <v-spacer></v-spacer>
      <v-btn color="blue darken-1" text @click="close()">Close</v-btn>
      <v-btn
        color="blue darken-1"
        text
        @click="() => (editMode ? updateRentalPayment() : makePayment())"
        >{{editMode ? 'Save' : 'Pay'}}</v-btn
      >
    </v-card-actions>
  </v-card>
</template>