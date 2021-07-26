
<script>
import { mapActions } from "vuex";
import { _ } from "../../../common/common-function";
import moment from "moment";
export default {
  data() {
    return {
      moment: moment,
      _: _,
      totalDataLength: 0,
      data: [],
      loading: true,
      options: {},
      roomContractFilterGroup: new Form({
        rooms: [],
        selectedRooms: [],
        keyword: null,
        fromdate: null,
        todate: null,
      }),
      roomContractFilterDialogConfig: {
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

      roomContractFormDialogConfig: {
        buttonStyle: {
          block: true,
          class: "title font-weight-bold ma-2",
          text: "Add RoomContract",
          icon: "mdi-plus",
          isIcon: false,
          color: "primary",
          evalation: "5",
        },
      },
      headers: [
        {
          text: "Room",
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
          text: "Deposit (RM)",
        },
        {
          text: "Rental (RM)",
        },
        {
          text: "Duration",
        },
        {
          text: "Services",
        },
        {
          text: "Check Out Date",
        },
      ],
      excelData: [],
      excelFields: {
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
        outstanding_deposit: "outstanding_deposit",
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
  watch: {
    options: {
      handler() {
        this.getRoomContracts();
      },
      deep: true,
    },
    totalDataLength(v) {
      console.log(v);
      if (v > 0) {
        this.fetchExcelData();
      }
    },
  },
  computed: {
    isLoading() {
      return this.$store.getters.isLoading;
    },
    keywordEmpty() {
      return this.helpers.isEmpty(this.roomContractFilterGroup.keyword);
    },
    fromdateEmpty() {
      return this.helpers.isEmpty(this.roomContractFilterGroup.fromdate);
    },
    todateEmpty() {
      return this.helpers.isEmpty(this.roomContractFilterGroup.todate);
    },
    roomsEmpty() {
      return this.helpers.isEmpty(this.roomContractFilterGroup.selectedRooms);
    },
  },
  created() {},
  mounted() {
    this.getRoomContracts();
  },
  methods: {
    ...mapActions({
      getRoomContractsAction: "getRoomContracts",
      filterRoomContractsAction: "filterRoomContracts",
      showLoadingAction: "showLoadingAction",
      endLoadingAction: "endLoadingAction",
    }),
    initRoomContractFilter(filterGroup) {
      this.roomContractFilterGroup.reset();
      if (filterGroup.tenant) {
        this.roomContractFilterGroup.tenant_id = filterGroup.tenant.id;
        this.roomContractFilterGroup.tenant = filterGroup.tenant;
      }
      if (filterGroup.owner) {
        this.roomContractFilterGroup.owner_id = filterGroup.owner.id;
        this.roomContractFilterGroup.owner = filterGroup.owner;
      }
      if (filterGroup.room) {
        this.roomContractFilterGroup.room_id = filterGroup.room.id;
        this.roomContractFilterGroup.room = filterGroup.room;
      }
      if (filterGroup.services) {
        this.roomContractFilterGroup.service_ids =
          _.map(filterGroup.services, "id") || [];
        this.roomContractFilterGroup.services = filterGroup.services;
      }
      if (filterGroup.startDateFromDate) {
        this.roomContractFilterGroup.startDateFromDate = filterGroup.startDateFromDate;
      }
      if (filterGroup.startDateToDate) {
        this.roomContractFilterGroup.startDateToDate = filterGroup.startDateToDate;
      }
      if (filterGroup.endDateFromDate) {
        this.roomContractFilterGroup.endDateFromDate = filterGroup.endDateFromDate;
      }
      if (filterGroup.endDateToDate) {
        this.roomContractFilterGroup.endDateToDate = filterGroup.endDateToDate;
      }
      if (filterGroup.sequence) {
        this.roomContractFilterGroup.sequence = filterGroup.sequence;
      }
      if (filterGroup.checkedout === 1 || filterGroup.checkedout === 0) {
        this.roomContractFilterGroup.checkedout = filterGroup.checkedout;
      }
      if (
        filterGroup.outstanding_deposit === 1 ||
        filterGroup.outstanding_deposit === 0
      ) {
        this.roomContractFilterGroup.outstanding_deposit =
          filterGroup.outstanding_deposit;
      }
      console.log(this.roomContractFilterGroup);
      this.options.page = 1;
      this.getRoomContracts();
    },
    showRoomContract($data) {
      this.$router.push("/roomcontract/" + $data.uid);
    },
    getRoomContracts() {
      this.loading = true;
      const { sortBy, sortDesc, page, itemsPerPage } = this.options;

      var totalResult = itemsPerPage;
      //Show All Items
      if (totalResult == -1) {
        this.roomContractFilterGroup.pageNumber = -1;
        this.roomContractFilterGroup.pageSize = -1;
      } else {
        this.roomContractFilterGroup.pageNumber = page;
        this.roomContractFilterGroup.pageSize = itemsPerPage;
      }

      this.filterRoomContractsAction(this.roomContractFilterGroup)
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
    async fetchExcelData() {
      let total = this.totalDataLength || 0;
      let size = 50;
      let maxPage = Math.ceil(total / size);
      let promises = [];
      let self = this;
      _.forEach(_.range(maxPage), function (index) {
        console.log(index);
        promises.push(
          self.filterRoomContractsAction({
            pageSize: size,
            pageNumber: index + 1,
          })
        );
      });

      let responses = await Promise.all(promises);
      console.log(responses);
      let finalData = [];
      _.forEach(responses, function (response) {
        finalData = _.compact(
          _.concat(finalData, _.get(response, `data`) || [])
        );
      });
      console.log(finalData);
      this.excelData = finalData;
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
              'roomContract',
              'create'
            )
          "
        >
          <v-col cols="12">
            <room-contract-form
              :editMode="false"
              :buttonStyle="roomContractFormDialogConfig.buttonStyle"
              @created="showRoomContract($event)"
            ></room-contract-form>
          </v-col>
        </v-row>

        <v-row justify="center" align="center" class="mx-3">
          <v-col cols="12">
            <v-card raised>
              <v-card-subtitle v-show="roomContractFilterGroup.fromdate">
                From Date :
                <v-chip class="mx-2">{{
                  roomContractFilterGroup.fromdate
                }}</v-chip>
              </v-card-subtitle>
              <v-card-subtitle v-show="roomContractFilterGroup.todate">
                To Date :
                <v-chip class="mx-2">{{
                  roomContractFilterGroup.todate
                }}</v-chip>
              </v-card-subtitle>
              <v-card-subtitle v-show="roomContractFilterGroup.sequence">
                Sequence No :
                <v-chip class="mx-2">{{
                  roomContractFilterGroup.sequence
                }}</v-chip>
              </v-card-subtitle>
              <v-card-subtitle v-show="roomContractFilterGroup.tenant">
                Tenant :
                <v-chip class="mx-2">{{
                  _.get(roomContractFilterGroup, ["tenant", "name"]) || "N/A"
                }}</v-chip>
              </v-card-subtitle>
              <v-card-subtitle v-show="roomContractFilterGroup.owner">
                Owner :
                <v-chip class="mx-2">{{
                  _.get(roomContractFilterGroup, ["owner", "name"]) || "N/A"
                }}</v-chip>
              </v-card-subtitle>
              <v-card-subtitle v-show="roomContractFilterGroup.room">
                Room :
                <v-chip class="mx-2">{{
                  _.get(roomContractFilterGroup, ["room", "unit"]) || "N/A"
                }}</v-chip>
              </v-card-subtitle>
              <v-card-subtitle
                v-show="
                  _.isArray(roomContractFilterGroup.services) &&
                  !_.isEmpty(roomContractFilterGroup.services)
                "
              >
                Services :
                <v-chip
                  v-for="(service, i) in roomContractFilterGroup.services"
                  :key="`service-${i}`"
                  class="mx-2"
                  >{{ _.get(service, ["text"]) || "N/A" }}</v-chip
                >
              </v-card-subtitle>
              <v-card-subtitle
                v-show="
                  roomContractFilterGroup.outstanding_deposit === 1 ||
                  roomContractFilterGroup.outstanding_deposit === 0
                "
              >
                Outstanding Deposit :
                <v-chip class="mx-2">{{
                  roomContractFilterGroup.outstanding_deposit ? "Yes" : "No"
                }}</v-chip>
              </v-card-subtitle>
              <v-card-subtitle
                v-show="
                  roomContractFilterGroup.checkedout === 1 ||
                  roomContractFilterGroup.checkedout === 0
                "
              >
                Checked out :
                <v-chip class="mx-2">{{
                  roomContractFilterGroup.checkedout ? "Yes" : "No"
                }}</v-chip>
              </v-card-subtitle>
            </v-card>
          </v-col>
        </v-row>
        <v-row
          justify="center"
          align="center"
          class="ma-3"
          v-if="
            helpers.isAccessible(_.get(role, ['name']), 'roomContract', 'read')
          "
        >
          <v-col cols="12">
            <v-card class="pa-8" raised>
              <v-data-table
                :headers="headers"
                :items="data"
                :options.sync="options"
                :server-items-length="totalDataLength"
                :loading="loading"
                calculate-widths
                disable-sort
                :footer-props="{
                  'items-per-page-options': [10],
                  'show-current-page': true,
                }"
              >
                <template v-slot:top>
                  <v-toolbar flat class="mb-5">
                    <room-contract-filter-dialog
                      :buttonStyle="roomContractFilterDialogConfig.buttonStyle"
                      :dialogStyle="roomContractFilterDialogConfig.dialogStyle"
                      @submitFilter="initRoomContractFilter($event)"
                    ></room-contract-filter-dialog>
                  </v-toolbar>
                  <v-toolbar
                    flat
                    class="mb-5 justify-end d-flex"
                    v-if="_.isArray(excelData) && !_.isEmpty(excelData)"
                  >
                    <download-excel
                      :header="`All_RoomContract_${moment().format(
                        'YYYY_MM_DD'
                      )}`"
                      :name="`All_RoomContract_${moment().format(
                        'YYYY_MM_DD'
                      )}.csv`"
                      type="csv"
                      :fields="excelFields || {}"
                      :data="excelData || []"
                      ><v-btn text color="primary"
                        >Download as Excel</v-btn
                      ></download-excel
                    >
                  </v-toolbar>
                </template>
                <template v-slot:item="props">
                  <tr @click="showRoomContract(props.item)">
                    <td class="text-truncate">
                      {{ _.get(props.item, ["room", "name"]) || "N/A" }}
                    </td>
                    <td class="text-truncate">
                      {{ _.get(props.item, ["tenant", "name"]) || "N/A" }}
                    </td>
                    <td class="text-truncate">
                      {{ props.item.startdate | formatDate }}
                    </td>
                    <td class="text-truncate">
                      {{ props.item.enddate | formatDate }}
                    </td>
                    <td class="text-truncate">
                      {{ props.item.deposit | toDouble }}
                    </td>
                    <td class="text-truncate">
                      {{ props.item.rental | toDouble }}
                    </td>
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
                    <td class="text-truncate">
                      {{ props.item.checkout_date | formatDate }}
                    </td>
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
