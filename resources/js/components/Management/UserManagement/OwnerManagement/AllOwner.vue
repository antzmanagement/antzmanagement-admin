

<script>
import { mapActions } from "vuex";
import moment from "moment";
import { _ } from "../../../../common/common-function";
export default {
  data() {
    return {
      role: "",
      moment: moment,
      _: _,
      totalDataLength: 0,
      data: [],
      loading: true,
      options: {},
      ownerFilterGroup: new Form({
        roomTypes: [],
        selectedRoomTypes: [],
        keyword: null,
        fromdate: null,
        todate: null,
      }),
      ownerFormDialogConfig: {
        buttonStyle: {
          block: true,
          class: "title font-weight-bold ma-2",
          text: "Add Owner",
          icon: "mdi-plus",
          isIcon: false,
          color: "primary",
          evalation: "5",
        },
      },
      ownerFilterDialogConfig: {
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
          text: "Name",
          align: "start",
          value: "name",
          width: 200,
        },
        {
          text: "Unit",
          width: 200,
        },
        { text: "Ic No", value: "icno", width: 200 },
        { text: "Phone", value: "tel1", width: 200 },
        { text: "Email", value: "email", width: 200 },
        {
          text: "Benificiary Name",
          width: 200,
        },
        {
          text: "Benificiary Bank",
          width: 200,
        },
        {
          text: "Bank A/C No",
          width: 200,
        },
      ],
      excelData: [],
      excelFields: {
        id: "id",
        uid: "uid",
        name: "name",
        email: "email",
        icno: "icno",
        tel1: "tel1",
        tel2: "tel2",
        tel3: "tel3",
        occupation: "occupation",
        address: "address",
        postcode: "postcode",
        state: "state",
        city: "city",
        bank_type: "banktype",
        other_bank_type: "otherbanktype",
        bank_account_name: "bankaccountname",
        bank_account: "bankaccount",
        created_at: "created_at",
        updated_at: "updated_at",
      },
    };
  },
  watch: {
    options: {
      handler() {
        this.getOwners();
      },
      deep: true,
    },
    totalDataLength(v) {
      console.log(v);
      if (v > 0) {
        // this.fetchExcelData();
      }
    },
  },
  computed: {
    isLoading() {
      return this.$store.getters.isLoading;
    },
    keywordEmpty() {
      return this.helpers.isEmpty(this.ownerFilterGroup.keyword);
    },
    fromdateEmpty() {
      return this.helpers.isEmpty(this.ownerFilterGroup.fromdate);
    },
    todateEmpty() {
      return this.helpers.isEmpty(this.ownerFilterGroup.todate);
    },
    roomTypesEmpty() {
      return this.helpers.isEmpty(this.ownerFilterGroup.selectedRoomTypes);
    },
  },
  created() {
    document.title = `All Owner`;

    var styles = this.helpers.managementStyles();
  },
  mounted() {},
  methods: {
    ...mapActions({
      filterOwnersAction: "filterOwners",
      showLoadingAction: "showLoadingAction",
      endLoadingAction: "endLoadingAction",
    }),
    initOwnerFilter(filterGroup) {
      this.ownerFilterGroup.reset();
      if (filterGroup) {
        this.ownerFilterGroup.keyword = filterGroup.keyword;
      }
      if (filterGroup.room) {
        this.ownerFilterGroup.room_id = filterGroup.room.id;
        this.ownerFilterGroup.room = filterGroup.room;
      }
      if (filterGroup.tel) {
        this.ownerFilterGroup.tel = filterGroup.tel;
      }
      this.options.page = 1;
      this.getOwners();
    },
    showOwner($data) {
      let routeData = this.$router.resolve({
        name: "owner",
        params: { uid: $data.uid },
      });
      window.open(routeData.href, "_blank");
      // this.$router.push("/owner/" + $data.uid);
    },
    getOwners() {
      this.loading = true;
      const { sortBy, sortDesc, page, itemsPerPage } = this.options;

      let filterGroup = _.cloneDeep(this.ownerFilterGroup);
      filterGroup.pageNumber = page;
      filterGroup.pageSize = itemsPerPage;

      delete filterGroup.room;
      console.log(this.ownerFilterGroup);
      this.filterOwnersAction(filterGroup)
        .then((data) => {
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
          self.filterOwnersAction({
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
          <v-col cols="12">
            <owner-form
              :editMode="false"
              :buttonStyle="ownerFormDialogConfig.buttonStyle"
              @created="showOwner($event)"
              v-if="
                helpers.isAccessible(_.get(role, ['name']), 'owner', 'create')
              "
            ></owner-form>
          </v-col>
        </v-row>

        <v-row justify="center" align="center" class="mx-3">
          <v-col cols="12">
            <v-card raised>
              <v-card-subtitle v-show="!keywordEmpty">
                Keyword :
                <v-chip class="mx-2">{{ ownerFilterGroup.keyword }}</v-chip>
              </v-card-subtitle>
              <v-card-subtitle v-show="!fromdateEmpty">
                From Date :
                <v-chip class="mx-2">{{ ownerFilterGroup.keyword }}</v-chip>
              </v-card-subtitle>
              <v-card-subtitle v-show="!todateEmpty">
                To Date :
                <v-chip class="mx-2">{{ ownerFilterGroup.todate }}</v-chip>
              </v-card-subtitle>
              <v-card-subtitle v-show="ownerFilterGroup.room">
                Room :
                <v-chip class="mx-2">{{
                  _.get(ownerFilterGroup, ["room", "unit"]) || "N/A"
                }}</v-chip>
              </v-card-subtitle>

              <v-card-subtitle v-show="!roomTypesEmpty">
                Room Types :
                <v-chip
                  class="mx-2"
                  v-for="roomType in ownerFilterGroup.selectedRoomTypes"
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
          v-if="
            helpers.isAccessible(_.get(role, ['name']), 'owner', 'tableView')
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
                calculate-widths
                :footer-props="{
                  'items-per-page-options': [10],
                  'show-current-page': true,
                }"
              >
                <template v-slot:top>
                  <v-toolbar flat class="mb-5">
                    <owner-filter-dialog
                      :buttonStyle="ownerFilterDialogConfig.buttonStyle"
                      :dialogStyle="ownerFilterDialogConfig.dialogStyle"
                      @submitFilter="initOwnerFilter($event)"
                    ></owner-filter-dialog>
                  </v-toolbar>
                  <v-toolbar
                    flat
                    class="mb-5 justify-end d-flex"
                    v-if="_.isArray(excelData) && !_.isEmpty(excelData)"
                  >
                    <download-excel
                      :header="`All_Owner_${moment().format('YYYY_MM_DD')}`"
                      :name="`All_Owner_${moment().format('YYYY_MM_DD')}.csv`"
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
                  <tr
                    @click="
                      helpers.isAccessible(
                        _.get(role, ['name']),
                        'owner',
                        'view'
                      )
                        ? showOwner(props.item)
                        : null
                    "
                  >
                    <td class="text-truncate small">{{ props.item.name }}</td>
                    <td class="text-truncate fs-5">
                      <div v-for="room in props.item.ownrooms" :key="room.id">
                        {{ _.get(room, ["unit"]) || "N/A" }}
                      </div>
                    </td>
                    <td class="text-truncate fs-normal">
                      {{ props.item.icno }}
                    </td>
                    <td class="text-truncate">{{ props.item.tel1 }}</td>
                    <td class="text-truncate">{{ props.item.email }}</td>
                    <td class="text-truncate">
                      {{ props.item.bankaccountname }}
                    </td>
                    <td class="text-truncate">{{ props.item.banktype }}</td>
                    <td class="text-truncate">{{ props.item.bankaccount }}</td>
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
