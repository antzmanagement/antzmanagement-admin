
<script>
import { mapActions } from "vuex";
export default {
  data: () => ({
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
    rentalPaymentHeaders: [
      {
        text: "Rental",
        value: "rentaldate",
      },
      { text: "Price", value: "price" },
      { text: "Penalty", value: "penalty" },
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
    sortedRentalPayments() {
      return this.helpers.sortByDate(this.data.rentalpayments, "rentaldate");
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
      showLoadingAction: "showLoadingAction",
      endLoadingAction: "endLoadingAction",
    }),
    isEmpty(data) {
      return this._.isEmpty(data);
    },
    openPaymentDialog(uid, mode) {
      this.paymentDialog = true;
      this.editMode = mode;
      console.log("mode");
      console.log(this.editMode);
      this.selectedPayment.uid = uid;
    },

    showRoom($data) {
      this.$router.push("/room/" + $data.uid);
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
      this.data.rentalpayments = this.data.rentalpayments.map(function (item) {
        if (item.id == id) {
          return rentalpayment;
        } else {
          return item;
        }
      });
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
    refreshPage() {
      location.reload();
    },
    print(data) {
      this.selectedRental = data;
      this.selectedRental.roomcontract = this.data;
      this.selectedRental.roomcontract.tenant = this.data.tenant;
      this.selectedRental.roomcontract.room = this.data.room;
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
          <v-divider
            class="mx-3"
            :color="helpers.managementStyles().dividerColor"
          ></v-divider>
          <v-container>
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
            ></v-divider>
            <v-row justify="start" align="center" class="pa-2">
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
                  <label class="form-label mb-0">Duration</label>
                  <div class="form-control-plaintext">
                    <h4>{{ data.duration }} Month</h4>
                  </div>
                </div>
              </v-col>
              <v-col cols="12" md="4">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Left Month</label>
                  <div class="form-control-plaintext">
                    <h4>{{ data.leftmonth }} month</h4>
                  </div>
                </div>
              </v-col>
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
                  <label class="form-label mb-0">Outstanding Deposit</label>
                  <div class="form-control-plaintext">
                    <h4>RM {{ data.outstanding_deposit }}</h4>
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
              <v-col cols="12">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Terms</label>
                  <div class="form-control-plaintext">
                    <h4>{{ data.terms }}</h4>
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
                  >
                    <template v-slot:top>
                      <v-toolbar flat color="white">
                        <v-toolbar-title
                          :class="helpers.managementStyles().subtitleClass"
                          >Rental Payment</v-toolbar-title
                        >
                        <v-spacer></v-spacer>
                        <v-btn
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
                        <td>{{ props.item.rentaldate | formatDate }}</td>
                        <td>{{ props.item.price | toDouble }}</td>
                        <td>{{ props.item.penalty | toDouble }}</td>
                        <td v-if="props.item.paid">
                          <v-icon small color="success">mdi-check</v-icon>
                        </td>
                        <td v-else>
                          <v-icon small color="danger">mdi-close</v-icon>
                        </td>
                        <td>{{ props.item.paymentdate | formatDate }}</td>
                        <td>
                          <v-icon
                            small
                            class="mr-2"
                            @click="print(props.item)"
                            v-if="props.item.paid"
                            color="success"
                            >mdi-printer</v-icon
                          >
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
                            v-else
                            >mdi-currency-usd</v-icon
                          >

                          <confirm-dialog
                            :activatorStyle="
                              deleteRentalButtonConfig.activatorStyle
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
            <v-row class="pa-2" justify="end" align="center">
              <v-col cols="auto">
                <room-contract-form
                  :editMode="true"
                  :buttonStyle="editButtonStyle"
                  :uid="this.$route.params.uid"
                  @updated="refreshPage()"
                ></room-contract-form>
              </v-col>
              <v-col cols="auto">
                <confirm-dialog
                  :activatorStyle="deleteButtonConfig.buttonStyle"
                  @confirmed="deleteRoomContract($event, data.uid)"
                ></confirm-dialog>
              </v-col>
            </v-row>
          </v-container>
        </v-card>

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

        <div class="d-none" id="printMe">
          <v-container>
            <v-row>
              <v-col
                cols="12"
                :class="helpers.managementStyles().centerWrapperClass"
              >
                <div class="h5 my-5 font-weight-bold">Payment Receipt</div>
              </v-col>
            </v-row>
            <v-row>
              <v-col
                cols="3"
                :class="helpers.managementStyles().centerWrapperClass"
              >
                <div :class="helpers.managementStyles().subtitleClass">
                  Payment Id
                </div>
              </v-col>
              <v-col
                cols="1"
                :class="helpers.managementStyles().centerWrapperClass"
              >
                <div :class="helpers.managementStyles().subtitleClass">:</div>
              </v-col>
              <v-col cols="8">
                <div :class="helpers.managementStyles().lightSubtitleClass">
                  {{ selectedRental.uid }}
                </div>
              </v-col>
            </v-row>
            <v-row>
              <v-col
                cols="3"
                :class="helpers.managementStyles().centerWrapperClass"
              >
                <div :class="helpers.managementStyles().subtitleClass">
                  Tenant
                </div>
              </v-col>
              <v-col
                cols="1"
                :class="helpers.managementStyles().centerWrapperClass"
              >
                <div :class="helpers.managementStyles().subtitleClass">:</div>
              </v-col>
              <v-col cols="8">
                <div :class="helpers.managementStyles().lightSubtitleClass">
                  {{ selectedRental.roomcontract.tenant.name }}
                </div>
              </v-col>
            </v-row>
            <v-row>
              <v-col
                cols="3"
                :class="helpers.managementStyles().centerWrapperClass"
              >
                <div :class="helpers.managementStyles().subtitleClass">
                  Room
                </div>
              </v-col>
              <v-col
                cols="1"
                :class="helpers.managementStyles().centerWrapperClass"
              >
                <div :class="helpers.managementStyles().subtitleClass">:</div>
              </v-col>
              <v-col cols="8">
                <div :class="helpers.managementStyles().lightSubtitleClass">
                  {{ selectedRental.roomcontract.room.name }}
                </div>
              </v-col>
            </v-row>
            <v-row>
              <v-col
                cols="3"
                :class="helpers.managementStyles().centerWrapperClass"
              >
                <div :class="helpers.managementStyles().subtitleClass">
                  Contract
                </div>
              </v-col>
              <v-col
                cols="1"
                :class="helpers.managementStyles().centerWrapperClass"
              >
                <div :class="helpers.managementStyles().subtitleClass">:</div>
              </v-col>
              <v-col cols="8">
                <div :class="helpers.managementStyles().lightSubtitleClass">
                  {{ selectedRental.roomcontract.name }}
                </div>
              </v-col>
            </v-row>
            <v-row>
              <v-col
                cols="3"
                :class="helpers.managementStyles().centerWrapperClass"
              >
                <div :class="helpers.managementStyles().subtitleClass">
                  Contract Start Date
                </div>
              </v-col>
              <v-col
                cols="1"
                :class="helpers.managementStyles().centerWrapperClass"
              >
                <div :class="helpers.managementStyles().subtitleClass">:</div>
              </v-col>
              <v-col cols="8">
                <div :class="helpers.managementStyles().lightSubtitleClass">
                  {{ selectedRental.roomcontract.startdate | formatDate }}
                </div>
              </v-col>
            </v-row>
            <v-row>
              <v-col
                cols="3"
                :class="helpers.managementStyles().centerWrapperClass"
              >
                <div :class="helpers.managementStyles().subtitleClass">
                  Rental
                </div>
              </v-col>
              <v-col
                cols="1"
                :class="helpers.managementStyles().centerWrapperClass"
              >
                <div :class="helpers.managementStyles().subtitleClass">:</div>
              </v-col>
              <v-col cols="8">
                <div :class="helpers.managementStyles().lightSubtitleClass">
                  {{ selectedRental.rentaldate | formatDate }}
                </div>
              </v-col>
            </v-row>
            <v-row>
              <v-col
                cols="3"
                :class="helpers.managementStyles().centerWrapperClass"
              >
                <div :class="helpers.managementStyles().subtitleClass">
                  Payment Date
                </div>
              </v-col>
              <v-col
                cols="1"
                :class="helpers.managementStyles().centerWrapperClass"
              >
                <div :class="helpers.managementStyles().subtitleClass">:</div>
              </v-col>
              <v-col cols="8">
                <div :class="helpers.managementStyles().lightSubtitleClass">
                  {{ selectedRental.paymentdate | formatDate }}
                </div>
              </v-col>
            </v-row>
            <v-row>
              <v-col
                cols="3"
                :class="helpers.managementStyles().centerWrapperClass"
              >
                <div :class="helpers.managementStyles().subtitleClass">
                  Price
                </div>
              </v-col>
              <v-col
                cols="1"
                :class="helpers.managementStyles().centerWrapperClass"
              >
                <div :class="helpers.managementStyles().subtitleClass">:</div>
              </v-col>
              <v-col cols="8">
                <div :class="helpers.managementStyles().lightSubtitleClass">
                  RM {{ selectedRental.price | toDouble }}
                </div>
              </v-col>
            </v-row>
            <v-row>
              <v-col
                cols="3"
                :class="helpers.managementStyles().centerWrapperClass"
              >
                <div :class="helpers.managementStyles().subtitleClass">
                  Penalty
                </div>
              </v-col>
              <v-col
                cols="1"
                :class="helpers.managementStyles().centerWrapperClass"
              >
                <div :class="helpers.managementStyles().subtitleClass">:</div>
              </v-col>
              <v-col cols="8">
                <div :class="helpers.managementStyles().lightSubtitleClass">
                  RM {{ selectedRental.penalty | toDouble }}
                </div>
              </v-col>
            </v-row>
            <v-row>
              <v-col
                cols="3"
                :class="helpers.managementStyles().centerWrapperClass"
              >
                <div :class="helpers.managementStyles().subtitleClass">
                  Total Paid
                </div>
              </v-col>
              <v-col
                cols="1"
                :class="helpers.managementStyles().centerWrapperClass"
              >
                <div :class="helpers.managementStyles().subtitleClass">:</div>
              </v-col>
              <v-col cols="8">
                <div :class="helpers.managementStyles().lightSubtitleClass">
                  RM
                  {{
                    (parseFloat(selectedRental.penalty) +
                      parseFloat(selectedRental.price))
                      | toDouble
                  }}
                </div>
              </v-col>
            </v-row>
          </v-container>
        </div>
      </v-container>
    </v-content>
  </v-app>
</template>
