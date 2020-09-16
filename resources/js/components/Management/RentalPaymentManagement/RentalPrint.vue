<template>
  <span class="d-inline-block">
    <div class="d-none" id="printMe">
      <v-container>
        <v-row>
          <v-col cols="12" :class="helpers.managementStyles().centerWrapperClass ">
            <div class="h5 my-5 font-weight-bold">Payment Receipt</div>
          </v-col>
        </v-row>
        <v-row>
          <v-col cols="3" :class="helpers.managementStyles().centerWrapperClass ">
            <div :class="helpers.managementStyles().subtitleClass">Payment Id</div>
          </v-col>
          <v-col cols="1" :class="helpers.managementStyles().centerWrapperClass ">
            <div :class="helpers.managementStyles().subtitleClass">:</div>
          </v-col>
          <v-col cols="8">
            <div :class="helpers.managementStyles().lightSubtitleClass">{{rentalpayment.uid}}</div>
          </v-col>
        </v-row>
        <v-row>
          <v-col cols="3" :class="helpers.managementStyles().centerWrapperClass ">
            <div :class="helpers.managementStyles().subtitleClass">Tenant</div>
          </v-col>
          <v-col cols="1" :class="helpers.managementStyles().centerWrapperClass ">
            <div :class="helpers.managementStyles().subtitleClass">:</div>
          </v-col>
          <v-col cols="8">
            <div :class="helpers.managementStyles().lightSubtitleClass">{{tenant.name}}</div>
          </v-col>
        </v-row>
        <v-row>
          <v-col cols="3" :class="helpers.managementStyles().centerWrapperClass ">
            <div :class="helpers.managementStyles().subtitleClass">Room</div>
          </v-col>
          <v-col cols="1" :class="helpers.managementStyles().centerWrapperClass ">
            <div :class="helpers.managementStyles().subtitleClass">:</div>
          </v-col>
          <v-col cols="8">
            <div :class="helpers.managementStyles().lightSubtitleClass">{{room.name}}</div>
          </v-col>
        </v-row>
        <v-row>
          <v-col cols="3" :class="helpers.managementStyles().centerWrapperClass ">
            <div :class="helpers.managementStyles().subtitleClass">Contract</div>
          </v-col>
          <v-col cols="1" :class="helpers.managementStyles().centerWrapperClass ">
            <div :class="helpers.managementStyles().subtitleClass">:</div>
          </v-col>
          <v-col cols="8">
            <div :class="helpers.managementStyles().lightSubtitleClass">{{roomcontract.name}}</div>
          </v-col>
        </v-row>
        <v-row>
          <v-col cols="3" :class="helpers.managementStyles().centerWrapperClass ">
            <div :class="helpers.managementStyles().subtitleClass">Contract Start Date</div>
          </v-col>
          <v-col cols="1" :class="helpers.managementStyles().centerWrapperClass ">
            <div :class="helpers.managementStyles().subtitleClass">:</div>
          </v-col>
          <v-col cols="8">
            <div
              :class="helpers.managementStyles().lightSubtitleClass"
            >{{roomcontract.startdate | formatDate}}</div>
          </v-col>
        </v-row>
        <v-row>
          <v-col cols="3" :class="helpers.managementStyles().centerWrapperClass ">
            <div :class="helpers.managementStyles().subtitleClass">Rental</div>
          </v-col>
          <v-col cols="1" :class="helpers.managementStyles().centerWrapperClass ">
            <div :class="helpers.managementStyles().subtitleClass">:</div>
          </v-col>
          <v-col cols="8">
            <div
              :class="helpers.managementStyles().lightSubtitleClass"
            >{{ rentalpayment.rentaldate | formatDate }}</div>
          </v-col>
        </v-row>
        <v-row>
          <v-col cols="3" :class="helpers.managementStyles().centerWrapperClass ">
            <div :class="helpers.managementStyles().subtitleClass">Payment Date</div>
          </v-col>
          <v-col cols="1" :class="helpers.managementStyles().centerWrapperClass ">
            <div :class="helpers.managementStyles().subtitleClass">:</div>
          </v-col>
          <v-col cols="8">
            <div
              :class="helpers.managementStyles().lightSubtitleClass"
            >{{ rentalpayment.paymentdate | formatDate }}</div>
          </v-col>
        </v-row>
        <v-row>
          <v-col cols="3" :class="helpers.managementStyles().centerWrapperClass ">
            <div :class="helpers.managementStyles().subtitleClass">Price</div>
          </v-col>
          <v-col cols="1" :class="helpers.managementStyles().centerWrapperClass ">
            <div :class="helpers.managementStyles().subtitleClass">:</div>
          </v-col>
          <v-col cols="8">
            <div
              :class="helpers.managementStyles().lightSubtitleClass"
            >RM {{ rentalpayment.price | toDouble}}</div>
          </v-col>
        </v-row>
        <v-row>
          <v-col cols="3" :class="helpers.managementStyles().centerWrapperClass ">
            <div :class="helpers.managementStyles().subtitleClass">Penalty</div>
          </v-col>
          <v-col cols="1" :class="helpers.managementStyles().centerWrapperClass ">
            <div :class="helpers.managementStyles().subtitleClass">:</div>
          </v-col>
          <v-col cols="8">
            <div
              :class="helpers.managementStyles().lightSubtitleClass"
            >RM {{ rentalpayment.penalty | toDouble }}</div>
          </v-col>
        </v-row>
        <v-row>
          <v-col cols="3" :class="helpers.managementStyles().centerWrapperClass ">
            <div :class="helpers.managementStyles().subtitleClass">Total Paid</div>
          </v-col>
          <v-col cols="1" :class="helpers.managementStyles().centerWrapperClass ">
            <div :class="helpers.managementStyles().subtitleClass">:</div>
          </v-col>
          <v-col cols="8">
            <div
              :class="helpers.managementStyles().lightSubtitleClass"
            >RM {{ parseFloat(rentalpayment.penalty) + parseFloat(rentalpayment.price) | toDouble }}</div>
          </v-col>
        </v-row>
      </v-container>
    </div>
    <v-btn
      :class="buttonStyle.class"
      tile
      :color="buttonStyle.color"
      :block="buttonStyle.block"
      :elevation="buttonStyle.elevation"
      v-on="on"
      :icon="buttonStyle.isIcon"
      :disabled="isLoading"
      @click="print"
    >
      <v-icon>{{buttonStyle.icon}}</v-icon>
      {{buttonStyle.text}}
    </v-btn>
  </span>
</template>

<script>
import { mapActions } from "vuex";
export default {
  props: {
    roomcontract: {
      type: Object,
      default: () => ({
        name: null,
        startdate: null,
      }),
    },
    room: {
      type: Object,
      default: () => ({
        name: null,
        price: null,
      }),
    },
    tenant: {
      type: Object,
      default: () => ({
        name: null,
      }),
    },
    rentalpayment: {
      type: Object,
      default: () => ({
        name: null,
      }),
    },
    buttonStyle: {
      type: Object,
      default: () => ({
        block: true,
        color: "primary",
        class: "ma-1",
        text: "Print",
        icon: "",
        elevation: 5,
        isIcon: false,
      }),
    },
  },
  data: () => ({
    paymentDialog: false,
    payonly: false,
    selectedTenant: "",
    selectedPayment: {
      uid: "",
    },
    tenants: [],
    deleteButtonConfig: {
      activatorStyle: {
        block: false,
        color: "error",
        class: "",
        text: "",
        icon: "mdi-trash-can-outline",
        isIcon: true,
        smallIcon: true,
      },
    },
    rentalPaymentHeaders: [
      {
        text: "Rental",
        value: "rentaldate",
      },
      { text: "Price", value: "price" },
      {
        text: "Paid",
        value: "paid",
      },
      {
        text: "Payment Date",
        value: "paymentdate",
      },
      { text: "Actions" },
    ],
  }),

  computed: {
    isLoading() {
      return this.$store.getters.isLoading;
    },
    sortedRentalPayments() {
      return this.helpers.sortByDate(this.rentalpayments, "rentaldate");
    },
  },
  created() {
    this.$Progress.start();
    this.showLoadingAction();
    this.getTenantsAction({ pageNumber: -1, pageSize: -1 })
      .then((data) => {
        this.tenants = data.data;
        this.$Progress.finish();
        this.endLoadingAction();
      })
      .catch((error) => {
        Toast.fire({
          icon: "warning",
          title: "Fail to retrieve the tenants!!!!! ",
        });
        this.$Progress.finish();
        this.endLoadingAction();
      });
  },

  methods: {
    ...mapActions({
      getTenantsAction: "getTenants",
      transferRoomContractAction: "transferRoomContract",
      createRentalPaymentAction: "createRentalPayment",
      deleteRentalPaymentAction: "deleteRentalPayment",
      showLoadingAction: "showLoadingAction",
      endLoadingAction: "endLoadingAction",
    }),
    print() {
      this.$htmlToPaper("printMe");
    },
  },
};
</script>