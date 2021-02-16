


<script>
import { mapActions } from "vuex";
import { _ } from "../../../../common/common-function";
export default {
  data() {
    return {
      _: _,
      totalDataLength: 0,
      data: [],
      loading: true,
      options: {},
      tenantFilterGroup: new Form({
        keyword: null,
        fromdate: null,
        todate: null,
      }),
      tenantFormDialogConfig: {
        buttonStyle: {
          block: true,
          class: "title font-weight-bold ma-2",
          text: "Add Tenant",
          icon: "mdi-plus",
          isIcon: false,
          color: "primary",
          evalation: "5",
        },
      },
      tenantFilterDialogConfig: {
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
        },
        {
          text: "Name",
        },
        {
          text: "Age",
        },
        {
          text: "Birthday",
        },
        {
          text: "Gender",
        },
        { text: "Ic No", value: "icno" },
        { text: "Phone", value: "tel1" },
        { text: "Email", value: "email" },
        { text: "Religion" },
        { text: "Occupation" },
        {
          text: "Approach Methods",
        },
        { text: "Person In Charge" },
      ],
    };
  },
  watch: {
    options: {
      handler() {
        this.getTenants();
      },
      deep: true,
    },
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
    },
  },
  created() {
    var styles = this.helpers.managementStyles();
  },
  mounted() {
    this.getTenants();
  },
  methods: {
    ...mapActions({
      getTenantsAction: "getTenants",
      filterTenantsAction: "filterTenants",
      showLoadingAction: "showLoadingAction",
      endLoadingAction: "endLoadingAction",
    }),
    initTenantFilter(filterGroup) {
      this.tenantFilterGroup.reset();
      if (filterGroup.keyword) {
        this.tenantFilterGroup.keyword = filterGroup.keyword;
      }
      if (filterGroup.gender) {
        this.tenantFilterGroup.gender = filterGroup.gender;
      }
      if (filterGroup.approach_method) {
        this.tenantFilterGroup.approach_method = filterGroup.approach_method;
      }
      if (filterGroup.religion) {
        this.tenantFilterGroup.religion = filterGroup.religion;
      }
      if (filterGroup.pic) {
        this.tenantFilterGroup.pic = filterGroup.pic.id;
        this.tenantFilterGroup.picObj = filterGroup.pic;
      }
      if (filterGroup.birthdayfromdate) {
        this.tenantFilterGroup.birthdayfromdate = filterGroup.birthdayfromdate;
      }
      if (filterGroup.birthdaytodate) {
        this.tenantFilterGroup.birthdaytodate = filterGroup.birthdaytodate;
      }
      console.log(this.tenantFilterGroup);
      this.options.page = 1;
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
        .then((data) => {
          if (data.data) {
            this.data = data.data;
          this.totalDataLength = data.totalResult;
          } else {
            this.data = [];
          this.totalDataLength = 0;
          }
          this.loading = false;
        })
        .catch((error) => {
          console.log(error);
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
            <tenant-form
              :editMode="false"
              :buttonStyle="tenantFormDialogConfig.buttonStyle"
              @created="showTenant($event)"
            ></tenant-form>
          </v-col>
        </v-row>

        <v-row justify="center" align="center" class="mx-3">
          <v-col cols="12">
            <v-card raised>
              <v-card-subtitle v-show="tenantFilterGroup.keyword">
                Keyword :
                <v-chip class="mx-2">{{ tenantFilterGroup.keyword }}</v-chip>
              </v-card-subtitle>
              <v-card-subtitle v-show="tenantFilterGroup.birthdayfromdate">
                Birthday From Date :
                <v-chip class="mx-2">{{
                  tenantFilterGroup.birthdayfromdate | formatDate
                }}</v-chip>
              </v-card-subtitle>
              <v-card-subtitle v-show="tenantFilterGroup.birthdaytodate">
                Birthday To Date :
                <v-chip class="mx-2">{{
                  tenantFilterGroup.birthdaytodate | formatDate
                }}</v-chip>
              </v-card-subtitle>
              <v-card-subtitle v-show="tenantFilterGroup.religion">
                Religion :
                <v-chip class="mx-2">{{ tenantFilterGroup.religion }}</v-chip>
              </v-card-subtitle>
              <v-card-subtitle v-show="tenantFilterGroup.gender">
                Gender :
                <v-chip class="mx-2">{{ tenantFilterGroup.gender }}</v-chip>
              </v-card-subtitle>
              <v-card-subtitle v-show="tenantFilterGroup.approach_method">
                Approach Method :
                <v-chip class="mx-2">{{
                  tenantFilterGroup.approach_method
                }}</v-chip>
              </v-card-subtitle>
              <v-card-subtitle v-show="tenantFilterGroup.picObj">
                Person In Charge :
                <v-chip class="mx-2">{{
                  _.get(tenantFilterGroup, ["picObj", "name"]) || 'N/A'
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
                disable-sort
              >
                <template v-slot:top>
                  <v-toolbar flat class="mb-5">
                    <tenant-filter-dialog
                      :buttonStyle="tenantFilterDialogConfig.buttonStyle"
                      :dialogStyle="tenantFilterDialogConfig.dialogStyle"
                      @submitFilter="initTenantFilter($event)"
                    ></tenant-filter-dialog>
                  </v-toolbar>
                </template>
                <template v-slot:item="props">
                  <tr @click="showTenant(props.item)">
                    <td>{{ props.item.id }}</td>
                    <td>{{ props.item.name }}</td>
                    <td>{{ props.item.age }}</td>
                    <td>{{ props.item.birthday | formatDate }}</td>
                    <td>{{ props.item.gender }}</td>
                    <td>{{ props.item.icno }}</td>
                    <td>{{ props.item.tel1 }}</td>
                    <td>{{ props.item.email }}</td>
                    <td>{{ props.item.religion }}</td>
                    <td>{{ props.item.occupation }}</td>
                    <td>{{ props.item.approach_method }}</td>
                    <td>
                      {{ _.get(props.item, ["creator", "name"]) || "N/A" }}
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