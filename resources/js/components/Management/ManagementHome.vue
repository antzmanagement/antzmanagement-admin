
<script>
import { mapActions } from "vuex";
import { insertBetween, notEmptyLength } from "../../common/common-function";

export default {
  data() {
    return {
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
      roomTypesPortionValues: [],
      options1: {
        chart: {
          id: "vuechart-example",
        },
        xaxis: {
          categories: ["05/2020", "06/2020", "07/2020", "08/2020", "09/2020"],
        },
        dataLabels: {
          enabled: false,
        },
        plotOptions: {
          bar: {
            distributed: true,
          },
        },
        theme: {
          palette: "palette1",
        },
        legend: {
          show: false,
        },
        title: {
          text: "Monthly Revenue",
        },
      },
      options2: {
        chart: {
          id: "vuechart-example",
        },
        xaxis: {
          categories: ["05/2020", "06/2020", "07/2020", "08/2020", "09/2020"],
        },
        dataLabels: {
          enabled: false,
        },
        plotOptions: {
          bar: {
            distributed: true,
          },
        },
        theme: {
          palette: "palette1",
        },
        legend: {
          show: false,
        },
        title: {
          text: "Rental Revenue",
        },
      },
      options3: {
        chart: {
          id: "vuechart-example",
        },
        xaxis: {
          categories: ["05/2020", "06/2020", "07/2020", "08/2020", "09/2020"],
        },
        dataLabels: {
          enabled: false,
        },
        plotOptions: {
          bar: {
            distributed: true,
          },
        },
        theme: {
          palette: "palette1",
        },
        legend: {
          show: false,
        },
        title: {
          text: "Commission Revenue",
        },
      },
      options4: {
        chart: {
          id: "vuechart-example",
        },
        xaxis: {
          categories: [
            "01/2020",
            "02/2020",
            "03/2020",
            "04/2020",
            "05/2020",
            "06/2020",
            "07/2020",
            "08/2020",
            "09/2020",
            "10/2020",
            "11/2020",
            "12/2020",
            "01/2021",
            "02/2021",
            "03/2021",
          ],
        },
        dataLabels: {
          enabled: false,
        },
        plotOptions: {
          bar: {
            distributed: true,
          },
        },
        theme: {
          palette: "palette1",
        },
        legend: {
          show: false,
        },
        title: {
          text: "Monthly Revenue",
        },
      },
      series1: [
        {
          name: "revenue",
          data: [91, 70, 45, 60, 49],
        },
      ],
      series2: [
        {
          name: "revenue",
          data: [10, 70, 20, 60, 30],
        },
      ],
      series3: [
        {
          name: "revenue",
          data: [30, 50, 10, 60, 70],
        },
      ],
      series4: [
        {
          name: "revenue",
          data: [30, 50, 10, 60, 70, 30, 50, 10, 60, 70, 30, 50, 10, 60, 70],
        },
      ],
      roomTypePortionOptions: {},
      donutseries: [30, 90, 45, 70, 80],
      items: [
        {
          icon: "mdi-account",
          iconClass: "grey lighten-1 white--text",
          title: "New Task Added",
          subtitle: "Check room 302 status",
        },
        {
          icon: "mdi-account",
          iconClass: "grey lighten-1 white--text",
          title: "Urgent",
          subtitle: "Income statement report",
        },
        {
          icon: "mdi-account",
          iconClass: "grey lighten-1 white--text",
          title: "Check in",
          subtitle: "Room 123 check in at 2pm 20/07",
        },
      ],
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
    };
  },
  created() {
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
      console.log("rentalpayment");
      console.log(rentalpayment);
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
      console.log(this.selectedRental);
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
          this.endLoadingAction();
          this.unpaidrentals = res.data.unpaidTenant;

          this.updateRoomTypePortion(res.data.roomTypesPortion.data);
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
        title: {
          text: "Room Type Portion",
        },
      };

      this.roomTypesPortionValues = report.map(function (roomType) {
        return roomType.count;
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
    <navbar></navbar>
    <v-content class="grey lighten-2">
      <div class="pb-5"></div>

      <v-container>
        <v-row justify="start" align="center">
          <v-col cols="12" md="4">
            <v-card
              class="d-flex flex-wrap justify-center align-center pa-5"
              height="300px"
              raised
            >
              <apexchart
                width="100%"
                height="100%"
                type="bar"
                :options="options1"
                :series="series1"
              ></apexchart>
            </v-card>
          </v-col>
          <v-col cols="12" md="4">
            <v-card
              class="d-flex flex-wrap justify-center align-center pa-5"
              height="300px"
              raised
            >
              <apexchart
                width="100%"
                height="100%"
                type="bar"
                :options="options2"
                :series="series2"
              ></apexchart>
            </v-card>
          </v-col>
          <v-col cols="12" md="4">
            <v-card
              class="d-flex flex-wrap justify-center align-center pa-5"
              height="300px"
              raised
            >
              <apexchart
                width="100%"
                height="100%"
                type="bar"
                :options="options3"
                :series="series3"
              ></apexchart>
            </v-card>
          </v-col>
        </v-row>
        <v-row>
          <v-col cols="12" md="6">
            <v-card height="300px" class="d-inline-block" width="100%" raised>
              <v-card-title class="title font-weight-bold mx-2" height="20%"
                >Activity</v-card-title
              >
              <v-card class="scroll" height="80%">
                <v-list two-line>
                  <v-list-item v-for="(item, i) in items" :key="i" class="mx-2">
                    <v-list-item-avatar>
                      <v-icon
                        :class="[item.iconClass]"
                        v-text="item.icon"
                      ></v-icon>
                    </v-list-item-avatar>

                    <v-list-item-content>
                      <v-list-item-title
                        v-text="item.title"
                      ></v-list-item-title>
                      <v-list-item-subtitle
                        v-text="item.subtitle"
                      ></v-list-item-subtitle>
                    </v-list-item-content>

                    <v-list-item-action>
                      <v-btn icon>
                        <v-icon color="grey lighten-1">mdi-close</v-icon>
                      </v-btn>
                    </v-list-item-action>
                  </v-list-item>
                </v-list>
              </v-card>
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
        </v-row>

        <v-row justify="start" align="center">
          <v-col cols="12">
            <v-card class="pa-5" height="300px" raised>
              <apexchart
                width="100%"
                height="100%"
                type="line"
                :options="options4"
                :series="series4"
              ></apexchart>
            </v-card>
          </v-col>
        </v-row>
        <!-- cards -->
        <v-row justify="center" align="center">
          <v-col cols="12" md="3">
            <v-card class="mx-3 text-center pa-5" raised>
              <div class="display-1 font-weight-black warning--text">500</div>
              <div class="headline font-weight-black warning--text">
                Tenants
              </div>
            </v-card>
          </v-col>
          <v-col cols="12" md="3">
            <v-card class="mx-3 text-center pa-5" raised>
              <div
                class="display-1 font-weight-black green--text text--darken-2"
              >
                1,000
              </div>
              <div
                class="headline font-weight-black green--text text--darken-2"
              >
                Rooms
              </div>
            </v-card>
          </v-col>
          <v-col cols="12" md="3">
            <v-card class="mx-3 text-center pa-5" raised>
              <div
                class="display-1 font-weight-black blue--text text--darken-2"
              >
                20
              </div>
              <div class="headline font-weight-black blue--text text--darken-2">
                Room Types
              </div>
            </v-card>
          </v-col>
          <v-col cols="12" md="3">
            <v-card class="mx-3 text-center pa-5" raised>
              <div class="display-1 font-weight-black red--text text--darken-2">
                100
              </div>
              <div class="headline font-weight-black red--text text--darken-2">
                Sold Room
              </div>
            </v-card>
          </v-col>
        </v-row>
        <!-- End of cards -->

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
                  class="float-right mx-5"
                >
                  <v-btn>download</v-btn>
                </download-excel>
              </v-card-title>
              <v-card-title class="ma-2">
                <v-text-field
                  v-model="search"
                  append-icon="search"
                  label="Search"
                  single-line
                  hide-details
                ></v-text-field>
              </v-card-title>
              <v-data-table
                :headers="headers"
                :items="unpaidrentals"
                class="ma-2"
                :items-per-page="5"
                :search="search"
              >
                <template v-slot:[`item.action`]="{ item }">
                  <v-icon
                    small
                    class="mr-2"
                    @click="print(item)"
                    v-if="item.paid"
                    color="success"
                    >mdi-printer</v-icon
                  >
                  <!-- <rental-print
                        @click="selectedRental = item"
                        class="mr-2"
                        v-if="item.paid"
                        :buttonStyle="rentalPrintButtonConfig.buttonStyle"
                        :room="selectedRental.roomcontract.room"
                        :roomcontract="selectedRental.roomcontract"
                        :tenant="selectedRental.roomcontract.tenant"
                        :rentalpayment="selectedRental"
                  >mdi-pencil</rental-print>-->
                  <v-icon
                    small
                    class="mr-2"
                    @click="openPaymentDialog(item.uid, false)"
                    color="warning"
                    v-else
                    >mdi-currency-usd</v-icon
                  >

                  <confirm-dialog
                    :activatorStyle="deleteRentalButtonConfig.activatorStyle"
                    @confirmed="$event ? deleteRentalPayment(item.uid) : null"
                  ></confirm-dialog>
                </template>
              </v-data-table>
            </v-card>
          </v-col>
        </v-row>

        <!-- 6 card with dividers -->
        <v-row no-gutters justify="center">
          <v-col cols="12" md="4">
            <v-card flat tile>
              <v-container>
                <v-row no-gutters justify="center">
                  <v-col cols="6">
                    <div class="headline text-center mx-2 font-weight-bold">
                      Tenant
                    </div>
                    <div class="caption text-center font-weight-light">
                      All of the years
                    </div>
                  </v-col>
                  <v-col cols="6">
                    <div
                      class="text-center display-2 blue--text text--darken-2"
                    >
                      5,000
                    </div>
                  </v-col>
                </v-row>
              </v-container>
            </v-card>
          </v-col>
          <v-col cols="12" md="4">
            <v-card flat tile>
              <v-container>
                <v-row no-gutters justify="center">
                  <v-col cols="6">
                    <div class="headline text-center mx-2 font-weight-bold">
                      Rented Room
                    </div>
                    <div class="caption text-center font-weight-light">
                      All of the years
                    </div>
                  </v-col>
                  <v-col cols="6">
                    <div
                      class="text-center display-2 cyan--text text--darken-2"
                    >
                      5,000
                    </div>
                  </v-col>
                </v-row>
              </v-container>
            </v-card>
          </v-col>
          <v-col cols="12" md="4">
            <v-card flat tile>
              <v-container>
                <v-row no-gutters justify="center">
                  <v-col cols="6">
                    <div class="headline text-center mx-2 font-weight-bold">
                      Maintenance
                    </div>
                    <div class="caption text-center font-weight-light">
                      All of the years
                    </div>
                  </v-col>
                  <v-col cols="6">
                    <div
                      class="text-center display-2 light-green--text text--darken-2"
                    >
                      100
                    </div>
                  </v-col>
                </v-row>
              </v-container>
            </v-card>
          </v-col>
        </v-row>

        <v-row no-gutters justify="center" class="mt-1">
          <v-col cols="12" md="4">
            <v-card flat tile>
              <v-container>
                <v-row no-gutters justify="center">
                  <v-col cols="6">
                    <div class="headline text-center mx-2 font-weight-bold">
                      Revenue
                    </div>
                    <div class="caption text-center font-weight-light">
                      Total revenue streams
                    </div>
                  </v-col>
                  <v-col cols="6">
                    <div
                      class="text-center display-2 orange--text text--darken-2"
                    >
                      3.3M
                    </div>
                  </v-col>
                </v-row>
              </v-container>
            </v-card>
          </v-col>
          <v-col cols="12" md="4">
            <v-card flat tile>
              <v-container>
                <v-row no-gutters justify="center">
                  <v-col cols="6">
                    <div class="headline text-center mx-2 font-weight-bold">
                      Rental
                    </div>
                    <div class="caption text-center font-weight-light">
                      Total profit
                    </div>
                  </v-col>
                  <v-col cols="6">
                    <div
                      class="text-center display-2 deep-orange--text text--darken-2"
                    >
                      2M
                    </div>
                  </v-col>
                </v-row>
              </v-container>
            </v-card>
          </v-col>
          <v-col cols="12" md="4">
            <v-card flat tile>
              <v-container>
                <v-row no-gutters justify="center">
                  <v-col cols="6">
                    <div class="headline text-center mx-2 font-weight-bold">
                      Commission
                    </div>
                    <div class="caption text-center font-weight-light">
                      Total profit
                    </div>
                  </v-col>
                  <v-col cols="6">
                    <div
                      class="text-center display-2 purple--text text--darken-2"
                    >
                      1.3M
                    </div>
                  </v-col>
                </v-row>
              </v-container>
            </v-card>
          </v-col>
        </v-row>

        <!--  6 card with dividers -->
        <div class="pb-10"></div>
      </v-container>
    </v-content>

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
            <div :class="helpers.managementStyles().subtitleClass">Tenant</div>
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
            <div :class="helpers.managementStyles().subtitleClass">Room</div>
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
            <div :class="helpers.managementStyles().subtitleClass">Rental</div>
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
            <div :class="helpers.managementStyles().subtitleClass">Price</div>
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
            <div :class="helpers.managementStyles().subtitleClass">Penalty</div>
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
