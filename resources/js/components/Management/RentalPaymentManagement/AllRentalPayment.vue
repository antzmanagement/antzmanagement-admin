<template>
  <v-app>
    <navbar></navbar>
    <v-content :class="helpers.managementStyles().backgroundClass">
      <v-container class="fill-height" fluid>
        <loading></loading>
        <v-row justify="center" align="center" class="ma-3">
          <v-col cols="12">
            <rental-payment-form
              :editMode="false"
              :buttonStyle="rentalPaymentFormDialogConfig.buttonStyle"
              @created="showRentalPayment($event)"
            ></rental-payment-form>
          </v-col>
        </v-row>

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
                  <tr @click="showRentalPayment(props.item)">
                    <td>{{props.item.uid}}</td>
                  </tr>
                </template>
              </v-data-table>
            </v-card>
          </v-col>
        </v-row>
      </v-container>
    </v-content>
  </v-app>
</template>

<script>
import { mapActions } from "vuex";
export default {
  data() {
    return {
      totalDataLength: 0,
      data: [],
      loading: true,
      options: {},
      rentalPaymentFilterGroup: new Form({
        rooms: [],
        selectedRooms: [],
        keyword: null,
        fromdate: null,
        todate: null
      }),
      rentalPaymentFilterDialogConfig: {
        buttonStyle: {
          block: true,
          class: "ma-2",
          text: "Filter",
          icon: "mdi-magnify",
          isIcon: false,
          color: "primary"
        },
        dialogStyle: {
          persistent: true,
          maxWidth: "1200px",
          fullscreen: false,
          hideOverlay: true
        }
      },
      
      rentalPaymentFormDialogConfig: {
        buttonStyle: {
          block: true,
          class: "title font-weight-bold ma-2",
          text: "Add RentalPayment",
          icon: "mdi-plus",
          isIcon: false,
          color: "primary",
          evalation : "5",
        },
      },
      headers: [
        {
          text: "uid",
        },
      ]
    };
  },
  watch: {
    options: {
      handler() {
        this.getRentalPayments();
      },
      deep: true
    }
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
    }
  },
  created() {},
  mounted() {
    this.getRentalPayments();
  },
  methods: {
    ...mapActions({
      getRentalPaymentsAction: "getRentalPayments",
      filterRentalPaymentsAction: "filterRentalPayments",
      showLoadingAction: "showLoadingAction",
      endLoadingAction: "endLoadingAction"
    }),
    initRentalPaymentFilter(filterGroup) {
      this.rentalPaymentFilterGroup.reset();
      if (filterGroup) {
        this.rentalPaymentFilterGroup.selectedRooms = filterGroup.rooms;
        this.rentalPaymentFilterGroup.rooms = filterGroup.rooms.map(function(
          rentalPaymentType
        ) {
          return rentalPaymentType.id;
        });
        this.rentalPaymentFilterGroup.keyword = filterGroup.keyword;
      }
      this.getRentalPayments();
    },
    showRentalPayment($data) {
      this.$router.push("/rentalpayment/" + $data.uid);
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

      this.filterRentalPaymentsAction(this.rentalPaymentFilterGroup)
        .then(data => {
          if (data.data) {
            this.data = data.data;
            console.log(data.data);
          } else {
            this.data = [];
          }
          this.totalDataLength = data.totalResult;
          this.loading = false;
        })
        .catch(error => {
          this.loading = false;
          Toast.fire({
            icon: "warning",
            title: "Something went wrong... "
          });
        });
    }
  }
};
</script>