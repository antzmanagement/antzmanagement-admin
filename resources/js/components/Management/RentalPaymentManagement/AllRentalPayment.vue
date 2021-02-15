
<script>
import { mapActions } from "vuex";
export default {
  data() {
    return {
      editMode: false,
      paymentDialog: false,
      selectedPayment: {
        uid: "",
      },
      selectedRental: {
        roomcontract: {
          room: {},
          tenant: {},
        },
        price: 0,
        penalty: 0,
        rentaldate: "",
        paymentdate: "",
        uid: "",
      },
      rentalPaymentTotal: 0,
      paymentTotal: 0,
      data: [],
      paymentData: [],
      loading: true,
      paymentLoading: true,
      options: {},
      paymentOptions: {},
      rentalPaymentFilterGroup: new Form({
        rooms: [],
        selectedRooms: [],
        keyword: null,
        fromdate: null,
        todate: null,
        paid: null,
      }),
      paymentFilterGroup: new Form({
        rooms: [],
        selectedRooms: [],
        keyword: null,
        fromdate: null,
        todate: null,
        paid: null,
      }),
      rentalPaymentFilterDialogConfig: {
        buttonStyle: {
          block: false,
          class: "",
          text: "Filter",
          icon: "mdi-magnify",
          isIcon: false,
          color: "primary",
        },
        dialogStyle: {
          persistent: true,
          maxWidth: "1200px",
          fullscreen: false,
          hideOverlay: true,
        },
      },

      deleteRentalButtonConfig: {
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
      rentalPrintButtonConfig: {
        buttonStyle: {
          block: false,
          color: "success",
          class: "",
          text: "",
          icon: "mdi-printer",
          isIcon: true,
          smallIcon: true,
        },
      },
      rentalPaymentFormDialogConfig: {
        buttonStyle: {
          block: true,
          class: "title font-weight-bold ma-2",
          text: "Add RentalPayment",
          icon: "mdi-plus",
          isIcon: false,
          color: "primary",
          evalation: "5",
        },
      },
      headers: [
        {
          text: "Sequence No",
        },
        {
          text: "Tenant",
        },
        {
          text: "Room Contract",
        },
        {
          text: "Room",
        },
        {
          text: "Rental Date",
        },
        {
          text: "Rental Price",
        },
        {
          text: "Penalty",
        },
        {
          text: "Processing Fees",
        },
        {
          text: "Service Fees",
        },
        {
          text: "Paid",
        },
        {
          text: "Payment Date",
        },
        {
          text: "Action",
        },
      ],
      paymentHeaders: [
        {
          text: "Sequence No",
          value: "sequence",
        },
        {
          text: "Tenant",
        },
        {
          text: "Room Contract",
        },
        {
          text: "Room",
        },
        { text: "Payment Date", value: "paymentdate" },
        { text: "Price", value: "price" },
        { text: "Other Charges", value: "other_charges" },
        {
          text: "Services",
          value: "services",
        },
        { text: "Remark", value: "remark" },
        { text: "Actions" },
      ],
    };
  },
  watch: {
    options: {
      handler() {
        this.getRentalPayments();
      },
      deep: true,
    },
    paymentOptions: {
      handler() {
        this.getPayments();
      },
      deep: true,
    },
  },
  computed: {
    isLoading() {
      return this.$store.getters.isLoading;
    },
    keywordEmpty() {
      return this.helpers.isEmpty(this.rentalPaymentFilterGroup.keyword);
    },
    fromdateEmpty() {
      return this.helpers.isEmpty(this.rentalPaymentFilterGroup.fromdate);
    },
    todateEmpty() {
      return this.helpers.isEmpty(this.rentalPaymentFilterGroup.todate);
    },
    roomsEmpty() {
      return this.helpers.isEmpty(this.rentalPaymentFilterGroup.selectedRooms);
    },
  },
  created() {},
  mounted() {
    this.getRentalPayments();
    this.getPayments();
  },
  methods: {
    ...mapActions({
      getRentalPaymentsAction: "getRentalPayments",
      filterRentalPaymentsAction: "filterRentalPayments",
      getPaymentsAction: "getPayments",
      filterPaymentsAction: "filterPayments",
      deleteRentalPaymentAction: "deleteRentalPayment",
      deletePaymentAction: "deletePayment",
      showLoadingAction: "showLoadingAction",
      endLoadingAction: "endLoadingAction",
    }),
    openPaymentDialog(uid, mode) {
      this.paymentDialog = true;
      this.editMode = mode;
      this.selectedPayment.uid = uid;
    },
    initRentalPaymentFilter(filterGroup) {
      this.rentalPaymentFilterGroup.reset();
      if (filterGroup.tenant) {
        this.rentalPaymentFilterGroup.tenant_id = filterGroup.tenant.id;
        this.rentalPaymentFilterGroup.tenant = filterGroup.tenant;
      }
      if (filterGroup.room) {
        this.rentalPaymentFilterGroup.room_id = filterGroup.room.id;
        this.rentalPaymentFilterGroup.room = filterGroup.room;
      }
      if (filterGroup.fromdate) {
        this.rentalPaymentFilterGroup.fromdate = filterGroup.fromdate;
      }
      if (filterGroup.sequence) {
        this.rentalPaymentFilterGroup.sequence = filterGroup.sequence;
      }
      if (filterGroup.todate) {
        this.rentalPaymentFilterGroup.todate = filterGroup.todate;
      }
      if (filterGroup.penalty === 1 || filterGroup.penalty === 0) {
        this.rentalPaymentFilterGroup.penalty =
          filterGroup.penalty;
      }
      if (filterGroup.paid === 1 || filterGroup.paid === 0) {
        this.rentalPaymentFilterGroup.paid =
          filterGroup.paid;
      }

      this.options.page = 1;
      this.getRentalPayments();
    },
    initPaymentFilter(filterGroup) {
      this.paymentFilterGroup.reset();
      if (filterGroup.tenant) {
        this.paymentFilterGroup.tenant_id = filterGroup.tenant.id;
        this.paymentFilterGroup.tenant = filterGroup.tenant;
      }
      if (filterGroup.room) {
        this.paymentFilterGroup.room_id = filterGroup.room.id;
        this.paymentFilterGroup.room = filterGroup.room;
      }
      if (filterGroup.fromdate) {
        this.paymentFilterGroup.fromdate = filterGroup.fromdate;
      }
      if (filterGroup.services) {
        this.paymentFilterGroup.service_ids =
          _.map(filterGroup.services, "id") || [];
        this.paymentFilterGroup.services = filterGroup.services;
      }
      if (filterGroup.sequence) {
        this.paymentFilterGroup.sequence = filterGroup.sequence;
      }
      if (filterGroup.todate) {
        this.paymentFilterGroup.todate = filterGroup.todate;
      }
      if (filterGroup.penalty === 1 || filterGroup.penalty === 0) {
        this.paymentFilterGroup.penalty =
          filterGroup.penalty;
      }
      if (filterGroup.paid === 1 || filterGroup.paid === 0) {
        this.paymentFilterGroup.paid =
          filterGroup.paid;
      }

      this.paymentOptions.page = 1;
      this.getPayments();
    },
    showRentalPayment($data) {
      this.$router.push("/rentalpayment/" + $data.uid);
    },
    updateRentalPaymentDetails(data) {
      var id = data.id;
      var rentalpayment = data;
      console.log("rentalpayment");
      console.log(rentalpayment);
      this.data = this.data.map(function (item) {
        if (item.id == id) {
          console.log("Found");
          return rentalpayment;
        } else {
          return item;
        }
      });
    },
    deletePaymentDetails(data) {
      var id = data.id;
      this.paymentData = this.paymentData.filter(function (item) {
        return item.id != id;
      });
    },
    deletePayment($uid) {
      this.$Progress.start();
      this.showLoadingAction();
      this.deletePaymentAction({ uid: $uid })
        .then((data) => {
          Toast.fire({
            icon: "success",
            title: "Successful Deleted. ",
          });
          this.deletePaymentDetails(data.data);
          this.$Progress.finish();
          this.endLoadingAction();
        })
        .catch((error) => {
          Toast.fire({
            icon: "warning",
            title: "Fail to delete the payment!!!!! ",
          });
          this.$Progress.finish();
          this.endLoadingAction();
        });
    },
    deleteRentalPaymentDetails(data) {
      var id = data.id;
      this.data = this.data.filter(function (item) {
        return item.id != id;
      });
    },
    deleteRentalPayment($uid) {
      this.$Progress.start();
      this.showLoadingAction();
      this.deleteRentalPaymentAction({ uid: $uid })
        .then((data) => {
          Toast.fire({
            icon: "success",
            title: "Successful Deleted. ",
          });
          this.deleteRentalPaymentDetails(data.data);
          this.$Progress.finish();
          this.endLoadingAction();
        })
        .catch((error) => {
          Toast.fire({
            icon: "warning",
            title: "Fail to delete the tenant!!!!! ",
          });
          this.$Progress.finish();
          this.endLoadingAction();
        });
    },
    getRentalPayments() {
      this.loading = true;
      const { sortBy, sortDesc, page, itemsPerPage } = this.options;

      var totalResult = itemsPerPage;
      //Show All Items
      if (totalResult == -1) {
        this.rentalPaymentFilterGroup.pageNumber = -1;
        this.rentalPaymentFilterGroup.pageSize = -1;
      } else {
        this.rentalPaymentFilterGroup.pageNumber = page;
        this.rentalPaymentFilterGroup.pageSize = itemsPerPage;
      }

      console.log(this.rentalPaymentFilterGroup);
      this.filterRentalPaymentsAction(this.rentalPaymentFilterGroup)
        .then((data) => {
          if (data.data) {
            this.data = data.data;
          } else {
            this.data = [];
          }
          this.rentalPaymentTotal = data.totalResult;
          this.loading = false;
        })
        .catch((error) => {
          this.loading = false;
          Toast.fire({
            icon: "warning",
            title: "Something went wrong... ",
          });
        });
    },
    getPayments() {
      this.paymentLoading = true;
      const { sortBy, sortDesc, page, itemsPerPage } = this.paymentOptions;

      var totalResult = itemsPerPage;
      //Show All Items
      if (totalResult == -1) {
        this.paymentFilterGroup.pageNumber = -1;
        this.paymentFilterGroup.pageSize = -1;
      } else {
        this.paymentFilterGroup.pageNumber = page;
        this.paymentFilterGroup.pageSize = itemsPerPage;
      }

      console.log(this.paymentFilterGroup);
      this.filterPaymentsAction(this.paymentFilterGroup)
        .then((data) => {
          if (data.data) {
            this.paymentData = data.data;
          } else {
            this.paymentData = [];
          }
          this.paymentTotal = data.totalResult;
          this.paymentLoading = false;
          console.log('data');
          console.log(this.paymentData);
        })
        .catch((error) => {
          this.paymentLoading = false;
          Toast.fire({
            icon: "warning",
            title: "Something went wrong... ",
          });
        });
    },
    print(data) {
      this.selectedRental = data;
      console.log(this.selectedRental);
      this.showLoadingAction();
      setTimeout(() => {
        this.endLoadingAction();
        this.$htmlToPaper("printMe");
      }, 2000);
    },
  },
};
</script>


<template>
  <v-app>
    <navbar></navbar>
    <v-content :class="helpers.managementStyles().backgroundClass">
      <v-container class="fill-height" fluid>
        <loading></loading>
        <v-row justify="center" align="center" class="mx-3">
          <v-col cols="12">
            <v-card raised>
              <v-card-subtitle v-show="rentalPaymentFilterGroup.fromdate">
                From Date :
                <v-chip class="mx-2">{{
                  rentalPaymentFilterGroup.fromdate
                }}</v-chip>
              </v-card-subtitle>
              <v-card-subtitle v-show="rentalPaymentFilterGroup.todate">
                To Date :
                <v-chip class="mx-2">{{
                  rentalPaymentFilterGroup.todate
                }}</v-chip>
              </v-card-subtitle>
              <v-card-subtitle v-show="rentalPaymentFilterGroup.sequence">
                Sequence No :
                <v-chip class="mx-2">{{
                  rentalPaymentFilterGroup.sequence
                }}</v-chip>
              </v-card-subtitle>
              <v-card-subtitle v-show="rentalPaymentFilterGroup.tenant">
                Tenant :
                <v-chip class="mx-2">{{
                  _.get(rentalPaymentFilterGroup, ["tenant", "name"]) || "N/A"
                }}</v-chip>
              </v-card-subtitle>
              <v-card-subtitle v-show="rentalPaymentFilterGroup.room">
                Room :
                <v-chip class="mx-2">{{
                  _.get(rentalPaymentFilterGroup, ["room", "unit"]) || "N/A"
                }}</v-chip>
              </v-card-subtitle>
              <v-card-subtitle
                v-show="rentalPaymentFilterGroup.penalty === 1 || rentalPaymentFilterGroup.penalty === 0 "
              >
                Penalty :
                <v-chip class="mx-2">{{
                  rentalPaymentFilterGroup.penalty ? "Yes" : "No"
                }}</v-chip>
              </v-card-subtitle>
              <v-card-subtitle v-show="rentalPaymentFilterGroup.paid === 1 || rentalPaymentFilterGroup.paid === 0">
                Paid :
                <v-chip class="mx-2">{{
                  rentalPaymentFilterGroup.paid ? "Yes" : "No"
                }}</v-chip>
              </v-card-subtitle>
            </v-card>
          </v-col>
        </v-row>
        <v-row justify="center" align="center" class="ma-3">
          <v-col cols="12">
            <v-card class="pa-8" raised>
              <v-data-table
                :headers="headers"
                :items="data"
                :options.sync="options"
                :server-items-length="rentalPaymentTotal"
                :loading="loading"
              >
                <template v-slot:top>
                  <v-toolbar flat color="white">
                    <v-toolbar-title
                      :class="helpers.managementStyles().subtitleClass"
                      >All Rental Payment</v-toolbar-title
                    >
                    <v-spacer></v-spacer>
                    <rental-payment-filter-dialog
                      :buttonStyle="rentalPaymentFilterDialogConfig.buttonStyle"
                      :dialogStyle="rentalPaymentFilterDialogConfig.dialogStyle"
                      @submitFilter="initRentalPaymentFilter($event)"
                    ></rental-payment-filter-dialog>
                  </v-toolbar>
                </template>
                <template v-slot:item="props">
                  <tr>
                    <td>{{ props.item.sequence }}</td>
                    <td>{{ props.item.roomcontract.tenant.name }}</td>
                    <td>{{ props.item.roomcontract.name }}</td>
                    <td>{{ props.item.roomcontract.room.name }}</td>
                    <td>{{ props.item.rentaldate | formatDate }}</td>
                    <td>{{ props.item.price | toDouble }}</td>
                    <td>{{ props.item.penalty | toDouble }}</td>
                    <td>{{ props.item.processing_fees | toDouble }}</td>
                    <td>{{ props.item.service_fees | toDouble }}</td>
                    <td v-if="props.item.paid">
                      <v-icon small color="success">mdi-check</v-icon>
                    </td>
                    <td v-else>
                      <v-icon small color="danger">mdi-close</v-icon>
                    </td>
                    <td>{{ props.item.paymentdate | formatDate }}</td>
                    <td>
                      <print-rental-payment-button
                        :item="props.item"
                        :roomcontract="props.item.roomcontract"
                        v-if="props.item.paid"
                      >
                        <v-icon small class="mr-2" color="success"
                          >mdi-printer</v-icon
                        >
                      </print-rental-payment-button>
                      <!-- <rental-print
                        @click="selectedRental = props.item"
                        class="mr-2"
                        v-if="props.item.paid"
                        :buttonStyle="rentalPrintButtonConfig.buttonStyle"
                        :room="selectedRental.roomcontract.room"
                        :roomcontract="selectedRental.roomcontract"
                        :tenant="selectedRental.roomcontract.tenant"
                        :rentalpayment="selectedRental"
                      >mdi-pencil</rental-print>-->
                      <v-icon
                        small
                        class="mr-2"
                        @click="openPaymentDialog(props.item.uid, false)"
                        color="warning"
                        v-else
                        >mdi-currency-usd</v-icon
                      >

                      <confirm-dialog
                        :activatorStyle="
                          deleteRentalButtonConfig.activatorStyle
                        "
                        @confirmed="
                          $event ? deleteRentalPayment(props.item.uid) : null
                        "
                      ></confirm-dialog>
                    </td>
                  </tr>
                </template>
              </v-data-table>
            </v-card>
          </v-col>
          <v-col
            cols="12"
            :class="helpers.managementStyles().centerWrapperClass"
          >
            <v-card raised width="100%">
              <v-data-table
                :headers="paymentHeaders"
                :items="paymentData"
                :options.sync="paymentOptions"
                :server-items-length="paymentTotal"
                :loading="paymentLoading"
              >
                <template v-slot:top>
                  <v-toolbar flat color="white">
                    <v-toolbar-title
                      :class="helpers.managementStyles().subtitleClass"
                      >Add On Payment</v-toolbar-title
                    >
                    <v-spacer></v-spacer>
                    <payment-filter-dialog
                      :buttonStyle="rentalPaymentFilterDialogConfig.buttonStyle"
                      :dialogStyle="rentalPaymentFilterDialogConfig.dialogStyle"
                      @submitFilter="initPaymentFilter($event)"
                    ></payment-filter-dialog>
                  </v-toolbar>
                </template>
                <template v-slot:item="props">
                  <tr>
                    <td>{{ props.item.sequence }}</td>
                    <td>{{ props.item.roomcontract.tenant.name }}</td>
                    <td>{{ props.item.roomcontract.name }}</td>
                    <td>{{ props.item.roomcontract.room.name }}</td>
                    <td>{{ props.item.paymentdate | formatDate }}</td>
                    <td>{{ props.item.totalpayment | toDouble }}</td>
                    <td>{{ props.item.other_charges | toDouble }}</td>
                    <td>
                      {{
                        _.compact(
                          _.map(props.item.services, function (service) {
                            return _.get(service, ["text"]) || "";
                          })
                        ) | getArrayValues
                      }}
                    </td>
                    <td>{{ props.item.remark }}</td>
                    <td>
                      <print-payment-button
                        :item="props.item"
                        :roomcontract="props.item.roomcontract"
                      >
                        <v-icon small class="mr-2" color="success"
                          >mdi-printer</v-icon
                        >
                      </print-payment-button>

                      <confirm-dialog
                        :activatorStyle="
                          deleteRentalButtonConfig.activatorStyle
                        "
                        @confirmed="
                          $event ? deletePayment(props.item.uid) : null
                        "
                      ></confirm-dialog>
                    </td>
                  </tr>
                </template>
              </v-data-table>
            </v-card>
          </v-col>
        </v-row>
        <v-dialog
          persistent
          :maxWidth="'30%'"
          :fullscreen="false"
          hideOverlay
          v-model="paymentDialog"
          transition="dialog-bottom-transition"
        >
          <rental-payment-form
            :uid="selectedPayment.uid"
            @close="paymentDialog = false"
            :editMode="this.editMode"
            @makePayment="updateRentalPaymentDetails($event)"
          ></rental-payment-form>
        </v-dialog>
      </v-container>
    </v-content>
  </v-app>
</template>
