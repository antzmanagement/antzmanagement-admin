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
            <v-data-table :headers="rentalPaymentHeaders" :items="rentalpayments" fixed-header height="300px" items-per-page="5">
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
                <tr @click="showTenant(props.item)">
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
                    <v-icon small class="mr-2" @click="editItem(item)">mdi-pencil</v-icon>
                    <v-icon small @click="deleteItem(item)">mdi-delete</v-icon>
                  </td>
                </tr>
              </template>
            </v-data-table>
          </v-card>
        </v-col>
      </v-row>
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
    }
  },
  created() {},

  methods: {
    ...mapActions({
      createRentalPaymentAction: "createRentalPayment",
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
    }
  }
};
</script>