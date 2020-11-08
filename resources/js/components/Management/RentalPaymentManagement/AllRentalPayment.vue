
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
      totalDataLength: 0,
      data: [],
      loading: true,
      options: {},
      rentalPaymentFilterGroup: new Form({
        rooms: [],
        selectedRooms: [],
        keyword: null,
        fromdate: null,
        todate: null,
        paid: "all",
      }),
      rentalPaymentFilterDialogConfig: {
        buttonStyle: {
          block: true,
          class: "ma-2",
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
          text: "Paid",
        },
        {
          text: "Payment Date",
        },
        {
          text: "Action",
        },
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
  },
  methods: {
    ...mapActions({
      getRentalPaymentsAction: "getRentalPayments",
      filterRentalPaymentsAction: "filterRentalPayments",
      deleteRentalPaymentAction: "deleteRentalPayment",
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
      if (filterGroup) {
        this.rentalPaymentFilterGroup.keyword = filterGroup.keyword;
        this.rentalPaymentFilterGroup.paid = filterGroup.paid;

      }
      this.getRentalPayments();
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
          this.totalDataLength = data.totalResult;
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
              <v-card-subtitle v-show="!keywordEmpty">
                Keyword :
                <v-chip class="mx-2">{{ rentalPaymentFilterGroup.keyword }}</v-chip>
              </v-card-subtitle>
              <v-card-subtitle v-show="!fromdateEmpty">
                From Date :
                <v-chip class="mx-2">{{ rentalPaymentFilterGroup.keyword }}</v-chip>
              </v-card-subtitle>
              <v-card-subtitle v-show="!todateEmpty">
                To Date :
                <v-chip class="mx-2">{{ rentalPaymentFilterGroup.todate }}</v-chip>
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
                :server-items-length="totalDataLength"
                :loading="loading"
              >
                <template v-slot:top>
                  <v-toolbar flat class="mb-5">
                    <rental-payment-filter-dialog
                      :buttonStyle="rentalPaymentFilterDialogConfig.buttonStyle"
                      :dialogStyle="rentalPaymentFilterDialogConfig.dialogStyle"
                      @submitFilter="initRentalPaymentFilter($event)"
                    ></rental-payment-filter-dialog>
                  </v-toolbar>
                </template>
                <template v-slot:item="props">
                  <tr>
                    <td>{{props.item.roomcontract.tenant.name}}</td>
                    <td>{{props.item.roomcontract.name}}</td>
                    <td>{{props.item.roomcontract.room.name}}</td>
                    <td>{{props.item.rentaldate | formatDate}}</td>
                    <td>{{props.item.price | toDouble}}</td>
                    <td>{{props.item.penalty | toDouble}}</td>
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
                        @click="print(props.item)"
                        v-if="props.item.paid"
                        color="success"
                      >mdi-printer</v-icon>
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
                      >mdi-currency-usd</v-icon>

                      <confirm-dialog
                        :activatorStyle="deleteRentalButtonConfig.activatorStyle"
                        @confirmed="$event ? deleteRentalPayment(props.item.uid) : null"
                      ></confirm-dialog>
                    </td>
                  </tr>
                </template>
              </v-data-table>
            </v-card>
          </v-col>
        </v-row>
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
                <div :class="helpers.managementStyles().lightSubtitleClass">{{selectedRental.uid}}</div>
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
                <div
                  :class="helpers.managementStyles().lightSubtitleClass"
                >{{selectedRental.roomcontract.tenant.name}}</div>
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
                <div
                  :class="helpers.managementStyles().lightSubtitleClass"
                >{{selectedRental.roomcontract.room.name}}</div>
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
                <div
                  :class="helpers.managementStyles().lightSubtitleClass"
                >{{selectedRental.roomcontract.name}}</div>
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
                >{{selectedRental.roomcontract.startdate | formatDate}}</div>
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
                >{{ selectedRental.rentaldate | formatDate }}</div>
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
                >{{ selectedRental.paymentdate | formatDate }}</div>
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
                >RM {{ selectedRental.price | toDouble}}</div>
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
                >RM {{ selectedRental.penalty | toDouble }}</div>
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
                >RM {{ parseFloat(selectedRental.penalty) + parseFloat(selectedRental.price) | toDouble }}</div>
              </v-col>
            </v-row>
          </v-container>
        </div>
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
