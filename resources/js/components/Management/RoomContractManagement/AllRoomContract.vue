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
              <v-card-subtitle v-show="!keywordEmpty">
                Keyword :
                <v-chip class="mx-2">{{ roomContractFilterGroup.keyword }}</v-chip>
              </v-card-subtitle>
              <v-card-subtitle v-show="!fromdateEmpty">
                From Date :
                <v-chip class="mx-2">{{ roomContractFilterGroup.keyword }}</v-chip>
              </v-card-subtitle>
              <v-card-subtitle v-show="!todateEmpty">
                To Date :
                <v-chip class="mx-2">{{ roomContractFilterGroup.todate }}</v-chip>
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
                    <room-contract-filter-dialog
                      :buttonStyle="roomContractFilterDialogConfig.buttonStyle"
                      :dialogStyle="roomContractFilterDialogConfig.dialogStyle"
                      @submitFilter="initRoomContractFilter($event)"
                    ></room-contract-filter-dialog>
                  </v-toolbar>
                </template>
                <template v-slot:item="props">
                  <tr @click="showRoomContract(props.item)">
                    <td>{{props.item.uid}}</td>
                    <td>{{props.item.room.name}}</td>
                    <td>{{props.item.tenant.name}}</td>
                    <td>{{props.item.startdate}}</td>
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
      roomContractFilterGroup: new Form({
        rooms: [],
        selectedRooms: [],
        keyword: null,
        fromdate: null,
        todate: null
      }),
      roomContractFilterDialogConfig: {
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
      
      roomContractFormDialogConfig: {
        buttonStyle: {
          block: true,
          class: "title font-weight-bold ma-2",
          text: "Add RoomContract",
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
        {
          text: "Room",
        },
        {
          text: "Tenant",
        },
        {
          text: "Start date",
        },
      ]
    };
  },
  watch: {
    options: {
      handler() {
        this.getRoomContracts();
      },
      deep: true
    }
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
    }
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
      endLoadingAction: "endLoadingAction"
    }),
    initRoomContractFilter(filterGroup) {
      this.roomContractFilterGroup.reset();
      if (filterGroup) {
        this.roomContractFilterGroup.selectedRooms = filterGroup.rooms;
        this.roomContractFilterGroup.rooms = filterGroup.rooms.map(function(
          roomContractType
        ) {
          return roomContractType.id;
        });
        this.roomContractFilterGroup.keyword = filterGroup.keyword;
      }
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
        .then(data => {
          if (data.data) {
            this.data = data.data;
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