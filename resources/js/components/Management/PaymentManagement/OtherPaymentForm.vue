

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
    roomcontractid: {
      type: String,
      default: "",
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
      penaltyRate: 3,
      expiredDays: 9,
      origPrice: 0,
      data: new Form({
        price: 0,
        other_charges: 0,
        remark: "",
        services: [],
        paymentdate: moment().format("YYYY-MM-DD"),
        room_contract_id: "",
        paymenttype: "other",
      }),
      servicesDialogConfig: {
        dialogStyle: {
          persistent: true,
          maxWidth: "1200px",
          fullscreen: false,
          hideOverlay: true,
        },
      },
    };
  },

  computed: {
    isLoading() {
      return this.$store.getters.isLoading;
    },
  },
  watch: {
    uid: function (val) {
      console.log("room contract changed");
      console.log(val);
    },
    roomcontractid: function (val) {
      console.log("room contract changed");
      console.log(val);
    },
  },
  mounted() {},
  methods: {
    ...mapActions({
      createPaymentAction: "createPayment",
      showLoadingAction: "showLoadingAction",
      endLoadingAction: "endLoadingAction",
    }),
    close() {
      this.$emit("close");
      this.data.reset();
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
      let price = 0;
      event.services.forEach((service) => {
        price += parseFloat(service.price);
      });

      this.data.price = parseFloat(price);
      this.data.services = this.pluckUid(event.services);
    },
    createPayment() {
      console.log(this.data);
      if (!this.roomcontractid) {
        Toast.fire({
          icon: "warning",
          title: "Room Contract Not Found!!!!! ",
        });
        return;
      }
      this.data.room_contract_id = this.roomcontractid;
      this.showLoadingAction();
      this.$Progress.start();
      this.createPaymentAction(this.data)
        .then((data) => {
          Toast.fire({
            icon: "success",
            title: "Successful Created. ",
          });
          this.$Progress.finish();
          this.endLoadingAction();
          this.$emit("createPayment", data.data);
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
            ></v-text-field>
          </v-col>
          <v-col cols="12">
            <v-textarea
              name="input-7-1"
              label="Remark"
              v-model="data.remark"
            ></v-textarea>
          </v-col>
        </v-row>
      </v-container>
    </v-card-text>
    <v-card-actions>
      <v-btn color="blue darken-1" text @click="close()">Close</v-btn>
      <v-btn color="blue darken-1" text @click="createPayment()">Save</v-btn>
    </v-card-actions>
  </v-card>
</template>