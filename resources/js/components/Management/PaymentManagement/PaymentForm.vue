

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
      moment : moment,
      _: _,
      menu: false,
      otherPaymentDialog: false,
      penaltyRate: 3,
      otherpayments : [],
      expiredDays: 9,
      origPrice: 0,
      data: new Form({
        price: 0,
        other_charges: 0,
        remark: "",
        services: [],
        paymentdate: moment().format("YYYY-MM-DD"),
        room_contract_id: "",
        otherpayments: [],
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
    otherPaymentDialog: function (val) {
      if(val){
        this.otherpayments = this.data.otherpayments || [];
      }else{
        this.otherpayments = [];
      }
      console.log('dialog', val);
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
    otherPaymentUpdated() {
      let price = 0;
      this.data.otherpayments.forEach((item) => {
        price += parseFloat(item.price);
      });

      this.data.other_charges = parseFloat(price);
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
              readonly
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
      <services-dialog
        :dialogStyle="servicesDialogConfig.dialogStyle"
        :services="!_.isEmpty(data.services) ? _.cloneDeep(data.services) : []"
        editMode
        @submit="
          (e) => {
            roomServiceUpdated(e);
          }
        "
      >
        <v-btn color="green darken-1" text>Add On Services</v-btn>
      </services-dialog>
      <v-btn color="yellow darken-4" text @click="otherPaymentDialog = true"
        >Other Payment</v-btn
      >
      <v-spacer></v-spacer>
      <v-btn color="blue darken-1" text @click="close()">Close</v-btn>
      <v-btn color="blue darken-1" text @click="createPayment()">Save</v-btn>
    </v-card-actions>

    <v-dialog v-model="otherPaymentDialog" >
      <v-card>
        <v-toolbar dark color="primary">
          <v-btn icon dark @click="otherPaymentDialog = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
          <v-toolbar-title>Other Payment</v-toolbar-title>
          <v-spacer></v-spacer>
          <v-toolbar-items>
            <v-btn dark text :disabled="isLoading" @click="() => {
              data.otherpayments = otherpayments;
              otherPaymentDialog = false;
              otherPaymentUpdated();
              }"
              >Save</v-btn
            >
          </v-toolbar-items>
        </v-toolbar>
        <v-card-text>
          <v-container>
            <v-row>
              <v-col cols="12">
                <v-data-table
                  :items="otherpayments || []"
                  :headers="[
                    {
                      text: 'Name',
                    },
                    {
                      text: 'Price',
                    },
                    {
                      text: 'Actions',
                    },
                  ]"
                >
                  <template v-slot:top>
                    <v-toolbar flat color="white">
                      <v-spacer></v-spacer>
                      <v-btn
                        @click="
                          () => {
                            otherpayments.push({
                              id: moment().valueOf(),
                              name: '',
                              price: 0,
                            });
                          }
                        "
                        >Add Other Payment</v-btn
                      >
                    </v-toolbar>
                  </template>
                  <template v-slot:item="props">
                    <tr>
                      <td>
                        <v-text-field
                          label="Item Name"
                          v-model="props.item.name"
                        ></v-text-field>
                      </td>
                      <td>
                        <v-text-field
                          label="Item Price"
                          type="number"
                          step="0.01"
                          v-model="props.item.price"
                        ></v-text-field>
                      </td>
                      <td>
                        <v-btn
                          icon
                          color="red"
                          @click="
                            () => {
                              otherpayments = (
                                otherpayments || []
                              ).filter(function (item) {
                                return item.id != props.item.id;
                              });
                            }
                          "
                          ><v-icon>mdi-trash-can-outline</v-icon></v-btn
                        >
                      </td>
                    </tr>
                  </template>
                </v-data-table>
              </v-col>
            </v-row>
          </v-container>
        </v-card-text>
      </v-card>
    </v-dialog>
  </v-card>
</template>