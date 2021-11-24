
<script>
import moment from "moment";
import { mapActions } from "vuex";
import { insertBetween, notEmptyLength, _ } from "../../common/common-function";

export default {
  data() {
    return {
      _: _,
      moment: moment,
      editMode: false,
      paymentDialog: false,
      unpaidrentals: [],
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
      todayPaidRentals : [],
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
      rentalPaymentHeaders: [
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
      roomContractHeaders: [
        {
          text: "Sequence No",
        },
        {
          text: "Contract Type",
        },
        {
          text: "Room",
        },
        {
          text: "Owner",
        },
        {
          text: "Tenant",
        },
        {
          text: "Start date",
        },
        {
          text: "End date",
        },
        {
          text: "Deposit",
        },
        {
          text: "Outstanding Deposit",
        },
        {
          text: "Rental",
        },
        {
          text: "Duration",
        },
        {
          text: "Services",
        },
        {
          text: "Checked out",
        },
        {
          text: "Check Out Date",
        },
      ],
      donutOptions: {
        plotOptions: {
          pie: {
            customScale: 1,
            offsetX: 0,
            offsetY: 0,
            expandOnClick: true,
            dataLabels: {
              offset: 0,
              minAngleToShowLabel: 10,
            },

            donut: {
              size: "40%",
              background: "transparent",
              labels: {
                show: true,
                total: {
                  show: true,
                  showAlways: true,
                  label: "Total",
                  fontSize: "22px",
                  fontFamily: "Helvetica, Arial, sans-serif",
                  fontWeight: 600,
                  color: "#373d3f",
                  formatter: function (w) {
                    return w.globals.seriesTotals.reduce((a, b) => {
                      return a + b;
                    }, 0);
                  },
                },
              },
            },
          },
        },
        theme: {
          palette: "palette1",
        },
      },
      roomTypePortionOptions: {},
      roomTypesPortionValues: [],
      roomStatusPortionOptions: {},
      roomStatusPortionValues: [],
      currentMonthUnpaidTenantPortionOptions: {},
      currentMonthUnpaidTenantPortionValues: [],
      monthlyMaintenancesPortion: {},
      monthlyMaintenancesPortionValues: [],
      roomContractAlmostEnd: [],
      search: "",
      headers: [
        {
          text: "Rental Date",
          align: "start",
          value: "rentaldate",
        },
        { text: "Room Contract", value: "contract" },
        { text: "Tenant", value: "tenant" },
        { text: "Rental", value: "price" },
        { text: "Action", value: "action" },
      ],
      rentalPaymentExcelFields: {
        id: "id",
        uid: "uid",
        referenceno: "referenceno",
        sequence: "sequence",
        room: {
          field: "roomcontract",
          callback: (value) => {
            return _.get(value, `room.name`) || "N/A";
          },
        },
        tenant: {
          field: "roomcontract",
          callback: (value) => {
            return _.get(value, `tenant.name`) || "N/A";
          },
        },
        room_contract_start_date: {
          field: "roomcontract",
          callback: (value) => {
            return _.get(value, `startdate`) || "N/A";
          },
        },
        room_contract_end_date: {
          field: "roomcontract",
          callback: (value) => {
            return _.get(value, `enddate`) || "N/A";
          },
        },
        room_contract_id: {
          field: "roomcontract",
          callback: (value) => {
            return _.get(value, `id`) || "N/A";
          },
        },
        totalpayment: "price",
        penalty: "penalty",
        processing_fees: "processing_fees",
        service_fees: "service_fees",
        outstanding_payment: "outstanding",
        paymentdate: "paymentdate",
        rentaldate: "rentaldate",
        paid: {
          field: "paid",
          callback: (value) => (value ? "Yes" : "No"),
        },
        remark: "remark",
        created_at: "created_at",
        updated_at: "updated_at",
      },
      roomContractExcelFields: {
        id: "id",
        sequence: "sequence",
        room: {
          field: "room",
          callback: (value) => {
            return _.get(value, `name`) || "N/A";
          },
        },
        room_id: {
          field: "room",
          callback: (value) => {
            return _.get(value, `id`) || "N/A";
          },
        },
        contract: {
          field: "contract",
          callback: (value) => {
            return _.get(value, `name`) || "N/A";
          },
        },
        contract_id: {
          field: "contract",
          callback: (value) => {
            return _.get(value, `id`) || "N/A";
          },
        },
        tenant: {
          field: "tenant",
          callback: (value) => {
            return _.get(value, `name`) || "N/A";
          },
        },
        tenant_id: {
          field: "tenant",
          callback: (value) => {
            return _.get(value, `id`) || "N/A";
          },
        },
        startdate: "startdate",
        enddate: "enddate",
        "duration(months)": "duration",
        "left(months)": "left",
        rental: "rental",
        booking_fees: "booking_fees",
        deposit: "deposit",
        agreement_fees: "agreement_fees",
        outstanding: "outstanding",
        checkedout: {
          field: "sublet",
          callback: (value) => (value ? "Yes" : "No"),
        },
        checkout_date: "checkout_date",
        checkout_charges: "checkout_charges",
        checkout_remark: "checkout_remark",
        remark: "remark",
        created_at: "created_at",
        updated_at: "updated_at",
      },
    };
  },
  created() {
    document.title = 'Antz Management';
    this.$vuetify.goTo(0);
    this.getReports();
  },
  methods: {
    ...mapActions({
      authenticationAction: "authentication",
      showLoadingAction: "showLoadingAction",
      endLoadingAction: "endLoadingAction",
      getReportsAction: "getReports",
      deleteRentalPaymentAction: "deleteRentalPayment",
    }),
    openPaymentDialog(uid, mode) {
      this.paymentDialog = true;
      this.editMode = mode;
      this.selectedPayment.uid = uid;
    },
    updateRentalPaymentDetails(data) {
      var id = data.id;
      var rentalpayment = data;
      this.unpaidrentals = this.unpaidrentals.map(function (item) {
        if (item.id == id) {
          item.paid = true;
          item.paymentdate = data.paymentdate;
          return item;
        } else {
          return item;
        }
      });
    },
    deleteRentalPaymentDetails(data) {
      var id = data.id;
      this.unpaidrentals = this.unpaidrentals.filter(function (item) {
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
    print(data) {
      this.selectedRental = data;
      this.showLoadingAction();
      setTimeout(() => {
        this.endLoadingAction();
        this.$htmlToPaper("printMe");
      }, 1500);
    },
    getReports() {
      this.showLoadingAction();
      this.getReportsAction()
        .then((res) => {
          console.log("res");
          console.log(res);
          this.endLoadingAction();
          console.log(res.data.currentMonthUnpaidTenantPortion);
          this.unpaidrentals = res.data.unpaidTenant;
          this.todayPaidRentals = _.get(res.data, ["todayPaidRental"]) || [];
          this.roomContractAlmostEnd =
            _.get(res.data, ["roomContractAlmostEnd"]) || [];

          this.updateRoomTypePortion(
            _.get(res.data, ["roomTypesPortion", "data"]) || []
          );
          this.updateRoomStatusPortion(
            _.get(res.data, ["roomStatusPortion", "data"]) || []
          );
          this.updateCurrentMonthUnpaidTenantPortion(
            _.get(res.data, ["currentMonthUnpaidTenantPortion", "data"]) || []
          );
          this.updateMonthlyMaintenancesPortion(
            _.get(res.data, ["monthlyMaintenancesPortion", "data"]) || []
          );
        })
        .catch((err) => {
          this.endLoadingAction();
        });
    },
    updateRoomTypePortion(report) {
      this.roomTypePortionOptions = {
        chart: {
          id: "roomTypePortionDonut",
        },
        labels:
          report.map(function (roomType) {
            return roomType.name;
          }) || [],
        title: {
          text: "Room Type Portion",
        },
        ...this.donutOptions,
      };

      this.roomTypesPortionValues =
        report.map(function (roomType) {
          return roomType.count;
        }) || [];
    },
    updateRoomStatusPortion(report) {
      this.roomStatusPortionOptions = {
        chart: {
          id: "roomStatusPortionDonut",
        },
        labels:
          Object.keys(report || {}).map(function (item) {
            return item;
          }) || [],
        title: {
          text: "Room Status Portion",
        },
        ...this.donutOptions,
      };

      this.roomStatusPortionValues =
        Object.values(report || {}).map(function (item) {
          return item;
        }) || [];
    },
    showRoomContract($data) {
      this.$router.push("/roomcontract/" + $data.uid);
    },
    updateCurrentMonthUnpaidTenantPortion(report) {
      this.currentMonthUnpaidTenantPortionOptions = {
        chart: {
          id: "currentMonthUnpaidTenantPortionDonut",
        },
        labels:
          Object.keys(report || {}).map(function (item) {
            return item;
          }) || [],
        title: {
          text: `${moment().format("MMMM")} Unpaid Tenant Portion`,
        },
        ...this.donutOptions,
      };

      this.currentMonthUnpaidTenantPortionValues =
        Object.values(report || {}).map(function (item) {
          return item;
        }) || [];
    },
    updateMonthlyMaintenancesPortion(report) {
      console.log(report);
      this.monthlyMaintenancesPortion = {
        chart: {
          id: "monthlyMaintenancesPortionDonut",
        },
        labels:
          report.map(function (item) {
            return item.type;
          }) || [],
        title: {
          text: `${moment().format("MMMM")} Maintenance Portion`,
        },
        ...this.donutOptions,
      };

      this.monthlyMaintenancesPortionValues =
        report.map(function (item) {
          return item.total;
        }) || [];
    },
  },
};
</script>

<style>
.scroll {
  overflow-y: scroll;
}
</style>

<template>
  <v-app id="inspire">
    <loading></loading>
    <navbar
      :returnRole="
        (role) => {
          this.role = role;
        }
      "
    ></navbar>
    <v-content class="grey lighten-2">
      <div class="pb-5"></div>

      <v-container>
        <v-row>
          <v-col cols="12" md="6">
            <v-card class="pa-5" height="300px" raised>
              <apexchart
                width="100%"
                height="100%"
                type="donut"
                :options="roomStatusPortionOptions"
                :series="roomStatusPortionValues"
              ></apexchart>
            </v-card>
          </v-col>
          <v-col cols="12" md="6">
            <v-card class="pa-5" height="300px" raised>
              <apexchart
                width="100%"
                height="100%"
                type="donut"
                :options="roomTypePortionOptions"
                :series="roomTypesPortionValues"
              ></apexchart>
            </v-card>
          </v-col>
          <v-col cols="12" md="6">
            <v-card class="pa-5" height="300px" raised>
              <apexchart
                width="100%"
                height="100%"
                type="donut"
                :options="currentMonthUnpaidTenantPortionOptions"
                :series="currentMonthUnpaidTenantPortionValues"
              ></apexchart>
            </v-card>
          </v-col>
          <v-col cols="12" md="6">
            <v-card class="pa-5" height="300px" raised>
              <apexchart
                width="100%"
                height="100%"
                type="donut"
                :options="monthlyMaintenancesPortion"
                :series="monthlyMaintenancesPortionValues"
              ></apexchart>
            </v-card>
          </v-col>
        </v-row>

        <v-row>
          <v-col cols="12">
            <v-card raised>
              <v-card-title class="mx-2">
                <div class="title font-weight-bold">
                  Unpaid Rental Tenant Data
                </div>
                <download-excel
                  :data="unpaidrentals"
                  type="csv"
                  :header="`Unpaid_Rental_Tenant_Data_${moment().format(
                    'YYYY_MM_DD'
                  )}`"
                  :name="`Unpaid_Rental_Tenant_Data_${moment().format(
                    'YYYY_MM_DD'
                  )}.csv`"
                  :fields="rentalPaymentExcelFields || {}"
                  class="float-right mx-5"
                >
                  <v-btn>download</v-btn>
                </download-excel>
              </v-card-title>
              <v-data-table
                :headers="rentalPaymentHeaders"
                :items="unpaidrentals"
                class="ma-2"
                :items-per-page="5"
                disable-sort
              >
                <template v-slot:item="props">
                  <tr>
                    <td class="text-truncate">{{ props.item.sequence }}</td>
                    <td class="text-truncate">{{ props.item.roomcontract.tenant.name }}</td>
                    <td class="text-truncate">{{ props.item.roomcontract.name }}</td>
                    <td class="text-truncate">{{ props.item.roomcontract.room.name }}</td>
                    <td class="text-truncate">{{ props.item.rentaldate | formatDate }}</td>
                    <td class="text-truncate">{{ props.item.price | toDouble }}</td>
                    <td class="text-truncate">{{ props.item.penalty | toDouble }}</td>
                    <td class="text-truncate">{{ props.item.processing_fees | toDouble }}</td>
                    <td class="text-truncate">{{ props.item.service_fees | toDouble }}</td>
                    <td class="text-truncate" v-if="props.item.paid">
                      <v-icon small color="success">mdi-check</v-icon>
                    </td>
                    <td class="text-truncate" v-else>
                      <v-icon small color="danger">mdi-close</v-icon>
                    </td>
                    <td class="text-truncate">{{ props.item.paymentdate | formatDate }}</td>
                    <td class="text-truncate">
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
        </v-row>

        <v-row>
          <v-col cols="12">
            <v-card raised>
              <v-card-title class="mx-2">
                <div class="title font-weight-bold">Today Paid Rental</div>
                <download-excel
                  :data="todayPaidRentals"
                  type="csv"
                  :header="`TotalPaidRentals_${moment().format('YYYY_MM_DD')}`"
                  :name="`TotalPaidRentals_${moment().format(
                    'YYYY_MM_DD'
                  )}.csv`"
                  :fields="rentalPaymentExcelFields || {}"
                  class="float-right mx-5"
                >
                  <v-btn>download</v-btn>
                </download-excel>
              </v-card-title>
              <v-data-table
                :headers="rentalPaymentHeaders"
                :items="todayPaidRentals"
                class="ma-2"
                :items-per-page="5"
                disable-sort
              >
                <template v-slot:item="props">
                  <tr>
                    <td class="text-truncate">{{ props.item.sequence }}</td>
                    <td class="text-truncate">{{ props.item.roomcontract.tenant.name }}</td>
                    <td class="text-truncate">{{ props.item.roomcontract.name }}</td>
                    <td class="text-truncate">{{ props.item.roomcontract.room.name }}</td>
                    <td class="text-truncate">{{ props.item.rentaldate | formatDate }}</td>
                    <td class="text-truncate">{{ props.item.price | toDouble }}</td>
                    <td class="text-truncate">{{ props.item.penalty | toDouble }}</td>
                    <td class="text-truncate">{{ props.item.processing_fees | toDouble }}</td>
                    <td class="text-truncate">{{ props.item.service_fees | toDouble }}</td>
                    <td class="text-truncate" v-if="props.item.paid">
                      <v-icon small color="success">mdi-check</v-icon>
                    </td>
                    <td class="text-truncate" v-else>
                      <v-icon small color="danger">mdi-close</v-icon>
                    </td>
                    <td class="text-truncate">{{ props.item.paymentdate | formatDate }}</td>
                    <td class="text-truncate">
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
                        v-else-if="
                          helpers.isAccessible(
                            _.get(role, ['name']),
                            'rentalPayment',
                            'makePayment'
                          )
                        "
                        small
                        class="mr-2"
                        @click="openPaymentDialog(props.item.uid, false)"
                        color="warning"
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
        </v-row>

        <v-row>
          <v-col cols="12">
            <v-card raised>
              <v-card-title class="mx-2">
                <div class="title font-weight-bold">
                  Room Contract End In 3 Months
                </div>
                <download-excel
                  :data="roomContractAlmostEnd"
                  type="csv"
                  :header="`Room_Contract_End_In_3_Months_${moment().format(
                    'YYYY_MM_DD'
                  )}`"
                  :name="`Room_Contract_End_In_3_Months_${moment().format(
                    'YYYY_MM_DD'
                  )}.csv`"
                  :fields="roomContractExcelFields || {}"
                  class="float-right mx-5"
                >
                  <v-btn>download</v-btn>
                </download-excel>
              </v-card-title>
              <v-data-table
                :headers="roomContractHeaders"
                :items="roomContractAlmostEnd"
                class="ma-2"
                :items-per-page="5"
                disable-sort
              >
                <template v-slot:item="props">
                  <tr @click="showRoomContract(props.item)">
                    <td class="text-truncate">{{ props.item.sequence }}</td>
                    <td class="text-truncate">{{ props.item.contract.name }}</td>
                    <td class="text-truncate">{{ props.item.room.name }}</td>
                    <td class="text-truncate">
                      {{
                        _.get(props.item, ["room", "owners", 0, "name"]) ||
                        "N/A"
                      }}
                    </td>
                    <td class="text-truncate">{{ props.item.tenant.name }}</td>
                    <td class="text-truncate">{{ props.item.startdate }}</td>
                    <td class="text-truncate">{{ props.item.enddate }}</td>
                    <td class="text-truncate">RM {{ props.item.deposit | toDouble }}</td>
                    <td class="text-truncate">RM {{ props.item.outstanding | toDouble }}</td>
                    <td class="text-truncate">RM {{ props.item.rental | toDouble }}</td>
                    <td class="text-truncate">
                      {{ props.item.duration }}
                      {{ props.item.rental_type || "day" }}
                    </td>
                    <td class="text-truncate">
                      {{
                        _.compact(
                          _.map(
                            _.concat(
                              _.get(props.item, ["addonservices"]) || [],
                              _.get(props.item, ["origservices"]) || []
                            ),
                            function (service) {
                              return _.get(service, ["text"]) || "";
                            }
                          )
                        ) | getArrayValues
                      }}
                    </td>
                    <td class="text-truncate text-center" v-if="props.item.checkedout">
                      <v-icon small color="success">mdi-check</v-icon>
                    </td>
                    <td class="text-truncate text-center" v-else>
                      <v-icon small color="danger">mdi-close</v-icon>
                    </td>
                    <td class="text-truncate">
                      {{ props.item.checkout_date | formatDate }}
                    </td>
                  </tr>
                </template>
              </v-data-table>
            </v-card>
          </v-col>
        </v-row>
        <!--  6 card with dividers -->
        <div class="pb-10"></div>
      </v-container>
    </v-content>

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
  </v-app>
</template>
