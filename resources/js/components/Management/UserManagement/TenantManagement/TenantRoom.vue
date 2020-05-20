<template>
  <v-card>
    <v-container class="pa-5">
      <loading></loading>
      <v-row>
        <v-col cols="auto" :class="helpers.managementStyles().centerWrapperClass ">
          <div :class="helpers.managementStyles().subtitleClass">Tenant :</div>
        </v-col>
        <v-col cols="auto" :class="helpers.managementStyles().centerWrapperClass">
          <div :class="helpers.managementStyles().lightSubtitleClass">{{tenant.name}}</div>
        </v-col>
      </v-row>
      <v-row>
        <v-col cols="auto" :class="helpers.managementStyles().centerWrapperClass ">
          <div :class="helpers.managementStyles().subtitleClass">Room :</div>
        </v-col>
        <v-col cols="auto" :class="helpers.managementStyles().centerWrapperClass">
          <div :class="helpers.managementStyles().lightSubtitleClass">{{room.name}}</div>
        </v-col>
      </v-row>
      <v-row>
        <v-col cols="auto" :class="helpers.managementStyles().centerWrapperClass ">
          <div :class="helpers.managementStyles().subtitleClass">Contract :</div>
        </v-col>
        <v-col cols="auto" :class="helpers.managementStyles().centerWrapperClass">
          <div :class="helpers.managementStyles().lightSubtitleClass">{{roomcontract.name}}</div>
        </v-col>
      </v-row>
      <v-row>
        <v-col cols="auto" :class="helpers.managementStyles().centerWrapperClass ">
          <div :class="helpers.managementStyles().subtitleClass">Contract Start Date :</div>
        </v-col>
        <v-col cols="auto" :class="helpers.managementStyles().centerWrapperClass">
          <div
            :class="helpers.managementStyles().lightSubtitleClass"
          >{{roomcontract.startdate | formatDate}}</div>
        </v-col>
      </v-row>

      <v-row>
        <v-col col="12">
          <v-divider class="mx-3" :color="helpers.managementStyles().dividerColor"></v-divider>
        </v-col>
      </v-row>
      <v-row>
        <v-col cols="12" :class="helpers.managementStyles().centerWrapperClass">
          <v-card raised width="100%">
            <v-data-table
              :headers="rentalPaymentHeaders"
              :items="sortedRentalPayments"
              fixed-header
              height="300px"
              :items-per-page="5"
            >
              <template v-slot:top>
                <v-toolbar flat color="white">
                  <v-toolbar-title :class="helpers.managementStyles().subtitleClass">Rental Payment</v-toolbar-title>
                  <v-spacer></v-spacer>
                  <v-btn
                    color="primary"
                    dark
                    class="mb-2"
                    @click="createRentalPayment()"
                  >New Payment</v-btn>
                </v-toolbar>
              </template>
              <template v-slot:item="props">
                <tr>
                  <td>{{props.item.rentaldate | formatDate}}</td>
                  <td>{{props.item.price | toDouble}}</td>
                  <td v-if="props.item.paid">
                    <v-icon small color="success">mdi-check</v-icon>
                  </td>
                  <td v-else>
                    <v-icon small color="danger">mdi-close</v-icon>
                  </td>
                  <td>{{props.item.paymentdate | formatDate}}</td>
                  <td>
                    <v-icon
                      small
                      class="mr-2"
                      @click="openPaymentDialog(props.item.uid, false)"
                      v-if="props.item.paid"
                      color="success"
                    >mdi-pencil</v-icon>
                    <v-icon
                      small
                      class="mr-2"
                      @click="openPaymentDialog(props.item.uid, true)"
                      color="warning"
                      v-else
                    >mdi-currency-usd</v-icon>

                    <confirm-dialog
                      :activatorStyle="deleteButtonConfig.activatorStyle"
                      @confirmed="deleteRentalPayment($event, props.item.uid)"
                    ></confirm-dialog>
                  </td>
                </tr>
              </template>
            </v-data-table>
          </v-card>
        </v-col>
      </v-row>

      <rental-payment-form
        :dialog="paymentDialog"
        :uid="selectedPayment.uid"
        @close="paymentDialog = false"
        @makePayment="updateRentalPaymentDetails($event)"
      ></rental-payment-form>
    </v-container>
  </v-card>
</template>

<script>
import { mapActions } from "vuex";
export default {
  props: {
    roomcontract: {
      type: Object,
      default: () => ({
        name: null,
        startdate: null
      })
    },
    room: {
      type: Object,
      default: () => ({
        name: null,
        price: null
      })
    },
    tenant: {
      type: Object,
      default: () => ({
        name: null
      })
    },
    rentalpayments: {
      type: Array,
      default: () => []
    }
  },
  data: () => ({
    paymentDialog: false,
    payonly: false,
    selectedPayment: {
      uid: ""
    },

    deleteButtonConfig: {
      activatorStyle: {
        block: false,
        color: "error",
        class: "",
        text: "",
        icon: "mdi-trash-can-outline",
        isIcon: true,
        smallIcon: true
      }
    },
    rentalPaymentHeaders: [
      {
        text: "Rental",
        value: "rentaldate"
      },
      { text: "Price", value: "price" },
      {
        text: "Paid",
        value: "paid"
      },
      {
        text: "Payment Date",
        value: "paymentdate"
      },
      { text: "Actions" }
    ]
  }),

  computed: {
    isLoading() {
      return this.$store.getters.isLoading;
    },
    sortedRentalPayments(){
      return this.helpers.sortByDate( this.rentalpayments, 'rentaldate');
    }
  },
  created() {},

  methods: {
    ...mapActions({
      createRentalPaymentAction: "createRentalPayment",
      makePaymentAction: "makePayment",
      deleteRentalPaymentAction: "deleteRentalPayment",
      showLoadingAction: "showLoadingAction",
      endLoadingAction: "endLoadingAction"
    }),
    createRentalPayment() {
      this.showLoadingAction();
      this.$Progress.start();
      this.createRentalPaymentAction({ room_contract_id: this.roomcontract.id })
        .then(data => {
          this.rentalpayments.push(data.data);
          console.log(data.data);
          Toast.fire({
            icon: "success",
            title: "Successful Created. "
          });
          this.$Progress.finish();
          this.endLoadingAction();
        })
        .catch(error => {
          Toast.fire({
            icon: "warning",
            title: "Fail to add the rental!!!!! "
          });
          this.$Progress.finish();
          this.endLoadingAction();
        });
    },
    openPaymentDialog(uid, payonly) {
      this.paymentDialog = true;
      this.selectedPayment.uid = uid;
      this.payonly = payonly;
    },

    updateRentalPaymentDetails(data) {
      var id = data.id;
      var rentalpayment = data;
      this.rentalpayments = this.rentalpayments.map(function(item) {
        if (item.id == id) {
          return rentalpayment;
        } else {
          return item;
        }
      });
    },
    deleteRentalPaymentDetails(data) {
      var id = data.id;
      this.rentalpayments = this.rentalpayments.filter(function(item) {
        return item.id != id;
      });
    },
    deleteRentalPayment($isConfirmed, $uid) {
      if ($isConfirmed) {
        this.$Progress.start();
        this.showLoadingAction();
        console.log('uid');
        console.log($uid);
        this.deleteRentalPaymentAction({ uid: $uid })
          .then(data => {
            Toast.fire({
              icon: "success",
              title: "Successful Deleted. "
            });
            this.deleteRentalPaymentDetails(data.data);
            this.$Progress.finish();
            this.endLoadingAction();
          })
          .catch(error => {
            Toast.fire({
              icon: "warning",
              title: "Fail to delete the tenant!!!!! "
            });
            this.$Progress.finish();
            this.endLoadingAction();
          });
      }
    }
  }
};
</script>