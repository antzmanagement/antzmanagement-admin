
<script>
import { mapActions } from "vuex";
import { notEmptyLength, _ } from "../../../common/common-function";
export default {
  data() {
    return {
      totalDataLength: 0,
      data: [],
      loading: true,
      options: {},
      roomTypeFilterGroup: new Form({
        keyword: null,
        fromdate: null,
        todate: null,
      }),
      roomTypeFilterDialogConfig: {
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

      roomTypeFormDialogConfig: {
        buttonStyle: {
          block: true,
          class: "title font-weight-bold ma-2",
          text: "Add Room Type",
          icon: "mdi-plus",
          isIcon: false,
          color: "primary",
          evalation: "5",
        },
      },
      headers: [
        {
          text: "Name",
        },
        {
          text: "Price",
        },
      ],
    };
  },
  watch: {
    options: {
      handler() {
        this.getRoomTypes();
      },
      deep: true,
    },
  },
  computed: {
    isLoading() {
      return this.$store.getters.isLoading;
    },
    keywordEmpty() {
      return this.helpers.isEmpty(this.roomTypeFilterGroup.keyword);
    },
    fromdateEmpty() {
      return this.helpers.isEmpty(this.roomTypeFilterGroup.fromdate);
    },
    todateEmpty() {
      return this.helpers.isEmpty(this.roomTypeFilterGroup.todate);
    },
    roomsEmpty() {
      return this.helpers.isEmpty(this.roomTypeFilterGroup.selectedRooms);
    },
  },
  created() {
    document.title = 'All Room Type'
  },
  mounted() {
    this.getRoomTypes();
  },
  methods: {
    ...mapActions({
      getRoomTypesAction: "getRoomTypes",
      filterRoomTypesAction: "filterRoomTypes",
      showLoadingAction: "showLoadingAction",
      endLoadingAction: "endLoadingAction",
    }),
    initRoomTypeFilter(filterGroup) {
      this.roomTypeFilterGroup.reset();
      if (filterGroup) {
        this.roomTypeFilterGroup.keyword = filterGroup.keyword;
      }
      this.options.page = 1;
      this.getRoomTypes();
    },
    showRoomType($data) {
      let routeData = this.$router.resolve({
        name: "roomtype",
        params: { uid: $data.uid },
      });
      window.open(routeData.href, "_blank");
      // this.$router.push("/roomtype/" + $data.uid);
    },
    getRoomTypes() {
      this.loading = true;
      const { sortBy, sortDesc, page, itemsPerPage } = this.options;
      _.isEmpty({});
      var totalResult = itemsPerPage;
      //Show All Items
      if (totalResult == -1) {
        this.roomTypeFilterGroup.pageNumber = -1;
        this.roomTypeFilterGroup.pageSize = -1;
      } else {
        this.roomTypeFilterGroup.pageNumber = page;
        this.roomTypeFilterGroup.pageSize = itemsPerPage;
      }

      this.filterRoomTypesAction(this.roomTypeFilterGroup)
        .then((data) => {
          if (notEmptyLength(data.data)) {
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
            helpers.isAccessible(_.get(role, ['name']), 'roomType', 'create')
          "
        >
          <v-col cols="12">
            <room-type-form :editMode="false" @created="showRoomType($event)">
              <v-btn block color="primary" class="ma-2"
                ><v-icon left>mdi-plus</v-icon> Add Room Type</v-btn
              >
            </room-type-form>
          </v-col>
        </v-row>

        <v-row justify="center" align="center" class="mx-3">
          <v-col cols="12">
            <v-card raised>
              <v-card-subtitle v-show="!keywordEmpty">
                Keyword :
                <v-chip class="mx-2">{{ roomTypeFilterGroup.keyword }}</v-chip>
              </v-card-subtitle>
              <v-card-subtitle v-show="!fromdateEmpty">
                From Date :
                <v-chip class="mx-2">{{ roomTypeFilterGroup.keyword }}</v-chip>
              </v-card-subtitle>
              <v-card-subtitle v-show="!todateEmpty">
                To Date :
                <v-chip class="mx-2">{{ roomTypeFilterGroup.todate }}</v-chip>
              </v-card-subtitle>
            </v-card>
          </v-col>
        </v-row>
        <v-row
          justify="center"
          align="center"
          class="ma-3"
          v-if="helpers.isAccessible(_.get(role, ['name']), 'roomType', 'tableView')"
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
                <!-- <template v-slot:top>
                  <v-toolbar flat class="mb-5">
                    <room-type-filter-dialog
                      :buttonStyle="roomTypeFilterDialogConfig.buttonStyle"
                      :dialogStyle="roomTypeFilterDialogConfig.dialogStyle"
                      @submitFilter="initRoomTypeFilter($event)"
                    ></room-type-filter-dialog>
                  </v-toolbar>
                </template> -->
                <template v-slot:item="props">
                  <tr @click="helpers.isAccessible(_.get(role, ['name']), 'roomType', 'view') ? showRoomType(props.item) : null">
                    <td class="text-truncate">{{ props.item.name }}</td>
                    <td class="text-truncate">{{ props.item.price }}</td>
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
