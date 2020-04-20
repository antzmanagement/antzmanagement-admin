<template>
  <v-app>
    <navbar></navbar>
    <v-content>
      <v-container class="fill-height" fluid>
        <loading></loading>
        <v-row justify="center" align="center" class="ma-3">
          <v-col cols="12">
            <tenant-form :editMode="false" @created="showTenant($event)"></tenant-form>
          </v-col>
        </v-row>

        <v-row justify="center" align="center" class="mx-3">
          <v-col cols="12">
            <v-card>
              <v-card-subtitle v-show="!keywordEmpty">
                Keyword :
                <v-chip class="mx-2">{{ tenantFilterGroup.keyword }}</v-chip>
              </v-card-subtitle>
              <v-card-subtitle v-show="!fromdateEmpty">
                From Date :
                <v-chip class="mx-2">{{ tenantFilterGroup.keyword }}</v-chip>
              </v-card-subtitle>
              <v-card-subtitle v-show="!todateEmpty">
                To Date :
                <v-chip class="mx-2">{{ tenantFilterGroup.todate }}</v-chip>
              </v-card-subtitle>

              <v-card-subtitle v-show="!roomTypesEmpty">
                Room Types :
                <v-chip
                  class="mx-2"
                  v-for="roomType in tenantFilterGroup.selectedRoomTypes"
                  :key="roomType.id"
                >{{ roomType.name | capitalizeFirstLetter }}</v-chip>
              </v-card-subtitle>
            </v-card>
          </v-col>
        </v-row>
        <v-row justify="center" align="center" class="ma-3">
          <v-col cols="12">
            <v-data-table
              :headers="headers"
              :items="data"
              :options.sync="options"
              :server-items-length="totalDataLength"
              :loading="loading"
              class="elevation-1"
            >
              <template v-slot:top>
                <v-toolbar flat>
                  <tenant-filter-dialog
                    :buttonStyle="tenantFilterDialogConfig.buttonStyle"
                    :dialogStyle="tenantFilterDialogConfig.dialogStyle"
                    @submitFilter="initTenantFilter($event)"
                  ></tenant-filter-dialog>
                </v-toolbar>
              </template>
              <template v-slot:item="props">
                <tr @click="showTenant(props.item)">
                  <td>{{props.item.name}}</td>
                  <td>{{props.item.icno}}</td>
                  <td>{{props.item.tel1}}</td>
                  <td>{{props.item.email}}</td>
                </tr>
              </template>
            </v-data-table>
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
      tenantFilterGroup: new Form({
        roomTypes: [],
        selectedRoomTypes: [],
        keyword: null,
        fromdate: null,
        todate: null
      }),
      tenantFilterDialogConfig: {
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
      headers: [
        {
          text: "Name",
          align: "start",
          sortable: false,
          value: "name"
        },
        { text: "Ic No", value: "icno" },
        { text: "Phone", value: "tel1" },
        { text: "Email", value: "email" }
      ]
    };
  },
  watch: {
    options: {
      handler() {
        this.getTenants();
      },
      deep: true
    }
  },
  computed: {
    isLoading() {
      return this.$store.getters.isLoading;
    },
    keywordEmpty() {
      return this.helpers.isEmpty(this.tenantFilterGroup.keyword);
    },
    fromdateEmpty() {
      return this.helpers.isEmpty(this.tenantFilterGroup.fromdate);
    },
    todateEmpty() {
      return this.helpers.isEmpty(this.tenantFilterGroup.todate);
    },
    roomTypesEmpty() {
      return this.helpers.isEmpty(this.tenantFilterGroup.selectedRoomTypes);
    }
  },
  created() {
    this.$vuetify.theme.dark = true;
  },
  mounted() {
    this.getTenants();
  },
  methods: {
    ...mapActions({
      getTenantsAction: "getTenants",
      filterTenantsAction: "filterTenants",
      showLoadingAction: "showLoadingAction",
      endLoadingAction: "endLoadingAction"
    }),
    initTenantFilter(filterGroup) {
      this.tenantFilterGroup.reset();
      if (filterGroup) {
        this.tenantFilterGroup.selectedRoomTypes = filterGroup.roomTypes;
        this.tenantFilterGroup.roomTypes = filterGroup.roomTypes.map(function(
          roomType
        ) {
          return roomType.id;
        });
        this.tenantFilterGroup.keyword = filterGroup.keyword;
      }
      this.getTenants();
    },
    showTenant($data) {
      this.$router.push("/tenant/" + $data.uid);
    },
    getTenants() {
      this.loading = true;
      const { sortBy, sortDesc, page, itemsPerPage } = this.options;

      var totalResult = itemsPerPage;
      //Show All Items
      if (totalResult == -1) {
        this.tenantFilterGroup.pageNumber = -1;
        this.tenantFilterGroup.pageSize = -1;
      } else {
        this.tenantFilterGroup.pageNumber = page;
        this.tenantFilterGroup.pageSize = itemsPerPage;
      }

      this.filterTenantsAction(this.tenantFilterGroup)
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