


<script>
import { mapActions } from "vuex";
import { _ } from "../../../../common/common-function";
import ExportExcelButton from "../../../ExportExcelButton.vue";
import moment from "moment";
export default {
  components: { ExportExcelButton },
  data() {
    return {
      _: _,
      role : '',
      moment: moment,
      totalDataLength: 0,
      data: [],
      excelData: [],
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
      excelFields: {
        id: "id",
        uid: "uid",
        name: "name",
        email: "email",
        icno: "icno",
        tel1: "tel1",
        tel2: "tel2",
        tel3: "tel3",
        mother_name: "mother_name",
        mother_tel: "mother_tel",
        father_name: "father_name",
        father_tel: "father_tel",
        emergency_contact_name: "emergency_name",
        age: "age",
        occupation: "occupation",
        gender: "gender",
        religion: "religion",
        approach_method: "approach_method",
        address: "address",
        postcode: "postcode",
        state: "state",
        city: "city",
        birthday: "birthday",
        created_at: "created_at",
        updated_at: "updated_at",
        person_in_charge_id: "pic",
        person_in_charge: "creator.name",
      },
      headers: [
        {
          text: "Unit No.",
        },
        {
          text: "Name",
        },
        {
          text: "Birthday",
        },
        {
          text: "Gender",
        },
        { text: "Ic No" },
        { text: "Phone" },
        { text: "Emergency Contact" },
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
      if (filterGroup.room) {
        this.tenantFilterGroup.room_id = filterGroup.room.id;
        this.tenantFilterGroup.room = filterGroup.room;
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
    async fetchExcelData() {
      let total = this.totalDataLength || 0;
      let size = 50;
      let maxPage = Math.ceil(total / size);
      let promises = [];
      let self = this;
      _.forEach(_.range(maxPage), function (index) {
        console.log(index);
        promises.push(
          self.filterTenantsAction({
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
            v-if="
              helpers.isAccessible(_.get(role, ['name']), 'tenant', 'create')
            "
          >
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
                  _.get(tenantFilterGroup, ["picObj", "name"]) || "N/A"
                }}</v-chip>
              </v-card-subtitle>
              <v-card-subtitle v-show="tenantFilterGroup.room">
                Room :
                <v-chip class="mx-2">{{
                  _.get(tenantFilterGroup, ["room", "unit"]) || "N/A"
                }}</v-chip>
              </v-card-subtitle>
            </v-card>
          </v-col>
        </v-row>
        <v-row
          justify="center"
          align="center"
          class="ma-3"
          v-if="helpers.isAccessible(_.get(role, ['name']), 'tenant', 'read')"
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
                  <v-toolbar flat>
                    <tenant-filter-dialog
                      :buttonStyle="tenantFilterDialogConfig.buttonStyle"
                      :dialogStyle="tenantFilterDialogConfig.dialogStyle"
                      @submitFilter="initTenantFilter($event)"
                    ></tenant-filter-dialog>
                  </v-toolbar>
                  <v-toolbar
                    flat
                    class="mb-5 justify-end d-flex"
                    v-if="_.isArray(excelData) && !_.isEmpty(excelData)"
                  >
                    <download-excel
                      :header="`All_Tenant_${moment().format('YYYY_MM_DD')}`"
                      :name="`All_Tenant_${moment().format('YYYY_MM_DD')}.csv`"
                      :fields="excelFields"
                      type="csv"
                      :data="excelData || []"
                      ><v-btn text color="primary"
                        >Download as Excel</v-btn
                      ></download-excel
                    >
                  </v-toolbar>
                </template>
                <template v-slot:item="props">
                  <tr @click="showTenant(props.item)">
                    <td class="text-truncate">{{ _.map(_.get(props.item, `roomcontracts`), 'room.unit') || [] | getArrayValues }}</td>
                    <td class="text-truncate">{{ props.item.name }}</td>
                    <td class="text-truncate">{{ props.item.birthday | formatDate }}</td>
                    <td class="text-truncate">{{ props.item.gender }}</td>
                    <td class="text-truncate">{{ props.item.icno }}</td>
                    <td class="text-truncate">{{ props.item.tel1 }}</td>
                    <td class="text-truncate">{{ props.item.emergency_contact }}</td>
                    <td class="text-truncate">{{ props.item.approach_method }}</td>
                    <td class="text-truncate">
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