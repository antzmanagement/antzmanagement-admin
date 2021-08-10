
<script>
import { mapActions } from "vuex";
import { _ } from "../../../common/common-function";
export default {
  data: () => ({
    _: _,
    maintenancePayFormDialog: false,
    maintenanceFormDialog: false,
    maintenanceEditMode: false,
    editButtonStyle: {
      block: false,
      color: "success",
      class: "m-3",
      text: "Edit",
      icon: "mdi-pencil",
    },
    deleteButtonConfig: {
      buttonStyle: {
        block: false,
        color: "error",
        class: "m-3",
        text: "Delete",
        icon: "mdi-trash-can-outline",
      },
    },
    data: new Form({
      remark: "",
      price: "",
      room: [],
      property: [],
    }),
  }),

  computed: {
    isLoading() {
      return this.$store.getters.isLoading;
    },
  },
  created() {

    document.title = 'Maintenance'
  
    this.$Progress.start();
    this.showLoadingAction();
    this.getMaintenanceAction({ uid: this.$route.params.uid })
      .then((data) => {
        this.data = data.data;
        this.$Progress.finish();
        this.endLoadingAction();
      })
      .catch((error) => {
        Toast.fire({
          icon: "warning",
          title: "Fail to retrieve the maintenance!!!!! ",
        });
        this.$Progress.finish();
        this.endLoadingAction();
      });
  },

  methods: {
    ...mapActions({
      getMaintenanceAction: "getMaintenance",
      updateMaintenanceAction: "updateMaintenance",
      deleteMaintenanceAction: "deleteMaintenance",
      showLoadingAction: "showLoadingAction",
      endLoadingAction: "endLoadingAction",
    }),
    showRoom($data) {
      this.$router.push("/room/" + $data.uid);
    },
    updateMaintenance($data) {
      try {
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
            this.refreshPage();
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
    deleteMaintenance($isConfirmed, $uid) {
      if ($isConfirmed) {
        this.$Progress.start();
        this.showLoadingAction();
        this.deleteMaintenanceAction({ uid: $uid })
          .then((data) => {
            Toast.fire({
              icon: "success",
              title: "Successful Deleted. ",
            });
            this.$Progress.finish();
            this.endLoadingAction();
            this.$router.push("/maintenances");
          })
          .catch((error) => {
            Toast.fire({
              icon: "warning",
              title: "Fail to delete the maintenance!!!!! ",
            });
            this.$Progress.finish();
            this.endLoadingAction();
          });
      }
    },
    refreshPage() {
      location.reload();
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
    <v-content
      :class="helpers.managementStyles().backgroundClass"
      v-if="
        helpers.isAccessible(_.get(role, ['name']), 'roomMaintenance', 'view')
      "
    >
      <v-container>
        <loading></loading>
        <v-card
          class="ma-2"
          :color="helpers.managementStyles().formCardColor"
          raised
        >
          <v-card-title
            class="ma-2"
            :class="helpers.managementStyles().titleClass"
            >Maintenance - {{ data.uid }}</v-card-title
          >
          <v-divider
            class="mx-3"
            :color="helpers.managementStyles().dividerColor"
          ></v-divider>
          <v-container>
            <v-row justify="start" align="center" class="pa-2">
              <v-col cols="12">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Room</label>
                  <div class="form-control-plaintext">
                    <v-chip class="ma-2" @click="helpers.isAccessible(_.get(role, ['name']), 'room', 'view') ? showRoom(data.room) : null">
                      <h4 class="text-center ma-2">{{ _.get(data , ['room', 'unit']) || 'N/A' }}</h4>
                    </v-chip>
                  </div>
                </div>
              </v-col>
            </v-row>

            <v-divider
              class="mx-3"
              :color="helpers.managementStyles().dividerColor"
            ></v-divider>

            <v-row justify="start" align="center" class="pa-2">
              <v-col cols="12">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Property</label>
                  <div class="form-control-plaintext">
                    <v-chip class="ma-2">
                      <h4 class="text-center ma-2">
                        {{ _.get(data , ['property', 'name']) || 'N/A' | capitalizeFirstLetter }}
                      </h4>
                    </v-chip>
                  </div>
                </div>
              </v-col>
            </v-row>

            <v-divider
              class="mx-3"
              :color="helpers.managementStyles().dividerColor"
              v-if="_.isPlainObject(data.owner) && !_.isEmpty(data.owner)"
            ></v-divider>

            <v-row
              justify="start"
              align="center"
              class="pa-2"
              v-if="_.isPlainObject(data.owner) && !_.isEmpty(data.owner)"
            >
              <v-col cols="12">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Claim By</label>
                  <div class="form-control-plaintext">
                    <v-chip class="ma-2">
                      <h4 class="text-center ma-2">
                        {{ data.owner.name | capitalizeFirstLetter }}
                      </h4>
                    </v-chip>
                  </div>
                </div>
              </v-col>
            </v-row>
            <v-divider
              class="mx-3"
              :color="helpers.managementStyles().dividerColor"
            ></v-divider>
            <v-row justify="start" align="center" class="pa-2">
              <v-col cols="12" md="4">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Price</label>
                  <div class="form-control-plaintext">
                    <h4>RM {{ data.price | toDouble }}</h4>
                  </div>
                </div>
              </v-col>
              <v-col cols="12" md="4">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Maintenance Type</label>
                  <div class="form-control-plaintext">
                    <h4>{{ data.maintenance_type }}</h4>
                  </div>
                </div>
              </v-col>
              <v-col cols="12" md="4">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Maintenance Status</label>
                  <div class="form-control-plaintext">
                    <h4>{{ data.maintenance_status }}</h4>
                  </div>
                </div>
              </v-col>
              <v-col cols="12" md="4">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Maintenance Date</label>
                  <div class="form-control-plaintext">
                    <h4>{{ data.maintenance_date | formatDate }}</h4>
                  </div>
                </div>
              </v-col>
              <v-col cols="12" md="12">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Remark</label>
                  <v-card tite class="pa-5 my-2">
                    <pre>
                      <div class="form-control-plaintext">{{data.remark}}</div>
                      </pre>
                  </v-card>
                </div>
              </v-col>
            </v-row>
              
              <v-divider
                class="mx-3"
                :color="helpers.managementStyles().dividerColor"
              ></v-divider>
              <v-row>
              <v-col cols="12" md="4">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Paid</label>
                  <div class="form-control-plaintext">
                    <h4>
                      {{ _.get(data, ["paid"]) ? "Yes" : "No" }}
                    </h4>
                  </div>
                </div>
              </v-col>
              <v-col cols="12" md="4">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Claim By</label>
                  <div class="form-control-plaintext">
                    <h4>
                      {{
                        _.get(data, ["claim_by_owner"])
                          ? `${_.get(data, ["owner", "name"])} (owner)`
                          : _.get(data, ["claim_by_tenant"])
                          ? `${_.get(data, ["tenant", "name"])} (tenant)`
                          : "N/A"
                      }}
                    </h4>
                  </div>
                </div>
              </v-col>
              <v-col cols="12" md="4" v-if="data.paid">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Receipt No</label>
                  <div class="form-control-plaintext">
                    <h4>
                      {{
                        _.get(data, ["receiptno"]) || 'N/A'
                      }}
                    </h4>
                  </div>
                </div>
              </v-col>
              <v-col cols="12" md="4" v-if="data.paid">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Payment Date</label>
                  <div class="form-control-plaintext">
                    <h4>
                      {{
                        _.get(data, ["paymentdate"]) || 'N/A'
                      }}
                    </h4>
                  </div>
                </div>
              </v-col>
              <v-col cols="12" md="4" v-if="data.paid">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Payment</label>
                  <div class="form-control-plaintext">
                    <h4>
                      RM {{
                        _.get(data, ["payment"]) || 0 | toDouble
                      }}
                    </h4>
                  </div>
                </div>
              </v-col>
              <v-col cols="12" md="4" v-if="data.paid">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Processing Fees</label>
                  <div class="form-control-plaintext">
                    <h4>
                      RM {{
                        _.get(data, ["processing_fees"]) || 0 | toDouble
                      }}
                    </h4>
                  </div>
                </div>
              </v-col>
              <v-col cols="12" md="4" v-if="data.paid">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Receive From</label>
                  <div class="form-control-plaintext">
                    <h4>
                      {{
                        _.get(data, ["receive_from"]) || 'N/A'
                      }}
                    </h4>
                  </div>
                </div>
              </v-col>
              <v-col cols="12" md="4" v-if="data.paid">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Issue By</label>
                  <div class="form-control-plaintext">
                    <h4>
                      {{
                        _.get(data, ["issueby", 'name']) || 'N/A'
                      }}
                    </h4>
                  </div>
                </div>
              </v-col>
            </v-row>

            <v-divider
              class="mx-3"
              :color="helpers.managementStyles().dividerColor"
            ></v-divider>
            <v-row class="pa-2" justify="end" align="center">
              <v-col cols="auto" v-if="data.paid && helpers.isAccessible(_.get(role, ['name']), 'roomMaintenance', 'print')">
                <print-maintenance-button :item="data">
                  <v-btn color="success">
                    <v-icon left>mdi-printer</v-icon>
                    Print
                  </v-btn>
                </print-maintenance-button>
              </v-col>

              <v-col cols="auto" v-else-if="helpers.isAccessible(_.get(role, ['name']), 'roomMaintenance', 'makePayment') && _.get(data , ['maintenance_status']) != 'reject'">
                <v-btn color="warning" @click="maintenancePayFormDialog = true"
                  ><v-icon left>mdi-currency-usd</v-icon>Pay</v-btn
                >
              </v-col>
              <v-col
                cols="auto"
                v-if="
                  helpers.isAccessible(
                    _.get(role, ['name']),
                    'roomMaintenance',
                    'edit'
                  )
                "
              >
                <v-btn color="success" @click="maintenanceFormDialog = true"
                  ><v-icon left>mdi-pencil</v-icon>Edit</v-btn
                >
              </v-col>
              <v-col
                cols="auto"
                v-if="
                  helpers.isAccessible(
                    _.get(role, ['name']),
                    'roomMaintenance',
                    'delete'
                  )
                "
              >
                <confirm-dialog
                  :activatorStyle="deleteButtonConfig.buttonStyle"
                  @confirmed="deleteMaintenance($event, data.uid)"
                ></confirm-dialog>
              </v-col>
            </v-row>
          </v-container>
        </v-card>

        <v-dialog
          v-model="maintenanceFormDialog"
          persistent
          hideOverlay
          max-width="600px"
        >
          <maintenance-form-1
            :roomId="
              _.get(this.data, 'roomcheck.id') ||
              _.get(this.data, 'room_check_id')
                ? _.get(this.data, `room.id`) ||
                  _.get(this.data, `room_id`) ||
                  null
                : null
            "
            :selectedData="data || {}"
            :reset="maintenanceFormDialog"
            :editMode="true"
            @cancel="maintenanceFormDialog = false"
            @submit="updateMaintenance"
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
            :uid="_.get(this.data, ['uid'])"
            @close="maintenancePayFormDialog = false"
            @updated="refreshPage()"
          >
          </maintenance-pay-form>
        </v-dialog>
      </v-container>
    </v-content>
  </v-app>
</template>
