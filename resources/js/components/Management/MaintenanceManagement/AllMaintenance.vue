
<script>
import { mapActions } from "vuex";
export default {
  data() {
    return {
      totalDataLength: 0,
      data: [],
      loading: true,
      options: {},
      maintenanceFilterGroup: new Form({
        rooms: [],
        selectedRooms: [],
        keyword: null,
        fromdate: null,
        todate: null,
      }),
      maintenanceFilterDialogConfig: {
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

      maintenanceFormDialogConfig: {
        buttonStyle: {
          block: true,
          class: "title font-weight-bold ma-2",
          text: "Add Maintenance",
          icon: "mdi-plus",
          isIcon: false,
          color: "primary",
          evalation: "5",
        },
      },
      headers: [
        {
          text: "uid",
        },
        {
          text: "Unit No",
        },
        {
          text: "Property",
        },
        { text: "Price (RM)" },
      ],
    };
  },
  watch: {
    options: {
      handler() {
        this.getMaintenances();
      },
      deep: true,
    },
  },
  computed: {
    isLoading() {
      return this.$store.getters.isLoading;
    },
    keywordEmpty() {
      return this.helpers.isEmpty(this.maintenanceFilterGroup.keyword);
    },
    fromdateEmpty() {
      return this.helpers.isEmpty(this.maintenanceFilterGroup.fromdate);
    },
    todateEmpty() {
      return this.helpers.isEmpty(this.maintenanceFilterGroup.todate);
    },
    roomsEmpty() {
      return this.helpers.isEmpty(this.maintenanceFilterGroup.selectedRooms);
    },
  },
  created() {},
  mounted() {
    this.getMaintenances();
  },
  methods: {
    ...mapActions({
      getMaintenancesAction: "getMaintenances",
      filterMaintenancesAction: "filterMaintenances",
      showLoadingAction: "showLoadingAction",
      endLoadingAction: "endLoadingAction",
    }),
    initMaintenanceFilter(filterGroup) {
      this.maintenanceFilterGroup.reset();
      if (filterGroup) {
        this.maintenanceFilterGroup.keyword = filterGroup.keyword;
      }
      this.options.page = 1;
      this.getMaintenances();
    },
    showMaintenance($data) {
      this.$router.push("/maintenance/" + $data.uid);
    },
    getMaintenances() {
      this.loading = true;
      const { sortBy, sortDesc, page, itemsPerPage } = this.options;

      var totalResult = itemsPerPage;
      //Show All Items
      if (totalResult == -1) {
        this.maintenanceFilterGroup.pageNumber = -1;
        this.maintenanceFilterGroup.pageSize = -1;
      } else {
        this.maintenanceFilterGroup.pageNumber = page;
        this.maintenanceFilterGroup.pageSize = itemsPerPage;
      }

      this.filterMaintenancesAction(this.maintenanceFilterGroup)
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
            <maintenance-form
              :editMode="false"
              :buttonStyle="maintenanceFormDialogConfig.buttonStyle"
              @created="showMaintenance($event)"
            ></maintenance-form>
          </v-col>
        </v-row>

        <v-row justify="center" align="center" class="mx-3">
          <v-col cols="12">
            <v-card raised>
              <v-card-subtitle v-show="!keywordEmpty">
                Keyword :
                <v-chip class="mx-2">{{ maintenanceFilterGroup.keyword }}</v-chip>
              </v-card-subtitle>
              <v-card-subtitle v-show="!fromdateEmpty">
                From Date :
                <v-chip class="mx-2">{{ maintenanceFilterGroup.keyword }}</v-chip>
              </v-card-subtitle>
              <v-card-subtitle v-show="!todateEmpty">
                To Date :
                <v-chip class="mx-2">{{ maintenanceFilterGroup.todate }}</v-chip>
              </v-card-subtitle>

              <v-card-subtitle v-show="!roomsEmpty">
                Rooms :
                <v-chip
                  class="mx-2"
                  v-for="maintenanceType in maintenanceFilterGroup.selectedRooms"
                  :key="maintenanceType.id"
                >{{ maintenanceType.name }}</v-chip>
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
                    <maintenance-filter-dialog
                      :buttonStyle="maintenanceFilterDialogConfig.buttonStyle"
                      :dialogStyle="maintenanceFilterDialogConfig.dialogStyle"
                      @submitFilter="initMaintenanceFilter($event)"
                    ></maintenance-filter-dialog>
                  </v-toolbar>
                </template>
                <template v-slot:item="props">
                  <tr @click="showMaintenance(props.item)">
                    <td>{{props.item.uid}}</td>
                    <td>{{props.item.room.name}}</td>
                    <td>{{props.item.property.name}}</td>
                    <td>{{props.item.price}}</td>
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
