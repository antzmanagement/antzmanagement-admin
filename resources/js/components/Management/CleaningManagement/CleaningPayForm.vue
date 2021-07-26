

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
      paymentMethods: ["cash", "online_transfer", "credit"],
      origPrice: 0,
      data: new Form({
        paymentdate: moment().format("YYYY-MM-DD"),
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
      this.getCleaning();
    },
  },
  mounted() {
    this.getCleaning();
    this.authenticateAction().then((res) => {
    });
  },
  methods: {
    ...mapActions({
      authenticateAction: "authenticate",
      makePaymentAction: "makePayment",
      updateCleaningAction: "updateCleaning",
      getCleaningAction: "getCleaning",
      showLoadingAction: "showLoadingAction",
      endLoadingAction: "endLoadingAction",
    }),
    close() {
      this.$emit("close");
    },
    getCleaning() {
      this.showLoadingAction();
      this.$Progress.start();
      this.getCleaningAction({ uid: this.uid })
        .then((data) => {
          this.data = data.data;
          this.origPrice = data.data.price;
          if (!this.editMode) {
            this.data.paymentdate = moment().format("YYYY-MM-DD");
            this.data.processing_fees = 3;
            this.data.service_fees = 0;
            this.data.paymentmethod = this.paymentMethods[0];
          }
          this.$Progress.finish();
          this.endLoadingAction();
        })
        .catch((error) => {
          Toast.fire({
            icon: "warning",
            title: "Fail to retrieve the cleaning!!!!! ",
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
    updateCleaning() {
      this.showLoadingAction();
      this.$Progress.start();
      this.data.paid = true;
      this.updateCleaningAction(this.data)
        .then((data) => {
          Toast.fire({
            icon: "success",
            title: "Successful Paid. ",
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
      let price = !_.isNaN(parseFloat(this.data.price)) ? parseFloat(this.data.price) : 0;
      switch (this.data.paymentmethod) {
        case 'cash':
          this.data.processing_fees = 3;
          break;
        case 'online_transfer':
          this.data.processing_fees = 0;
          break;
        case 'credit':
          this.data.processing_fees = parseFloat(((price) * 0.02).toFixed(2));
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
          <v-col cols="12">
            <v-text-field
              label="Receipt No"
              v-model="data.receiptno"
            ></v-text-field>
          </v-col>
          <v-col cols="12">
            <v-select
              :items="paymentMethods"
              v-model="data.paymentmethod"
              label="Payment Method"
              @change="updateProcessingFees"
            ></v-select>
          </v-col>
          <v-col cols="12">
            <v-menu
              ref="menu"
              v-model="menu"
              :close-on-content-click="false"
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
              label="Price"
              type="number"
              step="0.01"
              v-model="data.price"
              @change="updateProcessingFees"
              readonly
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
            <v-text-field label="Remark" v-model="data.remark"></v-text-field>
          </v-col>
        </v-row>
      </v-container>
    </v-card-text>
    <v-card-actions>
      <v-spacer></v-spacer>
      <v-btn color="blue darken-1" text @click="close()">Close</v-btn>
      <v-btn
        color="blue darken-1"
        text
        @click="() => (updateCleaning())"
        >Pay</v-btn
      >
    </v-card-actions>
  </v-card>
</template>