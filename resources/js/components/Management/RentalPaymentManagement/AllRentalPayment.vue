
<script>
import { mapActions } from "vuex";
import { getArrayValues, _ } from "../../../common/common-function";
import moment from "moment";
export default {
  data() {
    return {
      moment: moment,
      _: _,
      editMode: false,
      paymentPayDialog: false,
      addOnPaymentEditMode: false,
      addOnPaymentDialog: false,
      paymentDialog: false,
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
        paymentfromdate: null,
        paymenttodate: null,
        paid: null,
        status: 1,
      }),
      paymentFilterGroup: new Form({
        rooms: [],
        selectedRooms: [],
        keyword: null,
        fromdate: null,
        todate: null,
        paymentfromdate: null,
        paymenttodate: null,
        paid: null,
        status: 1,
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
          text: "Receipt No",
        },
        {
          text: "Reference No",
        },
        {
          text: "Tenant",
        },
        {
          text: "Room",
        },
        {
          text: "Contract Start Date",
        },
        {
          text: "Contract End Date",
        },
        {
          text: "Rental Date",
        },
        {
          text: "Payment Date",
        },
        {
          text: "Rental Price (RM)",
        },
        {
          text: "Penalty (RM)",
        },
        {
          text: "Processing Fees (RM)",
        },
        {
          text: "Grand Total (RM)",
        },
        {
          text: "Payment (RM)",
        },
        {
          text: "Oustanding (RM)",
        },
        {
          text: "Remark",
        },
        {
          text: "Paid",
        },
        {
          text: "Actions",
        },
        {
          text: "Payment Method",
        },
        {
          text: "Receive From",
        },
        {
          text: "Issue By",
        },
      ],
      paymentHeaders: [
        {
          text: "Receipt No",
          value: "receiptno",
        },
        {
          text: "Reference No",
        },
        {
          text: "Tenant",
        },
        {
          text: "Room",
        },
        {
          text: "Contract Start Date",
        },
        {
          text: "Contract End Date",
        },
        { text: "Payment Date", value: "paymentdate" },
        {
          text: "Services",
          value: "services",
        },
        { text: "Service Price (RM)", value: "price" },
        { text: "Other Payments" },
        { text: "Other Charges (RM)", value: "other_charges" },
        {
          text: "Processing Fees (RM)",
        },
        {
          text: "Grand Total (RM)",
        },
        {
          text: "Total Payment (RM)",
        },
        {
          text: "Outstanding (RM)",
        },
        {
          text: "Remark",
        },
        {
          text: "Paid",
        },
        { text: "Remark", value: "remark" },
        { text: "Actions" },
        {
          text: "Payment Method",
        },
        {
          text: "Receive From",
        },
        {
          text: "Issue By",
        },
      ],
      paymentExcelData: [],
      paymentExcelFields: {
        id: "id",
        uid: "uid",
        referenceno: "referenceno",
        receiptno: "receiptno",
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
        service_payment: "price",
        other_payment: "other_charges",
        paymentdate: "paymentdate",
        services: {
          field: "services",
          callback: (value) => {
            return getArrayValues(_.map(value, "name")) || "N/A";
          },
        },
        other_services: {
          field: "otherpayments",
          callback: (value) => {
            return getArrayValues(_.map(value, "name")) || "N/A";
          },
        },
        remark: "remark",
        created_at: "created_at",
        updated_at: "updated_at",
      },
      rentalPaymentExcelData: [],
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
    };
  },
  watch: {
    options: {
      handler() {
        this.getRentalPayments();
      },
      deep: true,
    },
    paymentPayDialog: {
      handler(val, oldVal) {
        if (!val) {
          this.selectedAddOnPayment = { uid: "" };
        }
      },
      deep: true,
    },
    addOnPaymentDialog: {
      handler(val, oldVal) {
        if (!val) {
          this.selectedAddOnPayment = { uid: "" };
        }
      },
      deep: true,
    },
    paymentDialog: {
      handler(val, oldVal) {
        if (!val) {
          this.selectedPayment = { uid: "" };
        }
      },
      deep: true,
    },
    paymentOptions: {
      handler() {
        // this.getPayments();
      },
      deep: true,
    },
    paymentTotal(v) {
      if (v > 0) {
        // this.fetchPaymentExcelData();
      }
    },
    rentalPaymentTotal(v) {
      if (v > 0) {
        // this.fetchRentalPaymentExcelData();
      }
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
  created() {
    document.title = "All Payment";
  },
  mounted() {
    this.getRentalPayments();
    // this.getPayments();
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
    openAddOnPaymentDialog(uid, mode) {
      this.addOnPaymentDialog = true;
      this.selectedAddOnPayment.uid = uid;
      this.addOnPaymentEditMode = mode;
    },
    openPaymentPayDialog(uid, mode) {
      this.paymentPayDialog = true;
      this.selectedAddOnPayment.uid = uid;
    },
    initRentalPaymentFilter(filterGroup) {
      this.rentalPaymentFilterGroup.reset();
      if (filterGroup.tenant) {
        this.rentalPaymentFilterGroup.tenant_id = filterGroup.tenant.id;
        this.rentalPaymentFilterGroup.tenant = filterGroup.tenant.name;
      }
      if (filterGroup.room) {
        this.rentalPaymentFilterGroup.room_id = filterGroup.room.id;
        this.rentalPaymentFilterGroup.room = filterGroup.room.unit;
      }
      if (filterGroup.fromdate) {
        this.rentalPaymentFilterGroup.fromdate = filterGroup.fromdate;
      }
      if (filterGroup.todate) {
        this.rentalPaymentFilterGroup.todate = filterGroup.todate;
      }
      if (filterGroup.paymentfromdate) {
        this.rentalPaymentFilterGroup.paymentfromdate =
          filterGroup.paymentfromdate;
      }
      if (filterGroup.paymenttodate) {
        this.rentalPaymentFilterGroup.paymenttodate = filterGroup.paymenttodate;
      }
      if (filterGroup.sequence) {
        this.rentalPaymentFilterGroup.sequence = filterGroup.sequence;
      }
      if (filterGroup.paymentmethod) {
        this.rentalPaymentFilterGroup.paymentmethod = filterGroup.paymentmethod;
      }
      if (filterGroup.penalty === 1 || filterGroup.penalty === 0) {
        this.rentalPaymentFilterGroup.penalty = filterGroup.penalty;
      }
      if (filterGroup.paid === 1 || filterGroup.paid === 0) {
        this.rentalPaymentFilterGroup.paid = filterGroup.paid;
      }
      if (filterGroup.status === 1 || filterGroup.status === 0) {
        this.rentalPaymentFilterGroup.status = filterGroup.status;
      }

      this.options.page = 1;
      this.getRentalPayments();
    },
    initPaymentFilter(filterGroup) {
      this.paymentFilterGroup.reset();
      if (filterGroup.tenant) {
        this.paymentFilterGroup.tenant_id = filterGroup.tenant.id;
        this.paymentFilterGroup.tenant = filterGroup.tenant.name;
      }
      if (filterGroup.room) {
        this.paymentFilterGroup.room_id = filterGroup.room.id;
        this.paymentFilterGroup.room = filterGroup.room.unit;
      }
      if (filterGroup.fromdate) {
        this.paymentFilterGroup.fromdate = filterGroup.fromdate;
      }
      if (filterGroup.todate) {
        this.paymentFilterGroup.todate = filterGroup.todate;
      }
      if (filterGroup.services) {
        this.paymentFilterGroup.service_ids =
          _.map(filterGroup.services, "id") || [];
        this.paymentFilterGroup.services = filterGroup.services;
      }
      if (filterGroup.paid === 1 || filterGroup.paid === 0) {
        this.paymentFilterGroup.paid = filterGroup.paid;
      }

      if (filterGroup.status === 1 || filterGroup.status === 0) {
        this.paymentFilterGroup.status = filterGroup.status;
      }
      if (filterGroup.paymentmethod) {
        this.paymentFilterGroup.paymentmethod = filterGroup.paymentmethod;
      }
      if (filterGroup.otherPaymentTitle) {
        this.paymentFilterGroup.otherPaymentTitle =
          filterGroup.otherPaymentTitle;
      }

      this.paymentOptions.page = 1;
      this.getPayments();
    },
    showRentalPayment($data) {
      let routeData = this.$router.resolve({
        name: "rentalpayment",
        params: { uid: $data.uid },
      });
      window.open(routeData.href, "_blank");
      // this.$router.push("/rentalpayment/" + $data.uid);
    },
    updateRentalPaymentDetails(data) {
      var id = data.id;
      var rentalpayment = data;
      this.data = this.data.map(function (item) {
        if (item.id == id) {
          return rentalpayment;
        } else {
          return item;
        }
      });
    },
    updatePaymentDetails(data) {
      var id = data.id;
      var payment = data;
      if (_.some(this.paymentData, ["id", id])) {
        this.paymentData = _.map(this.paymentData, function (item) {
          if (item.id == id) {
            return payment;
          }
          return item;
        });
      } else {
        this.paymentData = _.concat(this.paymentData, [data]);
      }
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
      let filterGroup = { ...this.rentalPaymentFilterGroup };
      if (totalResult == -1) {
        filterGroup.pageNumber = -1;
        filterGroup.pageSize = -1;
      } else {
        filterGroup.pageNumber = page;
        filterGroup.pageSize = itemsPerPage;
      }

      delete filterGroup.tenant;
      delete filterGroup.room;
      this.filterRentalPaymentsAction(filterGroup)
        .then((data) => {
          console.log(data);
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

      let filterGroup = { ...this.paymentFilterGroup };
      //Show All Items
      if (totalResult == -1) {
        filterGroup.pageNumber = -1;
        filterGroup.pageSize = -1;
      } else {
        filterGroup.pageNumber = page;
        filterGroup.pageSize = itemsPerPage;
      }

      delete filterGroup.tenant;
      delete filterGroup.room;
      delete filterGroup.services;

      this.filterPaymentsAction(filterGroup)
        .then((data) => {
          if (data.data) {
            this.paymentData = data.data;
          } else {
            this.paymentData = [];
          }
          this.paymentTotal = data.totalResult;
          this.paymentLoading = false;
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
      this.showLoadingAction();
      setTimeout(() => {
        this.endLoadingAction();
        this.$htmlToPaper("printMe");
      }, 2000);
    },
    async fetchPaymentExcelData() {
      let total = this.paymentTotal || 0;
      let size = 50;
      let maxPage = Math.ceil(total / size);
      let promises = [];
      let self = this;
      _.forEach(_.range(maxPage), function (index) {
        promises.push(
          self.filterPaymentsAction({
            pageSize: size,
            pageNumber: index + 1,
          })
        );
      });

      let responses = await Promise.all(promises);
      let finalData = [];
      _.forEach(responses, function (response) {
        finalData = _.compact(
          _.concat(finalData, _.get(response, `data`) || [])
        );
      });
      this.paymentExcelData = finalData;
      return finalData;
    },
    async fetchRentalPaymentExcelData() {
      let total = this.rentalPaymentTotal || 0;
      let size = 50;
      let maxPage = Math.ceil(total / size);
      let promises = [];
      let self = this;
      _.forEach(_.range(maxPage), function (index) {
        promises.push(
          self.filterRentalPaymentsAction({
            pageSize: size,
            pageNumber: index + 1,
          })
        );
      });

      let responses = await Promise.all(promises);
      let finalData = [];
      _.forEach(responses, function (response) {
        finalData = _.compact(
          _.concat(finalData, _.get(response, `data`) || [])
        );
      });
      this.rentalPaymentExcelData = finalData;
      return finalData;
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
    <v-content :class="helpers.managementStyles().backgroundClass">
      <v-container class="fill-height" fluid>
        <loading></loading>
        <!-- <v-row justify="center" align="center" class="mx-3">
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
                  _.get(rentalPaymentFilterGroup, ["tenant"]) || "N/A"
                }}</v-chip>
              </v-card-subtitle>
              <v-card-subtitle v-show="rentalPaymentFilterGroup.room">
                Room :
                <v-chip class="mx-2">{{
                  _.get(rentalPaymentFilterGroup, ["room"]) || "N/A"
                }}</v-chip>
              </v-card-subtitle>
              <v-card-subtitle
                v-show="
                  rentalPaymentFilterGroup.penalty === 1 ||
                  rentalPaymentFilterGroup.penalty === 0
                "
              >
                Penalty :
                <v-chip class="mx-2">{{
                  rentalPaymentFilterGroup.penalty ? "Yes" : "No"
                }}</v-chip>
              </v-card-subtitle>
              <v-card-subtitle
                v-show="
                  rentalPaymentFilterGroup.paid === 1 ||
                  rentalPaymentFilterGroup.paid === 0
                "
              >
                Paid :
                <v-chip class="mx-2">{{
                  rentalPaymentFilterGroup.paid ? "Yes" : "No"
                }}</v-chip>
              </v-card-subtitle>
            </v-card>
          </v-col>
        </v-row> -->
        <v-row
          justify="center"
          align="center"
          class="ma-3"
          v-if="
            helpers.isAccessible(
              _.get(role, ['name']),
              'rentalPayment',
              'tableView'
            )
          "
        >
          <v-col cols="12">
            <v-card class="pa-8" raised>
              <v-data-table
                :headers="
                  !_.get(rentalPaymentFilterGroup, 'status')
                    ? _.concat(
                        _.filter(headers, (header) => header.text != 'Actions'),
                        [{ text: 'Deleted By' }]
                      )
                    : headers
                "
                :items="data"
                :options.sync="options"
                :server-items-length="rentalPaymentTotal"
                :loading="loading"
                disable-sort
                :footer-props="{
                  'items-per-page-options': [10],
                  'show-current-page': true,
                }"
              >
                <template v-slot:top>
                  <v-toolbar flat color="white">
                    <v-toolbar-title
                      :class="helpers.managementStyles().subtitleClass"
                      >All Rental Payment</v-toolbar-title
                    >
                    <v-spacer></v-spacer>
                    <download-excel
                      :header="`All_RentalPayment_${moment().format(
                        'YYYY_MM_DD'
                      )}`"
                      :name="`All_RentalPayment_${moment().format(
                        'YYYY_MM_DD'
                      )}.csv`"
                      type="csv"
                      :data="rentalPaymentExcelData || []"
                      :fields="rentalPaymentExcelFields || {}"
                      v-if="
                        _.isArray(rentalPaymentExcelData) &&
                        !_.isEmpty(rentalPaymentExcelData)
                      "
                      ><v-btn text color="primary" class="mr-3"
                        >Download as Excel</v-btn
                      ></download-excel
                    >
                    <rental-payment-filter-dialog
                      :buttonStyle="rentalPaymentFilterDialogConfig.buttonStyle"
                      :dialogStyle="rentalPaymentFilterDialogConfig.dialogStyle"
                      @submitFilter="initRentalPaymentFilter($event)"
                    ></rental-payment-filter-dialog>
                  </v-toolbar>
                </template>
                <template v-slot:item="props">
                  <tr>
                    <td class="text-truncate">{{ props.item.sequence }}</td>
                    <td class="text-truncate">{{ props.item.referenceno }}</td>
                    <td class="text-truncate">
                      {{
                        _.get(props.item, "roomcontract.tenant.name") || "N/A"
                      }}
                    </td>
                    <td class="text-truncate">
                      {{ _.get(props.item, "roomcontract.room.unit") || "N/A" }}
                    </td>
                    <td class="text-truncate">
                      {{ _.get(props.item, "roomcontract.startdate") || "N/A" }}
                    </td>
                    <td class="text-truncate">
                      {{ _.get(props.item, "roomcontract.enddate") || "N/A" }}
                    </td>
                    <td class="text-truncate">
                      {{ props.item.rentaldate | formatDate }}
                    </td>
                    <td class="text-truncate">
                      {{ props.item.paymentdate | formatDate }}
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
                      {{
                        parseFloat(props.item.price || 0) +
                        parseFloat(props.item.penalty || 0) +
                        parseFloat(props.item.processing_fees || 0)
                      }}
                    </td>
                    <td class="text-truncate">
                      {{ props.item.payment | toDouble }}
                    </td>
                    <td class="text-truncate">
                      {{ props.item.outstanding | toDouble }}
                    </td>
                    <td class="text-truncate">
                      {{ props.item.remark }}
                    </td>
                    <td class="text-truncate" v-if="props.item.paid">
                      <v-icon small color="success">mdi-check</v-icon>
                    </td>
                    <td class="text-truncate" v-else>
                      <v-icon small color="danger">mdi-close</v-icon>
                    </td>
                    <td class="text-truncate" v-if="props.item.status">
                      <print-rental-payment-button
                        :item="props.item"
                        :roomcontract="props.item.roomcontract"
                        v-if="
                          props.item.paid &&
                          helpers.isAccessible(
                            _.get(role, ['name']),
                            'rentalPayment',
                            'print'
                          )
                        "
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
                          helpers.isAccessible(
                            _.get(role, ['name']),
                            'rentalPayment',
                            'edit'
                          )
                        "
                        >mdi-pencil</v-icon
                      >

                      <confirm-dialog
                        v-if="
                          helpers.isAccessible(
                            _.get(role, ['name']),
                            'rentalPayment',
                            'delete'
                          )
                        "
                        :activatorStyle="
                          deleteRentalButtonConfig.activatorStyle
                        "
                        @confirmed="
                          $event ? deleteRentalPayment(props.item.uid) : null
                        "
                      ></confirm-dialog>
                    </td>
                    <td class="text-truncate">
                      {{ props.item.paymentmethod || "N/A" }}
                    </td>
                    <td class="text-truncate">
                      {{ props.item.receive_from || "N/A" }}
                    </td>
                    <td class="text-truncate">
                      {{ _.get(props.item, "issueby.name") || "N/A" }}
                    </td>
                    <td class="text-truncate" v-if="!props.item.status">
                      {{ _.get(props.item, "deletedby.name") || "N/A" }}
                    </td>
                  </tr>
                </template>
              </v-data-table>
            </v-card>
          </v-col>
        </v-row>

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
      </v-container>
    </v-content>
  </v-app>
</template>
