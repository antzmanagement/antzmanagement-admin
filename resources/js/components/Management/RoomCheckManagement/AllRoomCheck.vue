
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
      roomCheckFilterGroup: new Form({
        rooms: [],
        selectedRooms: [],
        keyword: null,
        fromdate: null,
        todate: null,
      }),
      roomCheckFilterDialogConfig: {
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

      roomCheckFormDialogConfig: {
        buttonStyle: {
          block: true,
          class: "title font-weight-bold ma-2",
          text: "Add RoomCheck",
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
          text: "Check date",
        },
        {
          text: "Category",
        },
      ],
    };
  },
  watch: {
    options: {
      handler() {
        this.getRoomChecks();
      },
      deep: true,
    },
    totalDataLength(v) {
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
      return this.helpers.isEmpty(this.roomCheckFilterGroup.keyword);
    },
    fromdateEmpty() {
      return this.helpers.isEmpty(this.roomCheckFilterGroup.fromdate);
    },
    todateEmpty() {
      return this.helpers.isEmpty(this.roomCheckFilterGroup.todate);
    },
    roomsEmpty() {
      return this.helpers.isEmpty(this.roomCheckFilterGroup.selectedRooms);
    },
  },
  created() {},
  mounted() {
    // this.getRoomChecks();
  },
  methods: {
    ...mapActions({
      getRoomChecksAction: "getRoomChecks",
      filterRoomChecksAction: "filterRoomChecks",
      showLoadingAction: "showLoadingAction",
      endLoadingAction: "endLoadingAction",
    }),
    initRoomCheckFilter(filterGroup) {
      this.roomCheckFilterGroup.reset();
      if (_.get(filterGroup , `room.id`)) {
        this.roomCheckFilterGroup.room_id = filterGroup.room.id;
        this.roomCheckFilterGroup.room = filterGroup.room;
      }
      if (filterGroup.category) {
        this.roomCheckFilterGroup.category = filterGroup.category;
      }
      if (filterGroup.fromdate) {
        this.roomCheckFilterGroup.fromdate = filterGroup.fromdate;
      }
      if (filterGroup.todate) {
        this.roomCheckFilterGroup.todate = filterGroup.todate;
      }
      this.options.page = 1;
      this.getRoomChecks();
    },
    showRoomCheck($data) {
      this.$router.push("/roomcheck/" + $data.uid);
    },
    getRoomChecks() {
      this.loading = true;
      const { sortBy, sortDesc, page, itemsPerPage } = this.options;

      var totalResult = itemsPerPage;
      //Show All Items
      if (totalResult == -1) {
        this.roomCheckFilterGroup.pageNumber = -1;
        this.roomCheckFilterGroup.pageSize = -1;
      } else {
        this.roomCheckFilterGroup.pageNumber = page;
        this.roomCheckFilterGroup.pageSize = itemsPerPage;
      }

      let filterGroup = _.cloneDeep(this.roomCheckFilterGroup);
      delete filterGroup.room;

      this.filterRoomChecksAction(filterGroup)
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
        promises.push(
          self.filterRoomChecksAction({
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
            v-if="
              helpers.isAccessible(_.get(role, ['name']), 'roomCheck', 'create')
            "
          >
            <room-check-form
              :editMode="false"
              :buttonStyle="roomCheckFormDialogConfig.buttonStyle"
              @created="showRoomCheck($event)"
            ></room-check-form>
          </v-col>
        </v-row>

        <v-row
          justify="center"
          align="center"
          class="ma-3"
          v-if="
            helpers.isAccessible(_.get(role, ['name']), 'roomCheck', 'tableView')
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
                disable-sort
              >
                <template v-slot:top>
                  <v-toolbar flat class="mb-5">
                    <room-check-filter-dialog
                      :buttonStyle="roomCheckFilterDialogConfig.buttonStyle"
                      :dialogStyle="roomCheckFilterDialogConfig.dialogStyle"
                      @submitFilter="initRoomCheckFilter($event)"
                    ></room-check-filter-dialog>
                  </v-toolbar>
                  <!-- <v-toolbar
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
                  </v-toolbar> -->
                </template>
                <template v-slot:item="props">
                  <tr @click="helpers.isAccessible(_.get(role, ['name']), 'roomCheck', 'view') ? showRoomCheck(props.item) : null">
                    <td class="text-truncate">
                      {{ _.get(props.item, ["room", "name"]) || "N/A" }}
                    </td>
                    <td class="text-truncate">
                      {{ _.get(props.item, ["checked_date"]) || "N/A" }}
                    </td>
                    <td class="text-truncate">
                      {{ _.get(props.item, ["category"]) || "N/A" }}
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
