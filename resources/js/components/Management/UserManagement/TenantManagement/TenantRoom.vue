

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
    rentalpayments: {
      type: Array,
      default: () => [],
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
    shuffleButtonConfig: {
      activatorStyle: {
        icon: "mdi-shuffle-variant",
        isIcon: true,
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
    showRoom($data) {
      this.$router.push("/room/" + $data.uid);
    },
    transferRoomContract() {
      this.showLoadingAction();
      this.$Progress.start();
      this.transferRoomContractAction({
        room_contract_id: this.roomcontract.id,
        tenant_id: this.selectedTenant,
      })
        .then((data) => {
          Toast.fire({
            icon: "success",
            title: "Successful Transfer. ",
          });
          this.$Progress.finish();
          this.endLoadingAction();
          window.location.reload();
        })
        .catch((error) => {
          Toast.fire({
            icon: "warning",
            title: "Fail to transfer the contract!!!!! ",
          });
          this.$Progress.finish();
          this.endLoadingAction();
        });
    },
    createRentalPayment() {
      this.showLoadingAction();
      this.$Progress.start();
      this.createRentalPaymentAction({ room_contract_id: this.roomcontract.id })
        .then((data) => {
          this.rentalpayments.push(data.data);
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
    openPaymentDialog(uid, payonly) {
      this.paymentDialog = true;
      this.selectedPayment.uid = uid;
      this.payonly = payonly;
    },

    updateRentalPaymentDetails(data) {
      var id = data.id;
      var rentalpayment = data;
      this.rentalpayments = this.rentalpayments.map(function (item) {
        if (item.id == id) {
          return rentalpayment;
        } else {
          return item;
        }
      });
    },
    deleteRentalPaymentDetails(data) {
      var id = data.id;
      this.rentalpayments = this.rentalpayments.filter(function (item) {
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
  },
};
</script>

<template>
  <v-card>
    <v-container class="pa-5">
      <loading></loading>
      <v-row>
        <v-col
          cols="auto"
          :class="helpers.managementStyles().centerWrapperClass"
        >
          <div :class="helpers.managementStyles().subtitleClass">Tenant :</div>
        </v-col>
        <v-col
          cols="auto"
          :class="helpers.managementStyles().centerWrapperClass"
        >
          <div :class="helpers.managementStyles().lightSubtitleClass">
            {{ tenant.name }}
          </div>
        </v-col>
        <v-col
          cols="auto"
          :class="helpers.managementStyles().centerWrapperClass"
        >
          <confirm-dialog
            :activatorStyle="shuffleButtonConfig.activatorStyle"
            @confirmed="$event ? transferRoomContract() : null"
          >
            <template v-slot:header>
              <div :class="helpers.managementStyles().subtitleClass">
                Transfer Contract
              </div>
            </template>

            <template v-slot:body>
              <v-autocomplete
                v-model="selectedTenant"
                item-text="name"
                item-value="id"
                :items="tenants || []"
                label="Tenant"
              ></v-autocomplete>
            </template>
          </confirm-dialog>
        </v-col>
      </v-row>
      <v-row>
        <v-col
          cols="auto"
          :class="helpers.managementStyles().centerWrapperClass"
        >
          <div :class="helpers.managementStyles().subtitleClass">Room :</div>
        </v-col>
        <v-col
          cols="auto"
          :class="helpers.managementStyles().centerWrapperClass"
        >
          <v-chip class="mr-2 my-2" @click="showRoom(room)">
            <div :class="helpers.managementStyles().lightSubtitleClass">
              {{ room.name }}
            </div>
          </v-chip>
        </v-col>
      </v-row>
      <v-row>
        <v-col
          cols="auto"
          :class="helpers.managementStyles().centerWrapperClass"
        >
          <div :class="helpers.managementStyles().subtitleClass">
            Contract :
          </div>
        </v-col>
        <v-col
          cols="auto"
          :class="helpers.managementStyles().centerWrapperClass"
        >
          <div :class="helpers.managementStyles().lightSubtitleClass">
            {{ roomcontract.name }}
          </div>
        </v-col>
      </v-row>
      <v-row>
        <v-col
          cols="auto"
          :class="helpers.managementStyles().centerWrapperClass"
        >
          <div :class="helpers.managementStyles().subtitleClass">
            Contract Start Date :
          </div>
        </v-col>
        <v-col
          cols="auto"
          :class="helpers.managementStyles().centerWrapperClass"
        >
          <div :class="helpers.managementStyles().lightSubtitleClass">
            {{ roomcontract.startdate | formatDate }}
          </div>
        </v-col>
      </v-row>

      <v-row>
        <v-col col="12">
          <v-divider
            class="mx-3"
            :color="helpers.managementStyles().dividerColor"
          ></v-divider>
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
                      @click="openPaymentDialog(props.item.uid, false)"
                      v-if="props.item.paid"
                      color="success"
                      >mdi-pencil</v-icon
                    >
                    <v-icon
                      small
                      class="mr-2"
                      @click="openPaymentDialog(props.item.uid, true)"
                      color="warning"
                      v-else
                      >mdi-currency-usd</v-icon
                    >

                    <confirm-dialog
                      :activatorStyle="deleteButtonConfig.activatorStyle"
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