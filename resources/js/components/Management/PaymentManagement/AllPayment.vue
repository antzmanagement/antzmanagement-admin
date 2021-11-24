
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
      paymentTotal: 0,
      data: [],
      paymentData: [],
      loading: true,
      paymentLoading: true,
      options: {},
      paymentOptions: {},
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
    };
  },
  watch: {
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
        this.getPayments();
      },
      deep: true,
    },
    paymentTotal(v) {
      if (v > 0) {
        // this.fetchPaymentExcelData();
      }
    },
  },
  computed: {
    isLoading() {
      return this.$store.getters.isLoading;
    },
  },
  created() {
    document.title = "All Payment";
  },
  mounted() {
  },
  methods: {
    ...mapActions({
      filterPaymentsAction: "filterPayments",
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
    getPayments() {
      this.paymentLoading = true;
      const { sortBy, sortDesc, page, itemsPerPage } = this.paymentOptions;

      var totalResult = itemsPerPage;

      let filterGroup = { ...this.paymentFilterGroup };
      //Show All Items
      filterGroup.pageNumber = page;
      filterGroup.pageSize = itemsPerPage;

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
          <v-col
            cols="12"
            :class="helpers.managementStyles().centerWrapperClass"
          >
            <v-card raised width="100%" class="pa-8">
              <v-data-table
                :headers="
                  !_.get(paymentFilterGroup, 'status')
                    ? _.concat(
                        _.filter(
                          paymentHeaders,
                          (paymentHeader) => paymentHeader.text != 'Actions'
                        ),
                        [{ text: 'Deleted By' }]
                      )
                    : paymentHeaders
                "
                :items="paymentData"
                :options.sync="paymentOptions"
                :server-items-length="paymentTotal"
                :loading="paymentLoading"
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
                      >Others Payment</v-toolbar-title
                    >
                    <v-spacer></v-spacer>
                    <download-excel
                      :header="`All_AddOnPayment_${moment().format(
                        'YYYY_MM_DD'
                      )}`"
                      :name="`All_AddOnPayment_${moment().format(
                        'YYYY_MM_DD'
                      )}.csv`"
                      type="csv"
                      :data="paymentExcelData || []"
                      :fields="paymentExcelFields || {}"
                      v-if="
                        _.isArray(paymentExcelData) &&
                        !_.isEmpty(paymentExcelData)
                      "
                      ><v-btn text color="primary" class="mr-3"
                        >Download as Excel</v-btn
                      ></download-excel
                    >
                    <payment-filter-dialog
                      :buttonStyle="rentalPaymentFilterDialogConfig.buttonStyle"
                      :dialogStyle="rentalPaymentFilterDialogConfig.dialogStyle"
                      @submitFilter="initPaymentFilter($event)"
                    ></payment-filter-dialog>
                  </v-toolbar>
                </template>
                <template v-slot:item="props">
                  <tr>
                    <td class="text-truncate">{{ props.item.receiptno }}</td>
                    <td class="text-truncate">{{ props.item.referenceno }}</td>
                    <td class="text-truncate">
                      {{
                        _.get(props.item, "roomcontract.tenant.name") || "N/A"
                      }}
                    </td>
                    <td class="text-truncate">
                      {{ _.get(props.item, "roomcontract.room.name") || "N/A" }}
                    </td>
                    <td class="text-truncate">
                      {{
                        _.get(props.item, "roomcontract.startdate") ||
                        "N/A" | formatDate
                      }}
                    </td>
                    <td class="text-truncate">
                      {{
                        _.get(props.item, "roomcontract.enddate") ||
                        "N/A" | formatDate
                      }}
                    </td>
                    <td class="text-truncate">
                      {{ props.item.paymentdate | formatDate }}
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
                    <td class="text-truncate">
                      {{ props.item.price | toDouble }}
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
                      {{ props.item.other_charges | toDouble }}
                    </td>
                    <td class="text-truncate">
                      {{ props.item.processing_fees | toDouble }}
                    </td>
                    <td class="text-truncate">
                      {{
                        parseFloat(props.item.price || 0) +
                        parseFloat(props.item.other_charges || 0) +
                        parseFloat(props.item.processing_fees || 0)
                      }}
                    </td>
                    <td class="text-truncate">
                      {{ props.item.totalpayment | toDouble }}
                    </td>
                    <td class="text-truncate">
                      {{ props.item.outstanding | toDouble }}
                    </td>
                    <td class="text-truncate" v-if="props.item.paid">
                      <v-icon small color="success">mdi-check</v-icon>
                    </td>
                    <td class="text-truncate" v-else>
                      <v-icon small color="danger">mdi-close</v-icon>
                    </td>
                    <td class="text-truncate">{{ props.item.remark }}</td>
                    <td class="text-truncate" v-if="props.item.status">
                      <print-payment-button
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
                      </print-payment-button>
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
                        @click="openAddOnPaymentDialog(props.item.uid, true)"
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
      </v-container>
    </v-content>
  </v-app>
</template>
