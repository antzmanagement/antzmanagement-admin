

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
import OtherPaymentDialog from "./OtherPaymentDialog.vue";
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
      otherPaymentDialog: false,
      otherpayments: [],
      penaltyRate: 3,
      expiredDays: 9,
      origPrice: 0,
      data: new Form({
        paymentdate: moment().format("YYYY-MM-DD"),
        penalty: 0,
        price: 0,
        service_fees: 0,
        processing_fees: 0,
        referenceno: 0,
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
      this.getPayment();
    },
    otherPaymentDialog: function (val) {
      if (val) {
        this.otherpayments = this.data.otherpayments || [];
      } else {
        this.otherpayments = [];
      }
      console.log("dialog", val);
    },
  },
  mounted() {
    this.getPayment();
    this.authenticateAction().then((res) => {
      console.log(res);
    });
  },
  methods: {
    ...mapActions({
      authenticateAction: "authentication",
      makeAddOnPaymentAction: "makeAddOnPayment",
      updatePaymentAction: "updatePayment",
      getPaymentAction: "getPayment",
      showLoadingAction: "showLoadingAction",
      endLoadingAction: "endLoadingAction",
    }),
    close() {
      this.$emit("close");
    },
    getPayment() {
      this.showLoadingAction();
      this.$Progress.start();
      this.getPaymentAction({ uid: this.uid })
        .then((data) => {
          this.data = data.data;
          this.origPrice = data.data.price;
          if (!this.editMode) {
            this.data.paymentdate = moment().format("YYYY-MM-DD");
          }

          this.data.services = this.pluckUid(
            _.get(data, `data.services`) || []
          );
          this.data.otherpayments = _.map(
            this.data.otherpayments,
            (otherpayment) => {
              otherpayment.price = _.get(otherpayment, `pivot.price`) || 0;
              return otherpayment;
            }
          );

          console.log(data);
          this.$Progress.finish();
          this.endLoadingAction();
        })
        .catch((error) => {
          console.log(error);
          Toast.fire({
            icon: "warning",
            title: "Fail to retrieve the payment!!!!! ",
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
      console.log(this.data);
      this.makeAddOnPaymentAction(this.data)
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
          <v-col cols="12">
            <v-text-field
              label="Payment Method"
              v-model="data.paymentmethod"
            ></v-text-field>
          </v-col>
          <v-col cols="12">
            <v-menu
              ref="menu"
              v-model="menu"
              :close-on-content-click="true"
              transition="scale-transition"
              offset-y
            >
              <template v-slot:activator="{ on }">
                <v-text-field
                  v-model="data.paymentdate"
                  label="Payment Date"
                  readonly
                  v-on="on"
                  :error-messages="
                    helpers.isEmpty(data.paymentdate)
                      ? 'Payment Date is required'
                      : ''
                  "
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
              label="Service Fees"
              type="number"
              step="0.01"
              v-model="data.price"
              readonly
            ></v-text-field>
          </v-col>
          <v-col cols="12">
            <v-text-field
              label="Other Charges"
              type="number"
              step="0.01"
              v-model="data.other_charges"
              readonly
            ></v-text-field>
          </v-col>
          <v-col cols="12">
            <v-text-field label="Remark" v-model="data.remark"></v-text-field>
          </v-col>
          <v-col cols="12" v-if="editMode">
            <div>Paid Status</div>
            <v-radio-group v-model="data.paid" row>
              <v-radio label="Paid" :value="1"></v-radio>
              <v-radio label="Unpaid" :value="0"></v-radio>
            </v-radio-group>
          </v-col>
        </v-row>
      </v-container>
    </v-card-text>
    <v-card-actions>
      <services-dialog
        :dialogStyle="servicesDialogConfig.dialogStyle"
        :services="!_.isEmpty(data.services) ? _.cloneDeep(data.services) : []"
        @submit="
          (e) => {
            roomServiceUpdated(e);
          }
        "
      >
        <v-btn color="green darken-1" text>Add On Services</v-btn>
      </services-dialog>
      <other-payment-dialog :otherpayments="data.otherpayments"></other-payment-dialog>
      <v-spacer></v-spacer>
      <v-btn color="blue darken-1" text @click="close()">Close</v-btn>
      <v-btn color="blue darken-1" text @click="makePayment()">Save</v-btn>
    </v-card-actions>
  </v-card>
</template>