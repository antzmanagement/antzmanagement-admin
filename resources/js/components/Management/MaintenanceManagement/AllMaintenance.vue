
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
      maintenancePayFormDialog: false,
      maintenanceFormDialog: false,
      maintenanceEditMode: false,
      selectedMaintenance: {},
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
      headers: [
        {
          text: "Unit No",
        },
        {
          text: "Property",
        },
        { text: "Maintenance Type" },
        { text: "Maintenance Status" },
        { text: "Price (RM)" },
        { text: "Claimed By" },
        { text: "Created At" },
        { text: "Actions" },
      ],
      excelData: [],
      excelFields: {
        id: "id",
        uid: "uid",
        room: {
          field: "room",
          callback: (value) => {
            return _.get(value, `name`) || "N/A";
          },
        },
        room_id: {
          field: "room",
          callback: (value) => {
            return _.get(value, `id`) || "N/A";
          },
        },
        property: {
          field: "property",
          callback: (value) => {
            return _.get(value, `name`) || "N/A";
          },
        },
        property_id: {
          field: "property",
          callback: (value) => {
            return _.get(value, `id`) || "N/A";
          },
        },
        price: "price",
        maintenance_type: "maintenance_type",
        maintenance_status: "maintenance_status",
        remark: "remark",
        created_at: "created_at",
        updated_at: "updated_at",
      },
    };
  },
  watch: {
    options: {
      handler() {
        this.getMaintenances();
      },
      deep: true,
    },
    totalDataLength(v) {
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
  created() {
    document.title = "All Maintenance";
  },
  mounted() {
    this.getMaintenances();
  },
  methods: {
    ...mapActions({
      getMaintenancesAction: "getMaintenances",
      filterMaintenancesAction: "filterMaintenances",
      showLoadingAction: "showLoadingAction",
      endLoadingAction: "endLoadingAction",
      createMaintenanceAction: "createMaintenance",
      updateMaintenanceAction: "updateMaintenance",
      deleteMaintenanceAction: "deleteMaintenance",
    }),
    initMaintenanceFilter(filterGroup) {
      this.maintenanceFilterGroup.reset();
      if (filterGroup.owner) {
        this.maintenanceFilterGroup.owner_id = filterGroup.owner.id;
        this.maintenanceFilterGroup.owner = filterGroup.owner;
      }
      if (filterGroup.tenant) {
        this.maintenanceFilterGroup.tenant_id = filterGroup.tenant.id;
        this.maintenanceFilterGroup.tenant = filterGroup.tenant;
      }
      if (filterGroup.room) {
        this.maintenanceFilterGroup.room_id = filterGroup.room.id;
        this.maintenanceFilterGroup.room = filterGroup.room;
      }
      if (filterGroup.property) {
        this.maintenanceFilterGroup.property_id = filterGroup.property.id;
        this.maintenanceFilterGroup.property = filterGroup.property;
      }
      if (filterGroup.fromdate) {
        this.maintenanceFilterGroup.fromdate = filterGroup.fromdate;
      }
      if (filterGroup.todate) {
        this.maintenanceFilterGroup.todate = filterGroup.todate;
      }
      if (filterGroup.maintenance_status) {
        this.maintenanceFilterGroup.maintenance_status =
          filterGroup.maintenance_status;
      }
      if (filterGroup.maintenance_type) {
        this.maintenanceFilterGroup.maintenance_type =
          filterGroup.maintenance_type;
      }
      this.options.page = 1;
      this.getMaintenances();
    },
    showMaintenance($data) {
      let routeData = this.$router.resolve({
        name: "maintenance",
        params: { uid: $data.uid },
      });
      window.open(routeData.href, "_blank");
      // this.$router.push("/maintenance/" + $data.uid);
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

      let filterGroup = _.cloneDeep(this.maintenanceFilterGroup);
      delete filterGroup.room;
      delete filterGroup.tenant;
      delete filterGroup.owner;
      delete filterGroup.property;
      this.filterMaintenancesAction(filterGroup)
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
      let size = 100;
      let maxPage = Math.ceil(total / size);
      let promises = [];
      let self = this;
      _.forEach(_.range(maxPage), function (index) {
        promises.push(
          self.filterMaintenancesAction({
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
    openMaintenanceDialog(item, editMode) {
      this.maintenanceEditMode = editMode == true;
      this.selectedMaintenance = item || {};
      this.maintenanceFormDialog = true;
    },
    updateMaintenance($data) {
      try {
        console.log($data);
        if (_.get($data, "uid")) {
          let finalData = _.cloneDeep($data);
          this.updateMaintenanceAction(finalData)
            .then((data) => {
              Toast.fire({
                icon: "success",
                title: "Successful Updated Maintenance. ",
              });
              this.$Progress.finish();
              this.endLoadingAction();
              this.maintenanceFormDialog = false;
              this.updateMaintenances(data.data);
            })
            .catch((error) => {
              Toast.fire({
                icon: "warning",
                title: "Something went wrong!!!!! ",
              });
              this.$Progress.finish();
              this.endLoadingAction();
              this.maintenanceFormDialog = false;
            });
        } else {
          Toast.fire({
            icon: "error",
            title: "Maintenance Not Found!!!!! ",
          });
          this.$Progress.finish();
          this.endLoadingAction();
          this.maintenanceFormDialog = false;
        }
      } catch (error) {
        console.log(error);
      }
    },
    updateMaintenances($data, remove = false) {
      console.log($data);
      try {
        this.maintenanceFormDialog = false;
        if (!_.get($data, "uid")) {
          $data.uid = new Date().getTime();
        }

        if (remove) {
          this.data = _.filter(this.data, (item) => {
            return item.uid != $data.uid;
          });
        } else {
          if (!_.some(this.data, ["uid", $data.uid])) {
            this.data = _.unionBy([$data], this.data, "uid");
          } else {
            this.data = _.map(this.data, (item) => {
              return item.uid == $data.uid ? $data : item;
            });
          }
        }
      } catch (error) {
        console.log(error);
      }
    },
    createMaintenance($data) {
      try {
        console.log($data);
        let finalData = _.cloneDeep($data);
        this.createMaintenanceAction(finalData)
          .then((data) => {
            Toast.fire({
              icon: "success",
              title: "Successful Created Maintenance. ",
            });
            this.$Progress.finish();
            this.endLoadingAction();
            this.maintenanceFormDialog = false;
            this.showMaintenance(data.data);
          })
          .catch((error) => {
            Toast.fire({
              icon: "warning",
              title: "Something went wrong!!!!! ",
            });
            this.$Progress.finish();
            this.endLoadingAction();
            this.maintenanceFormDialog = false;
          });
      } catch (error) {
        console.log(error);
      }
    },
    deleteMaintenance(item) {
      this.showLoadingAction();
      this.deleteMaintenanceAction(item)
        .then((data) => {
          Toast.fire({
            icon: "success",
            title: "Successful Deleted Maintenance. ",
          });
          this.$Progress.finish();
          this.endLoadingAction();
          this.updateMaintenances(data.data, true);
        })
        .catch((error) => {
          Toast.fire({
            icon: "warning",
            title: "Something went wrong!!!!! ",
          });
          this.$Progress.finish();
          this.endLoadingAction();
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
        <v-row justify="center" align="center" class="ma-3">
          <v-col
            cols="12"
            v-if="
              helpers.isAccessible(
                _.get(role, ['name']),
                'roomMaintenance',
                'create'
              )
            "
          >
            <v-btn block color="primary" @click="maintenanceFormDialog = true"
              ><v-icon left>mdi-plus</v-icon>Add Maintenance</v-btn
            >
          </v-col>
        </v-row>

        <v-row justify="center" align="center" class="mx-3">
          <v-col cols="12">
            <v-card raised>
              <v-card-subtitle v-show="maintenanceFilterGroup.fromdate">
                From Date :
                <v-chip class="mx-2">{{
                  maintenanceFilterGroup.fromdate
                }}</v-chip>
              </v-card-subtitle>
              <v-card-subtitle v-show="maintenanceFilterGroup.todate">
                To Date :
                <v-chip class="mx-2">{{
                  maintenanceFilterGroup.todate
                }}</v-chip>
              </v-card-subtitle>
              <v-card-subtitle v-show="maintenanceFilterGroup.owner">
                Claim By :
                <v-chip class="mx-2">{{
                  _.get(maintenanceFilterGroup, ["owner", "name"]) || "N/A"
                }}</v-chip>
              </v-card-subtitle>
              <v-card-subtitle v-show="maintenanceFilterGroup.room">
                Room :
                <v-chip class="mx-2">{{
                  _.get(maintenanceFilterGroup, ["room", "unit"]) || "N/A"
                }}</v-chip>
              </v-card-subtitle>
              <v-card-subtitle v-show="maintenanceFilterGroup.property">
                Property :
                <v-chip class="mx-2">{{
                  _.get(maintenanceFilterGroup, ["property", "name"]) || "N/A"
                }}</v-chip>
              </v-card-subtitle>
              <v-card-subtitle v-show="maintenanceFilterGroup.maintenance_type">
                Maintenance Type :
                <v-chip class="mx-2">{{
                  _.get(maintenanceFilterGroup, ["maintenance_type"]) || "N/A"
                }}</v-chip>
              </v-card-subtitle>
              <v-card-subtitle
                v-show="maintenanceFilterGroup.maintenance_status"
              >
                Maintenance Status :
                <v-chip class="mx-2">{{
                  _.get(maintenanceFilterGroup, ["maintenance_status"]) || "N/A"
                }}</v-chip>
              </v-card-subtitle>
            </v-card>
          </v-col>
        </v-row>
        <v-row
          justify="center"
          align="center"
          class="ma-3"
          v-if="
            helpers.isAccessible(
              _.get(role, ['name']),
              'roomMaintenance',
              'tableView'
            )
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
                  <v-toolbar flat>
                    <maintenance-filter-dialog
                      :buttonStyle="maintenanceFilterDialogConfig.buttonStyle"
                      :dialogStyle="maintenanceFilterDialogConfig.dialogStyle"
                      @submitFilter="initMaintenanceFilter($event)"
                    ></maintenance-filter-dialog>
                  </v-toolbar>
                  <v-toolbar
                    flat
                    class="mb-5 justify-end d-flex"
                    v-if="_.isArray(excelData) && !_.isEmpty(excelData)"
                  >
                    <download-excel
                      :header="`All_Maintenance_${moment().format(
                        'YYYY_MM_DD'
                      )}`"
                      :name="`All_Maintenance_${moment().format(
                        'YYYY_MM_DD'
                      )}.csv`"
                      type="csv"
                      :data="excelData || []"
                      :fields="excelFields || {}"
                      ><v-btn text color="primary"
                        >Download as Excel</v-btn
                      ></download-excel
                    >
                  </v-toolbar>
                </template>
                <template v-slot:item="props">
                  <tr>
                    <td
                      class="text-truncate"
                      @click="
                        helpers.isAccessible(
                          _.get(role, ['name']),
                          'roomMaintenance',
                          'view'
                        )
                          ? showMaintenance(props.item)
                          : null
                      "
                    >
                      {{ _.get(props.item, `room.name`) || "N/A" }}
                    </td>
                    <td
                      class="text-truncate"
                      @click="
                        helpers.isAccessible(
                          _.get(role, ['name']),
                          'roomMaintenance',
                          'view'
                        )
                          ? showMaintenance(props.item)
                          : null
                      "
                    >
                      {{
                        _.get(props.item, ["property", "name"]) == "others"
                          ? `${_.get(props.item, ["property", "text"]) || ""} - ${
                              _.get(props.item, ["other_property"]) || ""
                            }`
                          : _.get(props.item, ["property", "text"]) || "N/A"
                      }}
                    </td>
                    <td
                      class="text-truncate"
                      @click="
                        helpers.isAccessible(
                          _.get(role, ['name']),
                          'roomMaintenance',
                          'view'
                        )
                          ? showMaintenance(props.item)
                          : null
                      "
                    >
                      {{ props.item.maintenance_type }}
                    </td>
                    <td
                      class="text-truncate"
                      @click="
                        helpers.isAccessible(
                          _.get(role, ['name']),
                          'roomMaintenance',
                          'view'
                        )
                          ? showMaintenance(props.item)
                          : null
                      "
                    >
                      {{ props.item.maintenance_status }}
                    </td>
                    <td
                      class="text-truncate"
                      @click="
                        helpers.isAccessible(
                          _.get(role, ['name']),
                          'roomMaintenance',
                          'view'
                        )
                          ? showMaintenance(props.item)
                          : null
                      "
                    >
                      {{ props.item.price }}
                    </td>
                    <td
                      class="text-truncate"
                      @click="
                        helpers.isAccessible(
                          _.get(role, ['name']),
                          'roomMaintenance',
                          'view'
                        )
                          ? showMaintenance(props.item)
                          : null
                      "
                    >
                      {{
                        _.get(props.item, ["claim_by_owner"])
                          ? _.get(props.item, ["owner", "name"]) || "N/A"
                          : _.get(props.item, ["claim_by_tenant"])
                          ? _.get(props.item, ["tenant", "name"]) || "N/A"
                          : "N/A"
                      }}
                    </td>
                    <td
                      class="text-truncate"
                      @click="
                        helpers.isAccessible(
                          _.get(role, ['name']),
                          'roomMaintenance',
                          'view'
                        )
                          ? showMaintenance(props.item)
                          : null
                      "
                    >
                      {{ props.item.created_at | formatDate }}
                    </td>
                    <td class="text-truncate">
                      <print-maintenance-button
                        :item="props.item"
                        v-if="
                          props.item.paid &&
                          helpers.isAccessible(
                            _.get(role, ['name']),
                            'roomMaintenance',
                            'print'
                          )
                        "
                      >
                        <v-icon small class="mr-2" color="success"
                          >mdi-printer</v-icon
                        >
                      </print-maintenance-button>

                      <v-icon
                        small
                        class="mr-2"
                        @click="
                          maintenancePayFormDialog = true;
                          selectedMaintenance = props.item;
                        "
                        color="warning"
                        v-else-if="
                          helpers.isAccessible(
                            _.get(role, ['name']),
                            'roomMaintenance',
                            'makePayment'
                          ) &&
                          _.get(props.item, 'maintenance_status') != 'reject'
                        "
                        >mdi-currency-usd</v-icon
                      >
                      <v-icon
                        small
                        class="mr-2"
                        @click="openMaintenanceDialog(props.item, true)"
                        color="success"
                        v-if="
                          helpers.isAccessible(
                            _.get(role, ['name']),
                            'roomMaintenance',
                            'edit'
                          )
                        "
                        >mdi-pencil</v-icon
                      >

                      <confirm-dialog
                        v-if="
                          helpers.isAccessible(
                            _.get(role, ['name']),
                            'roomMaintenance',
                            'delete'
                          )
                        "
                        @confirmed="
                          $event ? deleteMaintenance(props.item) : null
                        "
                      >
                        <template v-slot="{ on }">
                          <v-icon color="error" size="small" v-on="on"
                            >mdi-trash-can-outline</v-icon
                          >
                        </template>
                      </confirm-dialog>
                    </td>
                  </tr>
                </template>
              </v-data-table>
            </v-card>
          </v-col>
        </v-row>

        <v-dialog
          v-model="maintenanceFormDialog"
          persistent
          hideOverlay
          max-width="600px"
        >
          <maintenance-form-1
            :selectedData="selectedMaintenance || {}"
            :reset="maintenanceFormDialog"
            :editMode="maintenanceEditMode"
            @cancel="
              maintenanceFormDialog = false;
              selectedMaintenance = {};
            "
            @submit="
              (event) => {
                if (!maintenanceEditMode) {
                  createMaintenance(event);
                } else {
                  updateMaintenance(event);
                }
              }
            "
          >
          </maintenance-form-1>
        </v-dialog>

        <v-dialog
          v-model="maintenancePayFormDialog"
          persistent
          hideOverlay
          max-width="600px"
        >
          <maintenance-pay-form
            :uid="_.get(this.selectedMaintenance, ['uid'])"
            @close="
              maintenancePayFormDialog = false;
              selectedMaintenance = {};
            "
            @updated="updateMaintenances"
          >
          </maintenance-pay-form>
        </v-dialog>
      </v-container>
    </v-content>
  </v-app>
</template>
