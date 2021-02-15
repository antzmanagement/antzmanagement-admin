
<script>
import { mapActions } from "vuex";
export default {
  data() {
    return {
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
        {
          text: "Owner",
        },
      ],
    };
  },
  watch: {
    options: {
      handler() {
        this.getRoomContracts();
      },
      deep: true,
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
      if (filterGroup.fromdate) {
        this.roomContractFilterGroup.fromdate = filterGroup.fromdate;
      }
      if (filterGroup.todate) {
        this.roomContractFilterGroup.todate = filterGroup.todate;
      }
      if (filterGroup.sequence) {
        this.roomContractFilterGroup.sequence = filterGroup.sequence;
      }
      if (filterGroup.checkedout === 1 || filterGroup.checkedout === 0) {
        this.roomContractFilterGroup.checkedout = filterGroup.checkedout;
      }
      if (filterGroup.outstanding_deposit === 1 || filterGroup.outstanding_deposit === 0) {
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
  },
};
</script>

<template>
  <v-app>
    <navbar></navbar>
    <v-content :class="helpers.managementStyles().backgroundClass">
      <v-container class="fill-height" fluid>
        <loading></loading>
        <v-row justify="center" align="center" class="ma-3">
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
              <v-card-subtitle v-show="_.isArray(roomContractFilterGroup.services) && !_.isEmpty(roomContractFilterGroup.services)">
                Services :
                <v-chip
                  v-for="(service, i) in roomContractFilterGroup.services"
                  :key="`service-${i}`"
                  class="mx-2"
                  >{{
                    _.get(service, ["text"]) || "N/A"
                  }}</v-chip
                >
              </v-card-subtitle>
              <v-card-subtitle
                v-show="roomContractFilterGroup.outstanding_deposit === 1 || roomContractFilterGroup.outstanding_deposit === 0 "
              >
                Outstanding Deposit :
                <v-chip class="mx-2">{{
                  roomContractFilterGroup.outstanding_deposit ? "Yes" : "No"
                }}</v-chip>
              </v-card-subtitle>
              <v-card-subtitle v-show="roomContractFilterGroup.checkedout === 1 || roomContractFilterGroup.checkedout === 0">
                Checked out :
                <v-chip class="mx-2">{{
                  roomContractFilterGroup.checkedout ? "Yes" : "No"
                }}</v-chip>
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
                </template>
                <template v-slot:item="props">
                  <tr @click="showRoomContract(props.item)">
                    <td>{{ props.item.sequence }}</td>
                    <td>{{ props.item.contract.name }}</td>
                    <td>{{ props.item.room.name }}</td>
                    <td>
                      {{
                        _.get(props.item, ["room", "owners", 0, "name"]) ||
                        "N/A"
                      }}
                    </td>
                    <td>{{ props.item.tenant.name }}</td>
                    <td>{{ props.item.startdate }}</td>
                    <td>RM {{ props.item.deposit | toDouble }}</td>
                    <td>RM {{ props.item.outstanding_deposit | toDouble }}</td>
                    <td>RM {{ props.item.rental | toDouble }}</td>
                    <td>
                      {{ props.item.duration }}
                      {{ props.item.rental_type || "day" }}
                    </td>
                    <td>
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
                    <td class="text-center" v-if="props.item.checkedout">
                      <v-icon small color="success">mdi-check</v-icon>
                    </td>
                    <td class="text-center" v-else>
                      <v-icon small color="danger">mdi-close</v-icon>
                    </td>
                    <td>
                      {{ props.item.checkout_date | formatDate }}
                    </td>
                    <td>
                      {{
                        _.get(props.item, ["room", "owners", 0, "name"]) ||
                        "N/A"
                      }}
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
