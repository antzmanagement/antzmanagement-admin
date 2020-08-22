<template>
  <v-app id="inspire">
    <loading></loading>
    <navbar></navbar>
    <v-content class="grey lighten-2">
      <div class="pb-5"></div>

      <v-container>
        <!-- <div id="printMe">
          <v-row justify="start" align="center">
            <v-col cols="12" md="4">
              <v-card class="d-flex flex-wrap justify-center align-center pa-5" height="300px">
                <apexchart
                  width="100%"
                  height="100%"
                  type="bar"
                  :options="options"
                  :series="series"
                ></apexchart>
              </v-card>
            </v-col>
            <v-col cols="12" md="4">
              <v-card class="d-flex flex-wrap justify-center align-center pa-5" height="300px">
                <apexchart
                  width="100%"
                  height="100%"
                  type="bar"
                  :options="options"
                  :series="series1"
                ></apexchart>
              </v-card>
            </v-col>
            <v-col cols="12" md="4">
              <v-card class="d-flex flex-wrap justify-center align-center pa-5" height="300px">
                <apexchart
                  width="100%"
                  height="100%"
                  type="bar"
                  :options="options"
                  :series="series2"
                ></apexchart>
              </v-card>
            </v-col>
          </v-row>
          <button @click="print">Print</button>
        </div>-->
        <v-row justify="start" align="center">
          <v-col cols="12" md="4">
            <v-card class="d-flex flex-wrap justify-center align-center pa-5" height="300px" raised>
              <apexchart width="100%" height="100%" type="bar" :options="options" :series="series"></apexchart>
            </v-card>
          </v-col>
          <v-col cols="12" md="4">
            <v-card class="d-flex flex-wrap justify-center align-center pa-5" height="300px" raised>
              <apexchart width="100%" height="100%" type="bar" :options="options" :series="series1"></apexchart>
            </v-card>
          </v-col>
          <v-col cols="12" md="4">
            <v-card class="d-flex flex-wrap justify-center align-center pa-5" height="300px" raised>
              <apexchart width="100%" height="100%" type="bar" :options="options" :series="series2"></apexchart>
            </v-card>
          </v-col>
        </v-row>
        <v-row>
          <v-col cols="12" md="6">
            <v-card height="300px" class="d-inline-block" width="100%" raised>
              <v-card-title class="title font-weight-bold mx-2" height="20%">Activity</v-card-title>
              <v-card class="scroll" height="80%">
                <v-list two-line>
                  <v-list-item v-for="(item, i) in items" :key="i" class="mx-2">
                    <v-list-item-avatar>
                      <v-icon :class="[item.iconClass]" v-text="item.icon"></v-icon>
                    </v-list-item-avatar>

                    <v-list-item-content>
                      <v-list-item-title v-text="item.title"></v-list-item-title>
                      <v-list-item-subtitle v-text="item.subtitle"></v-list-item-subtitle>
                    </v-list-item-content>

                    <v-list-item-action>
                      <v-btn icon>
                        <v-icon color="grey lighten-1">mdi-information</v-icon>
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
                :options="donutoptions"
                :series="donutseries"
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
                :options="options"
                :series="series2"
              ></apexchart>
            </v-card>
          </v-col>
        </v-row>
        <!-- cards -->
        <v-row justify="center" align="center">
          <v-col cols="12" md="3">
            <v-card class="mx-3 text-center pa-5" raised>
              <div class="display-1 font-weight-black warning--text">500</div>
              <div class="headline font-weight-black warning--text">Clients</div>
            </v-card>
          </v-col>
          <v-col cols="12" md="3">
            <v-card class="mx-3 text-center pa-5" raised>
              <div class="display-1 font-weight-black green--text text--darken-2">1st</div>
              <div class="headline font-weight-black green--text text--darken-2">Company</div>
            </v-card>
          </v-col>
          <v-col cols="12" md="3">
            <v-card class="mx-3 text-center pa-5" raised>
              <div class="display-1 font-weight-black blue--text text--darken-2">8,000</div>
              <div class="headline font-weight-black blue--text text--darken-2">Projects</div>
            </v-card>
          </v-col>
          <v-col cols="12" md="3">
            <v-card class="mx-3 text-center pa-5" raised>
              <div class="display-1 font-weight-black red--text text--darken-2">900</div>
              <div class="headline font-weight-black red--text text--darken-2">Tasks</div>
            </v-card>
          </v-col>
        </v-row>
        <!-- End of cards -->

        <v-row>
          <v-col cols="12">
            <v-card raised>
              <v-card-title class="mx-2">
                <div class="title font-weight-bold">Dynamic Table Data</div>
                <download-excel :data="desserts" type="csv" class="float-right mx-5">
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
                :items="desserts"
                class="ma-2"
                :items-per-page="5"
                :search="search"
              ></v-data-table>
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
                    <div class="headline text-center mx-2 font-weight-bold">Total Orders</div>
                    <div class="caption text-center font-weight-light">Last Year Expenses</div>
                  </v-col>
                  <v-col cols="6">
                    <div class="text-center display-2 blue--text text--darken-2">989</div>
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
                    <div class="headline text-center mx-2 font-weight-bold">Clients</div>
                    <div class="caption text-center font-weight-light">Total Clients Profit</div>
                  </v-col>
                  <v-col cols="6">
                    <div class="text-center display-2 cyan--text text--darken-2">6500</div>
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
                    <div class="headline text-center mx-2 font-weight-bold">Followers</div>
                    <div class="caption text-center font-weight-light">People Interested</div>
                  </v-col>
                  <v-col cols="6">
                    <div class="text-center display-2 light-green--text text--darken-2">2.5k</div>
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
                    <div class="headline text-center mx-2 font-weight-bold">Products Sold</div>
                    <div class="caption text-center font-weight-light">Total revenue streams</div>
                  </v-col>
                  <v-col cols="6">
                    <div class="text-center display-2 orange--text text--darken-2">3.3M</div>
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
                    <div class="headline text-center mx-2 font-weight-bold">Revenue</div>
                    <div class="caption text-center font-weight-light">Total profit</div>
                  </v-col>
                  <v-col cols="6">
                    <div class="text-center display-2 deep-orange--text text--darken-2">9.3M</div>
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
                    <div class="headline text-center mx-2 font-weight-bold">Branches</div>
                    <div class="caption text-center font-weight-light">Total physical store</div>
                  </v-col>
                  <v-col cols="6">
                    <div class="text-center display-2 purple--text text--darken-2">1.5k</div>
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
  </v-app>
</template>

<script>
import { mapActions } from "vuex";
export default {
  data() {
    return {
      options: {
        chart: {
          id: "vuechart-example"
        },
        xaxis: {
          categories: [1991, 1992, 1993, 1994, 1995, 1996, 1997, 1998]
        },
        dataLabels: {
          enabled: false
        },
        plotOptions: {
          bar: {
            distributed: true
          }
        },
        theme: {
          palette: "palette1"
        },
        legend: {
          show: false
        },
        title: {
          text: "Sample Report"
        }
      },
      series: [
        {
          name: "series-1",
          data: [30, 40, 45, 50, 49, 60, 70, 91]
        }
      ],
      series1: [
        {
          name: "series-1",
          data: [91, 70, 45, 60, 49, 70, 45, 49]
        }
      ],
      series2: [
        {
          name: "series-1",
          data: [30, 90, 45, 70, 80, 20, 40, 30]
        }
      ],
      donutoptions: {
        chart: {
          id: "vuechart-example"
        },
        plotOptions: {
          pie: {
            customScale: 1,
            offsetX: 0,
            offsetY: 0,
            expandOnClick: true,
            dataLabels: {
              offset: 0,
              minAngleToShowLabel: 10
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
                  formatter: function(w) {
                    return w.globals.seriesTotals.reduce((a, b) => {
                      return a + b;
                    }, 0);
                  }
                }
              }
            }
          }
        },
        theme: {
          palette: "palette1"
        },
        title: {
          text: "Sample Report"
        }
      },
      donutseries: [30, 90, 45, 70, 80, 20, 40, 30],

      items: [
        {
          icon: "mdi-bell",
          iconClass: "grey lighten-1 white--text",
          title: "New Order Placed",
          subtitle: "Order placed at 10:00 a.m. 06/09/2019"
        },
        {
          icon: "mdi-alert-circle-outline",
          iconClass: "grey lighten-1 white--text",
          title: "Product Out Of Stock",
          subtitle: "Product - Item 1 sold out at 10:00 a.m. 06/09/2019"
        },
        {
          icon: "mdi-account",
          iconClass: "grey lighten-1 white--text",
          title: "New Task Added",
          subtitle: "Staff 1 added new task at Jan 28, 2014"
        },
        {
          icon: "mdi-bell",
          iconClass: "grey lighten-1 white--text",
          title: "New Order Placed",
          subtitle: "Order placed at 10:00 a.m. 06/09/2019"
        },
        {
          icon: "mdi-alert-circle-outline",
          iconClass: "grey lighten-1 white--text",
          title: "Product Out Of Stock",
          subtitle: "Product - Item 1 sold out at 10:00 a.m. 06/09/2019"
        },
        {
          icon: "mdi-account",
          iconClass: "grey lighten-1 white--text",
          title: "New Task Added",
          subtitle: "Staff 1 added new task at Jan 28, 2014"
        },
        {
          icon: "mdi-bell",
          iconClass: "grey lighten-1 white--text",
          title: "New Order Placed",
          subtitle: "Order placed at 10:00 a.m. 06/09/2019"
        },
        {
          icon: "mdi-alert-circle-outline",
          iconClass: "grey lighten-1 white--text",
          title: "Product Out Of Stock",
          subtitle: "Product - Item 1 sold out at 10:00 a.m. 06/09/2019"
        },
        {
          icon: "mdi-account",
          iconClass: "grey lighten-1 white--text",
          title: "New Task Added",
          subtitle: "Staff 1 added new task at Jan 28, 2014"
        },
        {
          icon: "mdi-bell",
          iconClass: "grey lighten-1 white--text",
          title: "New Order Placed",
          subtitle: "Order placed at 10:00 a.m. 06/09/2019"
        },
        {
          icon: "mdi-alert-circle-outline",
          iconClass: "grey lighten-1 white--text",
          title: "Product Out Of Stock",
          subtitle: "Product - Item 1 sold out at 10:00 a.m. 06/09/2019"
        },
        {
          icon: "mdi-account",
          iconClass: "grey lighten-1 white--text",
          title: "New Task Added",
          subtitle: "Staff 1 added new task at Jan 28, 2014"
        }
      ],
      search: "",
      headers: [
        {
          text: "Dessert (100g serving)",
          align: "start",
          value: "name"
        },
        { text: "Calories", value: "calories" },
        { text: "Fat (g)", value: "fat" },
        { text: "Carbs (g)", value: "carbs" },
        { text: "Protein (g)", value: "protein" },
        { text: "Iron (%)", value: "iron" }
      ],
      desserts: [
        {
          name: "Frozen Yogurt",
          calories: 159,
          fat: 6.0,
          carbs: 24,
          protein: 4.0,
          iron: "1%"
        },
        {
          name: "Ice cream sandwich",
          calories: 237,
          fat: 9.0,
          carbs: 37,
          protein: 4.3,
          iron: "1%"
        },
        {
          name: "Eclair",
          calories: 262,
          fat: 16.0,
          carbs: 23,
          protein: 6.0,
          iron: "7%"
        },
        {
          name: "Cupcake",
          calories: 305,
          fat: 3.7,
          carbs: 67,
          protein: 4.3,
          iron: "8%"
        },
        {
          name: "Gingerbread",
          calories: 356,
          fat: 16.0,
          carbs: 49,
          protein: 3.9,
          iron: "16%"
        },
        {
          name: "Jelly bean",
          calories: 375,
          fat: 0.0,
          carbs: 94,
          protein: 0.0,
          iron: "0%"
        },
        {
          name: "Lollipop",
          calories: 392,
          fat: 0.2,
          carbs: 98,
          protein: 0,
          iron: "2%"
        },
        {
          name: "Honeycomb",
          calories: 408,
          fat: 3.2,
          carbs: 87,
          protein: 6.5,
          iron: "45%"
        },
        {
          name: "Cupcake",
          calories: 305,
          fat: 3.7,
          carbs: 67,
          protein: 4.3,
          iron: "8%"
        },
        {
          name: "Gingerbread",
          calories: 356,
          fat: 16.0,
          carbs: 49,
          protein: 3.9,
          iron: "16%"
        },
        {
          name: "Jelly bean",
          calories: 375,
          fat: 0.0,
          carbs: 94,
          protein: 0.0,
          iron: "0%"
        },
        {
          name: "Lollipop",
          calories: 392,
          fat: 0.2,
          carbs: 98,
          protein: 0,
          iron: "2%"
        },
        {
          name: "Honeycomb",
          calories: 408,
          fat: 3.2,
          carbs: 87,
          protein: 6.5,
          iron: "45%"
        },
        {
          name: "Donut",
          calories: 452,
          fat: 25.0,
          carbs: 51,
          protein: 4.9,
          iron: "22%"
        },
        {
          name: "KitKat",
          calories: 518,
          fat: 26.0,
          carbs: 65,
          protein: 7,
          iron: "6%"
        },
        {
          name: "Cupcake",
          calories: 305,
          fat: 3.7,
          carbs: 67,
          protein: 4.3,
          iron: "8%"
        },
        {
          name: "Gingerbread",
          calories: 356,
          fat: 16.0,
          carbs: 49,
          protein: 3.9,
          iron: "16%"
        },
        {
          name: "Jelly bean",
          calories: 375,
          fat: 0.0,
          carbs: 94,
          protein: 0.0,
          iron: "0%"
        },
        {
          name: "Lollipop",
          calories: 392,
          fat: 0.2,
          carbs: 98,
          protein: 0,
          iron: "2%"
        },
        {
          name: "Honeycomb",
          calories: 408,
          fat: 3.2,
          carbs: 87,
          protein: 6.5,
          iron: "45%"
        }
      ]
    };
  },
  created() {
    this.$vuetify.goTo(0);
    this.showLoadingAction();
    this.authenticationAction()
      .then(data => {
        this.endLoadingAction();
      })
      .catch(error => {
        console.log("error");
        this.endLoadingAction();
        this.$router.push("/login");
      });
  },
  methods: {
    ...mapActions({
      authenticationAction: "authentication",
      showLoadingAction: "showLoadingAction",
      endLoadingAction: "endLoadingAction"
    }),
    print() {
      this.$htmlToPaper("printMe");
    }
  }
};
</script>

<style>
.scroll {
  overflow-y: scroll;
}
</style>