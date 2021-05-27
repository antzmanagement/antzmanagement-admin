


<script>
import { mapActions } from "vuex";

export default {
  data() {
    return {
      role: "",
      totalDataLength: 0,
      data: [],
      loading: true,
      options: {},
      staffFilterGroup: new Form({
        roomTypes: [],
        selectedRoomTypes: [],
        keyword: null,
        fromdate: null,
        todate: null,
      }),
      staffFormDialogConfig: {
        buttonStyle: {
          block: true,
          class: "title font-weight-bold ma-2",
          text: "Add Staff",
          icon: "mdi-plus",
          isIcon: false,
          color: "primary",
          evalation: "5",
        },
      },
      staffFilterDialogConfig: {
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
      headers: [
        {
          text: "Id",
          align: "start",
          value: "id",
        },
        {
          text: "Name",
          align: "start",
          value: "name",
        },
        { text: "Ic No", value: "icno" },
        { text: "Phone", value: "tel1" },
        { text: "Email", value: "email" },
      ],
    };
  },
  watch: {
    options: {
      handler() {
        this.getStaffs();
      },
      deep: true,
    },
    role: function (val) {
      console.log("role", val);
    },
  },
  computed: {
    isLoading() {
      return this.$store.getters.isLoading;
    },
    keywordEmpty() {
      return this.helpers.isEmpty(this.staffFilterGroup.keyword);
    },
    fromdateEmpty() {
      return this.helpers.isEmpty(this.staffFilterGroup.fromdate);
    },
    todateEmpty() {
      return this.helpers.isEmpty(this.staffFilterGroup.todate);
    },
    roomTypesEmpty() {
      return this.helpers.isEmpty(this.staffFilterGroup.selectedRoomTypes);
    },
  },
  created() {
    var styles = this.helpers.managementStyles();
  },
  mounted() {
    this.getStaffs();
    console.log(this.helpers);
  },
  methods: {
    ...mapActions({
      getStaffsAction: "getStaffs",
      filterStaffsAction: "filterStaffs",
      showLoadingAction: "showLoadingAction",
      endLoadingAction: "endLoadingAction",
    }),
    initStaffFilter(filterGroup) {
      console.log(filterGroup);
      this.staffFilterGroup.reset();
      if (filterGroup) {
        this.staffFilterGroup.keyword = filterGroup.keyword;
      }
      this.getStaffs();
      this.options.page = 1;
    },
    showStaff($data) {
      this.$router.push("/staff/" + $data.uid);
    },
    getStaffs() {
      this.loading = true;
      const { sortBy, sortDesc, page, itemsPerPage } = this.options;

      var totalResult = itemsPerPage;
      //Show All Items
      if (totalResult == -1) {
        this.staffFilterGroup.pageNumber = -1;
        this.staffFilterGroup.pageSize = -1;
      } else {
        this.staffFilterGroup.pageNumber = page;
        this.staffFilterGroup.pageSize = itemsPerPage;
      }

      this.filterStaffsAction(this.staffFilterGroup)
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
          v-if="helpers.isAccessible(_.get(role, ['name']), 'staff', 'create')"
        >
          <v-col cols="12">
            <staff-form
              :editMode="false"
              :buttonStyle="staffFormDialogConfig.buttonStyle"
              @created="showStaff($event)"
            ></staff-form>
          </v-col>
        </v-row>

        <v-row justify="center" align="center" class="mx-3">
          <v-col cols="12">
            <v-card raised>
              <v-card-subtitle v-show="!keywordEmpty">
                Keyword :
                <v-chip class="mx-2">{{ staffFilterGroup.keyword }}</v-chip>
              </v-card-subtitle>
              <v-card-subtitle v-show="!fromdateEmpty">
                From Date :
                <v-chip class="mx-2">{{ staffFilterGroup.keyword }}</v-chip>
              </v-card-subtitle>
              <v-card-subtitle v-show="!todateEmpty">
                To Date :
                <v-chip class="mx-2">{{ staffFilterGroup.todate }}</v-chip>
              </v-card-subtitle>

              <v-card-subtitle v-show="!roomTypesEmpty">
                Room Types :
                <v-chip
                  class="mx-2"
                  v-for="roomType in staffFilterGroup.selectedRoomTypes"
                  :key="roomType.id"
                  >{{ roomType.name | capitalizeFirstLetter }}</v-chip
                >
              </v-card-subtitle>
            </v-card>
          </v-col>
        </v-row>
        <v-row
          justify="center"
          align="center"
          class="ma-3"
          v-if="helpers.isAccessible(_.get(role, ['name']), 'staff', 'read')"
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
                    <staff-filter-dialog
                      :buttonStyle="staffFilterDialogConfig.buttonStyle"
                      :dialogStyle="staffFilterDialogConfig.dialogStyle"
                      @submitFilter="initStaffFilter($event)"
                    ></staff-filter-dialog>
                  </v-toolbar>
                </template>
                <template v-slot:item="props">
                  <tr @click="showStaff(props.item)">
                    <td class="text-truncate">{{ props.item.id }}</td>
                    <td class="text-truncate">{{ props.item.name }}</td>
                    <td class="text-truncate">{{ props.item.icno }}</td>
                    <td class="text-truncate">{{ props.item.tel1 }}</td>
                    <td class="text-truncate">{{ props.item.email }}</td>
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