
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
      roomFilterGroup: new Form({
        roomTypes: [],
        selectedRoomTypes: [],
        keyword: null,
        fromdate: null,
        todate: null,
      }),
      roomFilterDialogConfig: {
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

      roomFormDialogConfig: {
        buttonStyle: {
          block: true,
          class: "title font-weight-bold ma-2",
          text: "Add Room",
          icon: "mdi-plus",
          isIcon: false,
          color: "primary",
          evalation: "5",
        },
      },
      headers: [
        {
          text: "Id",
        },
        {
          text: "Room Types",
        },
        {
          text: "Unit No",
        },
        {
          text: "Jalan",
        },
        {
          text: "Lot",
        },
        {
          text: "Floor",
        },
        { text: "Rental (RM)" },
        {
          text: "Room Status",
        },
        {
          text: "TNB Account No",
        },
        // {
        //   text: "Owner",
        // },
      ],
      excelData: [],
      excelFields: {
        id: "id",
        uid: "uid",
        unit: "unit",
        floor: "floor",
        lot: "lot",
        jalan: "jalan",
        price: "price",
        size: "size",
        room_status: "room_status",
        address: "address",
        postcode: "postcode",
        state: "state",
        city: "city",
        isSublet: {
          field: "sublet",
          callback: (value) => (value ? "Yes" : "No"),
        },
        sublet_claim: "sublet_claim",
        owner_claim: "owner_claim",
        tnb_account_no: "tnb_account_no",
        room_type: {
          field: "room_types",
          callback: (value) => {
            return _.get(value, `[0].name`) || "N/A";
          },
        },
        room_type_id: {
          field: "room_types",
          callback: (value) => {
            return _.get(value, `[0].id`) || "N/A";
          },
        },
        owner: {
          field: "owners",
          callback: (value) => {
            return _.get(value, `[0].name`) || "N/A";
          },
        },
        owner_id: {
          field: "owners",
          callback: (value) => {
            return _.get(value, `[0].id`) || "N/A";
          },
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
        this.getRooms();
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
      return this.helpers.isEmpty(this.roomFilterGroup.keyword);
    },
    fromdateEmpty() {
      return this.helpers.isEmpty(this.roomFilterGroup.fromdate);
    },
    todateEmpty() {
      return this.helpers.isEmpty(this.roomFilterGroup.todate);
    },
    roomTypesEmpty() {
      return this.helpers.isEmpty(this.roomFilterGroup.selectedRoomTypes);
    },
  },
  created() {},
  mounted() {
    this.getRooms();
  },
  methods: {
    ...mapActions({
      getRoomsAction: "getRooms",
      filterRoomsAction: "filterRooms",
      showLoadingAction: "showLoadingAction",
      endLoadingAction: "endLoadingAction",
    }),
    initRoomFilter(filterGroup) {
      this.roomFilterGroup.reset();
      if (filterGroup.unit) {
        this.roomFilterGroup.unit = filterGroup.unit;
      }
      if (filterGroup.jalan) {
        this.roomFilterGroup.jalan = filterGroup.jalan;
      }
      if (filterGroup.floor) {
        this.roomFilterGroup.floor = filterGroup.floor;
      }
      if (filterGroup.block) {
        this.roomFilterGroup.block = filterGroup.block;
      }
      if (filterGroup.room_status) {
        this.roomFilterGroup.room_status = filterGroup.room_status;
      }
      if (filterGroup.roomType) {
        this.roomFilterGroup.room_type_id = filterGroup.roomType.id;
        this.roomFilterGroup.roomTypeObj = filterGroup.roomType;
      }
      if (filterGroup.owner) {
        this.roomFilterGroup.owner_id = filterGroup.owner.id;
        this.roomFilterGroup.ownerObj = filterGroup.owner;
      }
      console.log(this.roomFilterGroup);
      this.options.page = 1;
      this.getRooms();
    },
    showRoom($data) {
      this.$router.push("/room/" + $data.uid);
    },
    getRooms() {
      this.loading = true;
      const { sortBy, sortDesc, page, itemsPerPage } = this.options;

      var totalResult = itemsPerPage;
      //Show All Items
      if (totalResult == -1) {
        this.roomFilterGroup.pageNumber = -1;
        this.roomFilterGroup.pageSize = -1;
      } else {
        this.roomFilterGroup.pageNumber = page;
        this.roomFilterGroup.pageSize = itemsPerPage;
      }

      this.filterRoomsAction(this.roomFilterGroup)
        .then((data) => {
          console.log("data");
          console.log(data);
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
          self.filterRoomsAction({
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
        <v-row justify="center" align="center" class="ma-3">
          <v-col
            cols="12"
            v-if="helpers.isAccessible(_.get(role, ['name']), 'room', 'create')"
          >
            <room-form
              :editMode="false"
              :buttonStyle="roomFormDialogConfig.buttonStyle"
              @created="showRoom($event)"
            ></room-form>
          </v-col>
        </v-row>

        <v-row justify="center" align="center" class="mx-3">
          <v-col cols="12">
            <v-card raised>
              <v-card-subtitle v-show="roomFilterGroup.unit">
                Unit :
                <v-chip class="mx-2">{{ roomFilterGroup.unit }}</v-chip>
              </v-card-subtitle>
              <v-card-subtitle v-show="roomFilterGroup.jalan">
                Jalan :
                <v-chip class="mx-2">{{ roomFilterGroup.jalan }}</v-chip>
              </v-card-subtitle>
              <v-card-subtitle v-show="roomFilterGroup.block">
                Block :
                <v-chip class="mx-2">{{ roomFilterGroup.block }}</v-chip>
              </v-card-subtitle>
              <v-card-subtitle v-show="roomFilterGroup.floor">
                Floor :
                <v-chip class="mx-2">{{ roomFilterGroup.floor }}</v-chip>
              </v-card-subtitle>
              <v-card-subtitle v-show="roomFilterGroup.room_status">
                Room Status :
                <v-chip class="mx-2">{{ roomFilterGroup.room_status }}</v-chip>
              </v-card-subtitle>
              <v-card-subtitle v-show="roomFilterGroup.roomTypeObj">
                Room Type :
                <v-chip class="mx-2">{{
                  _.get(roomFilterGroup, ["roomTypeObj", "name"]) || "N/A"
                }}</v-chip>
              </v-card-subtitle>
              <v-card-subtitle v-show="roomFilterGroup.ownerObj">
                Owner :
                <v-chip class="mx-2">{{
                  _.get(roomFilterGroup, ["ownerObj", "name"]) || "N/A"
                }}</v-chip>
              </v-card-subtitle>
            </v-card>
          </v-col>
        </v-row>
        <v-row
          justify="center"
          align="center"
          class="ma-3"
          v-if="helpers.isAccessible(_.get(role, ['name']), 'service', 'read')"
        >
          <v-col cols="12">
            <v-card class="pa-8" raised>
              <v-data-table
                :headers="headers"
                :items="data"
                :options.sync="options"
                :server-items-length="totalDataLength"
                :loading="loading"
                disable-sort
              >
                <template v-slot:top>
                  <v-toolbar flat class="mb-5">
                    <room-filter-dialog
                      :buttonStyle="roomFilterDialogConfig.buttonStyle"
                      :dialogStyle="roomFilterDialogConfig.dialogStyle"
                      @submitFilter="initRoomFilter($event)"
                    ></room-filter-dialog>
                  </v-toolbar>
                  <v-toolbar
                    flat
                    class="mb-5 justify-end d-flex"
                    v-if="_.isArray(excelData) && !_.isEmpty(excelData)"
                  >
                    <download-excel
                      :header="`All_Room_${moment().format('YYYY_MM_DD')}`"
                      :name="`All_Room_${moment().format('YYYY_MM_DD')}.csv`"
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
                  <tr @click="showRoom(props.item)">
                    <td>{{ props.item.id }}</td>
                    <td>{{ props.item.room_types[0].name }}</td>
                    <td>{{ props.item.unit }}</td>
                    <td>{{ props.item.jalan }}</td>
                    <td>{{ props.item.lot }}</td>
                    <td>{{ props.item.floor }}</td>
                    <td>{{ props.item.price | toDouble }}</td>
                    <td>{{ props.item.room_status }}</td>
                    <td>{{ props.item.tnb_account_no }}</td>
                    <!-- <td>
                      {{ _.get(props.item, ["owners", 0, "name"]) || "N/A" }}
                    </td> -->
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
