
<script>
import moment from 'moment';
import { mapActions } from "vuex";
import { _ } from "../../../common/common-function";
import PrintPaymentButton from "../PaymentManagement/PrintPaymentButton.vue";
import RoomContractCheckOutForm from "./RoomContractCheckOutForm.vue";
export default {
  components: { RoomContractCheckOutForm, PrintPaymentButton },
  data: () => ({
    _: _,
    editMode: false,
    addOnPaymentEditMode: false,
    checkoutDialog: false,
    paymentDialog: false,
    addOnPaymentDialog: false,
    paymentPayDialog: false,
    depositPaymentDialog: false,
    selectedPayment: {
      uid: "",
    },
    selectedAddOnPayment: {
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
    rentalPaymentHeaders: [
      {
        text: "Sequence No",
        value: "sequence",
      },
      {
        text: "Reference No",
        value: "referenceno",
      },
      {
        text: "Rental",
        value: "rentaldate",
      },
      { text: "Price", value: "price" },
      { text: "Penalty", value: "penalty" },
      { text: "Processing Fees", value: "processing_fees" },
      { text: "Service Fees", value: "service_fees" },
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
    paymentHeaders: [
      {
        text: "Receipt No",
      },
      {
        text: "Reference No",
      },
      { text: "Payment Date" },
      { text: "Other Charges" },
      { text: "Other Payments" },
      { text: "Service Payment" },
      {
        text: "Services",
      },
      { text: "Remark" },
      { text: "Total Payment" },
      { text: "Actions" },
    ],
    editButtonStyle: {
      block: false,
      color: "success",
      class: "m-3",
      text: "Edit",
      icon: "mdi-pencil",
    },
    deleteButtonConfig: {
      buttonStyle: {
        block: false,
        color: "error",
        class: "m-3",
        text: "Delete",
        icon: "mdi-trash-can-outline",
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
    data: new Form({
      remark: "",
      room: [],
    }),
  }),

  computed: {
    isLoading() {
      return this.$store.getters.isLoading;
    },
    isExpired() {
      return moment().isAfter(moment(this.data.enddate));
    },
    sortedRentalPayments() {
      return this.helpers.sortByDate(this.data.rentalpayments, "rentaldate");
    },
    sortedPayments() {
      return this.helpers.sortByDate(this.data.payments, "paymentdate");
    },
  },
  watch: {
    paymentDialog: function (val) {
      if (!val) {
        this.selectedPayment = {
          uid: "",
        };
      }
    },
    addOnPaymentDialog: function (val) {
      console.log("dialog", val);
      if (!val) {
        this.selectedAddOnPayment = {
          uid: "",
        };
      }
    },
  },
  created() {
    this.$Progress.start();
    this.showLoadingAction();
    this.getRoomContractAction({ uid: this.$route.params.uid })
      .then((data) => {
        this.data = data.data;
        this.$Progress.finish();
        this.endLoadingAction();
      })
      .catch((error) => {
        Toast.fire({
          icon: "warning",
          title: "Fail to retrieve the Room Contract!!!!! ",
        });
        this.$Progress.finish();
        this.endLoadingAction();
      });
  },

  methods: {
    ...mapActions({
      getRoomContractAction: "getRoomContract",
      createRentalPaymentAction: "createRentalPayment",
      deleteRoomContractAction: "deleteRoomContract",
      deleteRentalPaymentAction: "deleteRentalPayment",
      deletePaymentAction: "deletePayment",
      showLoadingAction: "showLoadingAction",
      endLoadingAction: "endLoadingAction",
    }),
    isEmpty(data) {
      return this._.isEmpty(data);
    },
    openPaymentDialog(uid, mode) {
      this.paymentDialog = true;
      this.editMode = mode;
      this.selectedPayment.uid = uid;
    },
    openAddOnPaymentDialog(uid, mode) {
      this.addOnPaymentDialog = true;
      this.selectedAddOnPayment.uid = uid;
      this.addOnPaymentEditMode = mode;
    },
    openPaymentPayDialog(uid, mode) {
      this.paymentPayDialog = true;
      this.selectedAddOnPayment.uid = uid;
    },
    showRoom($data) {
      this.$router.push("/room/" + $data.uid);
    },
    showRoomContract($data) {
      this.$router.push("/roomcontract/" + $data.uid);
    },
    showTenant($data) {
      this.$router.push("/tenant/" + $data.uid);
    },
    showService($data) {
      this.$router.push("/service/" + $data.uid);
    },
    createRentalPayment() {
      this.showLoadingAction();
      this.$Progress.start();
      this.createRentalPaymentAction({ room_contract_id: this.data.id })
        .then((data) => {
          this.data.rentalpayments.push(data.data);
          Toast.fire({
            icon: "success",
            title: "Successful Created. ",
          });
          this.$Progress.finish();
          this.endLoadingAction();
        })
        .catch((error) => {
          Toast.fire({
            icon: "warning",
            title: "Fail to add the rental!!!!! ",
          });
          this.$Progress.finish();
          this.endLoadingAction();
        });
    },
    updateRentalPaymentDetails(data) {
      var id = data.id;
      var rentalpayment = data;
      console.log(data.addOnPayment);
      this.data.rentalpayments = this.data.rentalpayments.map(function (item) {
        if (item.id == id) {
          return rentalpayment;
        } else {
          return item;
        }
      });

      if (_.isPlainObject(data.addOnPayment) && !_.isEmpty(data.addOnPayment)) {
        this.updatePaymentDetails(data.addOnPayment);
      }
    },
    updatePaymentDetails(data) {
      console.log(data);
      var id = data.id;
      var payment = data;
      if (
        _.isArray(_.get(payment, ["services"])) &&
        !_.isEmpty(_.get(payment, ["services"]))
      ) {
        payment.services = _.map(payment.services, function (service) {
          service.pivot = {
            price: service.price,
          };
          return service;
        });
      }
      if (
        _.isArray(_.get(payment, ["otherpayments"])) &&
        !_.isEmpty(_.get(payment, ["otherpayments"]))
      ) {
        payment.otherpayments = _.map(payment.otherpayments, (otherpayment) => {
          if (otherpayment.name == "Deposit") {
            if (payment.paid) {
              console.log(parseFloat(this.data.outstanding_deposit));
              console.log(parseFloat(otherpayment.pivot.price));
              this.data.outstanding_deposit =
                (parseFloat(this.data.outstanding_deposit) || 0) -
                parseFloat(otherpayment.pivot.price);

              console.log(this.data.outstanding_deposit);
            } else {
              this.data.outstanding_deposit =
                parseFloat(this.data.outstanding_deposit) ||
                0 + parseFloat(otherpayment.price) ||
                0;
            }
          }
          otherpayment.pivot = {
            price: otherpayment.other_charges,
          };
          return otherpayment;
        });
      }

      if (_.some(this.data.payments, ["id", id])) {
        this.data.payments = _.map(this.data.payments, function (item) {
          if (item.id == id) {
            return payment;
          }
          return item;
        });
      } else {
        this.data.payments = _.concat(this.data.payments, [data]);
      }
    },
    deleteRoomContract($isConfirmed, $uid) {
      if ($isConfirmed) {
        this.$Progress.start();
        this.showLoadingAction();
        this.deleteRoomContractAction({ uid: $uid })
          .then((data) => {
            Toast.fire({
              icon: "success",
              title: "Successful Deleted. ",
            });
            this.$Progress.finish();
            this.endLoadingAction();
            this.$router.push("/roomcontracts");
          })
          .catch((error) => {
            Toast.fire({
              icon: "warning",
              title: "Fail to delete the Room Contract!!!!! ",
            });
            this.$Progress.finish();
            this.endLoadingAction();
          });
      }
    },
    deleteRentalPaymentDetails(data) {
      var id = data.id;
      this.data.rentalpayments = this.data.rentalpayments.filter(function (
        item
      ) {
        return item.id != id;
      });
    },
    deletePaymentDetails(data) {
      var id = data.id;
      if (
        _.isArray(_.get(data, ["otherpayments"])) &&
        !_.isEmpty(_.get(data, ["otherpayments"]))
      ) {
        _.forEach(data.otherpayments, (otherpayment) => {
          if (otherpayment.name == "Deposit") {
            this.data.outstanding_deposit = 0;
          }
        });
      }
      this.data.payments = this.data.payments.filter(function (item) {
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
            title: "Fail to delete the rental!!!!! ",
          });
          this.$Progress.finish();
          this.endLoadingAction();
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
    refreshPage() {
      location.reload();
    },
    checkoutRoomContract(isConfirmed, value) {
      if (isConfirmed) {
      }
    },
    print(data) {
      this.selectedRental = data;
      this.selectedRental.roomcontract = this.data;
      this.selectedRental.roomcontract.tenant = this.data.tenant;
      this.selectedRental.roomcontract.room = this.data.room;
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
    <navbar
      :returnRole="
        (role) => {
          this.role = role;
        }
      "
    ></navbar>
    <v-content
      :class="helpers.managementStyles().backgroundClass"
      v-if="helpers.isAccessible(_.get(role, ['name']), 'roomContract', 'read')"
    >
      <v-container>
        <loading></loading>
        <v-card
          class="ma-2"
          :color="helpers.managementStyles().formCardColor"
          raised
        >
          <v-card-title
            class="ma-2"
            :class="helpers.managementStyles().titleClass"
            >RoomContract - {{ data.uid }}</v-card-title
          >
          <v-container>
            <v-divider
              class="mx-3"
              :color="helpers.managementStyles().dividerColor"
              v-if="
                _.isPlainObject(data.parentroomcontract) &&
                !_.isEmpty(data.parentroomcontract)
              "
            ></v-divider>
            <v-row
              justify="start"
              align="center"
              class="pa-2"
              v-if="
                _.isPlainObject(data.parentroomcontract) &&
                !_.isEmpty(data.parentroomcontract)
              "
            >
              <v-col cols="12">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Main Room Contract</label>
                  <div class="form-control-plaintext">
                    <v-chip
                      class="ma-2"
                      @click="showRoomContract(data.parentroomcontract)"
                    >
                      <h4 class="text-center ma-2">
                        {{ data.parentroomcontract.name }}
                      </h4>
                    </v-chip>
                  </div>
                </div>
              </v-col>
            </v-row>
            <v-divider
              class="mx-3"
              :color="helpers.managementStyles().dividerColor"
            ></v-divider>
            <v-row justify="start" align="center" class="pa-2">
              <v-col cols="12">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Name</label>
                  <div class="form-control-plaintext">
                    <v-chip class="ma-2">
                      <h4 class="text-center ma-2">{{ data.name }}</h4>
                    </v-chip>
                  </div>
                </div>
              </v-col>
            </v-row>
            <v-divider
              class="mx-3"
              :color="helpers.managementStyles().dividerColor"
            ></v-divider>
            <v-row justify="start" align="center" class="pa-2">
              <v-col cols="12">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Room</label>
                  <div class="form-control-plaintext">
                    <v-chip class="ma-2" @click="showRoom(data.room)">
                      <h4 class="text-center ma-2">{{ data.room.name }}</h4>
                    </v-chip>
                  </div>
                </div>
              </v-col>
            </v-row>
            <v-divider
              class="mx-3"
              :color="helpers.managementStyles().dividerColor"
            ></v-divider>
            <v-row justify="start" align="center" class="pa-2">
              <v-col cols="12">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Contract</label>
                  <div class="form-control-plaintext">
                    <v-chip class="ma-2">
                      <h4 class="text-center ma-2">{{ data.contract.name }}</h4>
                    </v-chip>
                  </div>
                </div>
              </v-col>
            </v-row>
            <v-divider
              class="mx-3"
              :color="helpers.managementStyles().dividerColor"
            ></v-divider>
            <v-row justify="start" align="center" class="pa-2">
              <v-col cols="12">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Tenant</label>
                  <div class="form-control-plaintext">
                    <v-chip class="ma-2" @click="showTenant(data.tenant)">
                      <h4 class="text-center ma-2">{{ data.tenant.name }}</h4>
                    </v-chip>
                  </div>
                </div>
              </v-col>
            </v-row>
            <v-divider
              class="mx-3"
              :color="helpers.managementStyles().dividerColor"
              v-if="
                _.isArray(data.childrenroomcontracts) &&
                !_.isEmpty(data.childrenroomcontracts)
              "
            ></v-divider>
            <v-row
              justify="start"
              align="center"
              class="pa-2"
              v-if="
                _.isArray(data.childrenroomcontracts) &&
                !_.isEmpty(data.childrenroomcontracts)
              "
            >
              <v-col cols="12">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Sub Contracts</label>
                  <div class="form-control-plaintext">
                    <v-chip
                      class="ma-2"
                      v-for="subcontract in data.childrenroomcontracts"
                      :key="subcontract.uid"
                      @click="showRoomContract(subcontract)"
                    >
                      <h4 class="text-center ma-2">{{ subcontract.name }}</h4>
                    </v-chip>
                  </div>
                </div>
              </v-col>
            </v-row>
            <v-divider
              class="mx-3"
              :color="helpers.managementStyles().dividerColor"
            ></v-divider>
            <v-row justify="start" align="center" class="pa-2">
              <v-col cols="12" md="4">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Sequence No</label>
                  <div class="form-control-plaintext">
                    <h4>{{ data.sequence }}</h4>
                  </div>
                </div>
              </v-col>
              <v-col cols="12" md="4">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Contract Start Date</label>
                  <div class="form-control-plaintext">
                    <h4>{{ data.startdate }}</h4>
                  </div>
                </div>
              </v-col>
              <v-col cols="12" md="4">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Contract End Date</label>
                  <div class="form-control-plaintext">
                    <h4>{{ data.enddate }}</h4>
                  </div>
                </div>
              </v-col>
              <v-col cols="12" md="4" v-if="data.rental_payment_start_date">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Rental Start Date</label>
                  <div class="form-control-plaintext">
                    <h4>{{ data.rental_payment_start_date }}</h4>
                  </div>
                </div>
              </v-col>
              <v-col cols="12" md="4">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Duration</label>
                  <div class="form-control-plaintext">
                    <h4>{{ data.duration }} {{ data.rental_type }}</h4>
                  </div>
                </div>
              </v-col>
              <!-- <v-col cols="12" md="4">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Left Month</label>
                  <div class="form-control-plaintext">
                    <h4>{{ data.left }} {{ data.rental_type }}</h4>
                  </div>
                </div>
              </v-col> -->
              <v-col cols="12" md="4">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Rental</label>
                  <div class="form-control-plaintext">
                    <h4>RM {{ data.rental }}</h4>
                  </div>
                </div>
              </v-col>
              <v-col cols="12" md="4">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Deposit</label>
                  <div class="form-control-plaintext">
                    <h4>RM {{ data.deposit }}</h4>
                  </div>
                </div>
              </v-col>
              <v-col cols="12" md="4">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Agreement Fees</label>
                  <div class="form-control-plaintext">
                    <h4>RM {{ data.agreement_fees }}</h4>
                  </div>
                </div>
              </v-col>
              <v-col cols="12" md="4">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Booking fees</label>
                  <div class="form-control-plaintext">
                    <h4>RM {{ data.booking_fees }}</h4>
                  </div>
                </div>
              </v-col>
              <v-col cols="12" md="4">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Outstanding Deposit</label>
                  <div class="form-control-plaintext">
                    <h4>RM {{ data.outstanding_deposit }}</h4>
                  </div>
                </div>
              </v-col>
              <!-- <v-col cols="12">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Terms</label>
                  <div class="form-control-plaintext">
                    <h4>{{ data.terms }}</h4>
                  </div>
                </div>
              </v-col> -->
              <!-- <v-col cols="12" md="4">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Auto Renew</label>
                  <div class="form-control-plaintext">
                    <h4>{{ data.autorenew ? "Yes" : "No" }}</h4>
                  </div>
                </div>
              </v-col> -->
              <v-col cols="12" md="4">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Checked Out</label>
                  <div class="form-control-plaintext">
                    <h4>{{ data.checkedout ? "Yes" : "No" }}</h4>
                  </div>
                </div>
              </v-col>
              <v-col cols="12" md="4">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Expired</label>
                  <div class="form-control-plaintext">
                    <h4>{{ isExpired ? "Yes" : "No" }}</h4>
                  </div>
                </div>
              </v-col>
              <v-col cols="12">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Remark</label>
                  <div class="form-control-plaintext">
                    <h4>{{ data.remark }}</h4>
                  </div>
                </div>
              </v-col>
            </v-row>

            <v-divider
              class="mx-3"
              :color="helpers.managementStyles().dividerColor"
              v-if="data.checkedout"
            ></v-divider>
            <v-row
              justify="start"
              align="center"
              class="pa-2"
              v-if="data.checkedout"
            >
              <v-col cols="12" md="4">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Checkout Date</label>
                  <div class="form-control-plaintext">
                    <h4>{{ data.checkout_date }}</h4>
                  </div>
                </div>
              </v-col>
              <v-col cols="12" md="4">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Checkout Charge</label>
                  <div class="form-control-plaintext">
                    <h4>RM {{ data.checkout_charges }}</h4>
                  </div>
                </div>
              </v-col>
              <v-col cols="12" md="4">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Return Deposit</label>
                  <div class="form-control-plaintext">
                    <h4>{{ data.return_deposit }}</h4>
                  </div>
                </div>
              </v-col>
              <v-col cols="12" md="4">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Commission</label>
                  <div class="form-control-plaintext">
                    <h4>{{ data.commission }}</h4>
                  </div>
                </div>
              </v-col>
              <v-col cols="12" md="4">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Checkout Remark</label>
                  <div class="form-control-plaintext">
                    <h4>{{ data.checkout_remark }}</h4>
                  </div>
                </div>
              </v-col>
            </v-row>
            <v-divider
              class="mx-3"
              :color="helpers.managementStyles().dividerColor"
              v-if="!isEmpty(data.origservices)"
            ></v-divider>
            <v-row
              justify="start"
              align="center"
              class="pa-2"
              v-if="!isEmpty(data.origservices)"
            >
              <v-col cols="12">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Room Package Services</label>
                  <div class="form-control-plaintext">
                    <v-chip
                      class="ma-2"
                      v-for="service in data.origservices"
                      :key="service.uid"
                      @click="showService(service)"
                    >
                      <h4 class="text-center ma-2">{{ service.text }}</h4>
                    </v-chip>
                  </div>
                </div>
              </v-col>
            </v-row>
            <v-divider
              class="mx-3"
              :color="helpers.managementStyles().dividerColor"
              v-if="!isEmpty(data.addonservices)"
            ></v-divider>
            <v-row
              justify="start"
              align="center"
              class="pa-2"
              v-if="!isEmpty(data.addonservices)"
            >
              <v-col cols="12">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Add On Room Services</label>
                  <div class="form-control-plaintext">
                    <v-chip
                      class="ma-2"
                      v-for="service in data.addonservices"
                      :key="service.uid"
                      @click="showService(service)"
                    >
                      <h4 class="text-center ma-2">{{ service.text }}</h4>
                    </v-chip>
                  </div>
                </div>
              </v-col>
            </v-row>
            <v-divider
              class="mx-3"
              :color="helpers.managementStyles().dividerColor"
            ></v-divider>
            <v-row>
              <v-col
                cols="12"
                :class="helpers.managementStyles().centerWrapperClass"
              >
                <v-card raised width="100%">
                  <v-data-table
                    :headers="rentalPaymentHeaders"
                    :items="sortedRentalPayments"
                    fixed-header
                    height="300px"
                    :items-per-page="5"
                    disable-sort
                  >
                    <template v-slot:top>
                      <v-toolbar flat color="white">
                        <v-toolbar-title
                          :class="helpers.managementStyles().subtitleClass"
                          >Rental Payment</v-toolbar-title
                        >
                        <v-spacer></v-spacer>
                        <v-btn
                          v-if="!data.checkedout"
                          color="primary"
                          dark
                          class="mb-2"
                          @click="createRentalPayment()"
                          >New Payment</v-btn
                        >
                      </v-toolbar>
                    </template>
                    <template v-slot:item="props">
                      <tr>
                        <td class="text-truncate">{{ props.item.sequence }}</td>
                        <td class="text-truncate">
                          {{ props.item.referenceno }}
                        </td>
                        <td class="text-truncate">
                          {{ props.item.rentaldate | formatDate }}
                        </td>
                        <td class="text-truncate">
                          {{ props.item.price | toDouble }}
                        </td>
                        <td class="text-truncate">
                          {{ props.item.penalty | toDouble }}
                        </td>
                        <td class="text-truncate">
                          {{ props.item.processing_fees | toDouble }}
                        </td>
                        <td class="text-truncate">
                          {{ props.item.service_fees | toDouble }}
                        </td>
                        <td class="text-truncate" v-if="props.item.paid">
                          <v-icon small color="success">mdi-check</v-icon>
                        </td>
                        <td class="text-truncate" v-else>
                          <v-icon small color="danger">mdi-close</v-icon>
                        </td>
                        <td class="text-truncate">
                          {{ props.item.paymentdate | formatDate }}
                        </td>
                        <td class="text-truncate">
                          <print-rental-payment-button
                            :item="props.item"
                            :roomcontract="data"
                            v-if="props.item.paid"
                          >
                            <v-icon small class="mr-2" color="success"
                              >mdi-printer</v-icon
                            >
                          </print-rental-payment-button>

                          <!-- <v-icon
                            small
                            class="mr-2"
                            @click="openPaymentDialog(props.item.uid, true)"
                            v-if="props.item.paid"
                            color="success"
                          >mdi-pencil</v-icon>-->
                          <v-icon
                            small
                            class="mr-2"
                            @click="openPaymentDialog(props.item.uid, false)"
                            color="warning"
                            v-else-if="
                              helpers.isAccessible(
                                _.get(role, ['name']),
                                'rentalPayment',
                                'makePayment'
                              )
                            "
                            >mdi-currency-usd</v-icon
                          >
                          <v-icon
                            small
                            class="mr-2"
                            @click="openPaymentDialog(props.item.uid, true)"
                            color="success"
                            v-if="
                              props.item.paid == true &&
                              helpers.isAccessible(
                                _.get(role, ['name']),
                                'rentalPayment',
                                'edit'
                              )
                            "
                            >mdi-pencil</v-icon
                          >

                          <confirm-dialog
                            :activatorStyle="
                              deleteRentalButtonConfig.activatorStyle
                            "
                            v-if="
                              helpers.isAccessible(
                                _.get(role, ['name']),
                                'rentalPayment',
                                'delete'
                              )
                            "
                            @confirmed="
                              $event
                                ? deleteRentalPayment(props.item.uid)
                                : null
                            "
                          ></confirm-dialog>
                        </td>
                      </tr>
                    </template>
                  </v-data-table>
                </v-card>
              </v-col>
            </v-row>
            <v-divider
              class="mx-3"
              :color="helpers.managementStyles().dividerColor"
            ></v-divider>
            <v-row>
              <v-col
                cols="12"
                :class="helpers.managementStyles().centerWrapperClass"
              >
                <v-card raised width="100%">
                  <v-data-table
                    :headers="paymentHeaders"
                    :items="sortedPayments"
                    fixed-header
                    height="300px"
                    :items-per-page="5"
                    disable-sort
                  >
                    <template v-slot:top>
                      <v-toolbar flat color="white">
                        <v-toolbar-title
                          :class="helpers.managementStyles().subtitleClass"
                          >Pending Payment</v-toolbar-title
                        >
                        <v-spacer></v-spacer>
                        <!-- <v-btn
                          v-if="data.outstanding_deposit > 0"
                          color="success"
                          dark
                          class="mb-2 mr-2"
                          @click="depositPaymentDialog = true"
                          >Deposit Payment</v-btn
                        > -->
                        <v-btn
                          v-if="!data.checkedout"
                          color="success"
                          dark
                          class="mb-2 mr-2"
                          @click="openAddOnPaymentDialog(null, false)"
                          >New Pending Payment</v-btn
                        >
                      </v-toolbar>
                    </template>
                    <template v-slot:item="props">
                      <tr>
                        <td class="text-truncate">
                          {{ props.item.receiptno }}
                        </td>
                        <td class="text-truncate">
                          {{ props.item.referenceno }}
                        </td>
                        <td class="text-truncate">
                          {{ props.item.paymentdate | formatDate }}
                        </td>
                        <td class="text-truncate">
                          {{ props.item.other_charges | toDouble }}
                        </td>
                        <td class="text-truncate">
                          {{
                            _.compact(
                              _.map(
                                props.item.otherpayments,
                                function (otherpayment) {
                                  return _.get(otherpayment, ["name"]) || "";
                                }
                              )
                            ) | getArrayValues
                          }}
                        </td>
                        <td class="text-truncate">
                          {{ props.item.price | toDouble }}
                        </td>
                        <td class="text-truncate">
                          {{
                            _.compact(
                              _.map(props.item.services, function (service) {
                                return _.get(service, ["text"]) || "";
                              })
                            ) | getArrayValues
                          }}
                        </td>
                        <td class="text-truncate">{{ props.item.remark }}</td>
                        <td class="text-truncate">
                          {{ props.item.totalpayment | toDouble }}
                        </td>
                        <td class="text-truncate">
                          <print-payment-button
                            :item="props.item"
                            :roomcontract="data"
                            v-if="props.item.paid"
                          >
                            <v-icon small class="mr-2" color="success"
                              >mdi-printer</v-icon
                            >
                          </print-payment-button>

                          <!-- <v-icon
                            small
                            class="mr-2"
                            @click="openPaymentDialog(props.item.uid, true)"
                            v-if="props.item.paid"
                            color="success"
                          >mdi-pencil</v-icon>-->
                          <v-icon
                            small
                            class="mr-2"
                            @click="openPaymentPayDialog(props.item.uid)"
                            color="warning"
                            v-else-if="
                              helpers.isAccessible(
                                _.get(role, ['name']),
                                'rentalPayment',
                                'makePayment'
                              )
                            "
                            >mdi-currency-usd</v-icon
                          >
                          <v-icon
                            small
                            class="mr-2"
                            @click="
                              openAddOnPaymentDialog(props.item.uid, true)
                            "
                            color="success"
                            v-if="
                              props.item.paid == true &&
                              helpers.isAccessible(
                                _.get(role, ['name']),
                                'rentalPayment',
                                'edit'
                              )
                            "
                            >mdi-pencil</v-icon
                          >

                          <confirm-dialog
                            :activatorStyle="
                              deleteRentalButtonConfig.activatorStyle
                            "
                            @confirmed="
                              $event ? deletePayment(props.item.uid) : null
                            "
                            v-if="
                              helpers.isAccessible(
                                _.get(role, ['name']),
                                'rentalPayment',
                                'delete'
                              )
                            "
                          ></confirm-dialog>
                        </td>
                      </tr>
                    </template>
                  </v-data-table>
                </v-card>
              </v-col>
            </v-row>

            <v-divider
              class="mx-3"
              :color="helpers.managementStyles().dividerColor"
            ></v-divider>
            <v-row class="pa-2" justify="end" align="center">
              <v-col
                cols="auto"
                v-if="
                  !data.checkedout &&
                  helpers.isAccessible(
                    _.get(role, ['name']),
                    'roomContract',
                    'edit'
                  )
                "
              >
                <v-btn
                  class="m-2"
                  color="primary"
                  :disabled="isLoading"
                  @click="checkoutDialog = true"
                >
                  <v-icon left>mdi-exit-run</v-icon>
                  Checkout
                </v-btn>
              </v-col>
              <v-col
                cols="auto"
                v-if="
                  !data.checkedout &&
                  helpers.isAccessible(
                    _.get(role, ['name']),
                    'roomContract',
                    'edit'
                  )
                "
              >
                <room-contract-form
                  :editMode="true"
                  :buttonStyle="editButtonStyle"
                  :uid="this.$route.params.uid"
                  @updated="refreshPage()"
                ></room-contract-form>
              </v-col>
              <v-col
                cols="auto"
                v-if="
                  helpers.isAccessible(
                    _.get(role, ['name']),
                    'roomContract',
                    'delete'
                  )
                "
              >
                <confirm-dialog
                  :activatorStyle="deleteButtonConfig.buttonStyle"
                  @confirmed="deleteRoomContract($event, data.uid)"
                ></confirm-dialog>
              </v-col>
            </v-row>
          </v-container>
        </v-card>

        <v-dialog
          :maxWidth="'50%'"
          :fullscreen="false"
          hideOverlay
          v-model="depositPaymentDialog"
          transition="dialog-bottom-transition"
        >
          <deposit-payment-form
            @close="depositPaymentDialog = false"
            :roomcontract="depositPaymentDialog ? this.data || {} : null"
            :editMode="this.editMode"
            @createPayment="
              ($event) => {
                this.data.outstanding_deposit = 0;
                updatePaymentDetails($event);
              }
            "
          ></deposit-payment-form>
        </v-dialog>

        <v-dialog
          persistent
          :maxWidth="'50%'"
          :fullscreen="false"
          hideOverlay
          v-model="paymentPayDialog"
          transition="dialog-bottom-transition"
        >
          <payment-pay-form
            :uid="selectedAddOnPayment.uid"
            :roomcontractid="this.data.id ? `${this.data.id || ''}` : ''"
            @close="paymentPayDialog = false"
            @makePayment="updatePaymentDetails($event)"
          ></payment-pay-form>
        </v-dialog>

        <v-dialog
          persistent
          :maxWidth="'50%'"
          :fullscreen="false"
          hideOverlay
          v-model="addOnPaymentDialog"
          transition="dialog-bottom-transition"
        >
          <payment-form
            :uid="selectedAddOnPayment.uid || ''"
            :resetIndicator="addOnPaymentDialog"
            :roomcontractid="this.data.id ? `${this.data.id || ''}` : ''"
            @close="addOnPaymentDialog = false"
            :editMode="this.addOnPaymentEditMode"
            @createPayment="updatePaymentDetails($event)"
            @updated="updatePaymentDetails($event)"
          ></payment-form>
        </v-dialog>

        <v-dialog
          persistent
          :maxWidth="'30%'"
          :fullscreen="false"
          hideOverlay
          v-model="checkoutDialog"
          transition="dialog-bottom-transition"
        >
          <room-contract-check-out-form
            :uid="data.uid"
            @close="checkoutDialog = false"
          ></room-contract-check-out-form>
        </v-dialog>

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
            @updated="updateRentalPaymentDetails($event)"
          ></rental-payment-form>
        </v-dialog>

        <v-dialog
          persistent
          :maxWidth="'30%'"
          :fullscreen="false"
          hideOverlay
          v-model="checkoutDialog"
          transition="dialog-bottom-transition"
        >
          <room-contract-check-out-form
            :uid="data.uid"
            @close="checkoutDialog = false"
          ></room-contract-check-out-form>
        </v-dialog>
      </v-container>
    </v-content>
  </v-app>
</template>
