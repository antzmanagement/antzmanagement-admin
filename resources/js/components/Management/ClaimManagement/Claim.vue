
<script>
import { mapActions } from "vuex";
import { _ } from "../../../common/common-function";
export default {
  data: () => ({
    _: _,
    claimFormDialog: false,
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
      claim: [],
    }),
    rentalPaymentHeaders: [
      {
        text: "Sequence No",
      },
      {
        text: "Tenant",
      },
      {
        text: "Room Contract",
      },
      {
        text: "Room",
      },
      {
        text: "Rental Date",
      },
      {
        text: "Rental Price",
      },
      {
        text: "Penalty",
      },
      {
        text: "Processing Fees",
      },
      {
        text: "Claim Amount",
      },
      {
        text: "Payment Date",
      },
    ],
    maintenanceHeaders: [
      {
        text: "id",
      },
      {
        text: "Unit No",
      },
      {
        text: "Property",
      },
      { text: "Repair Type" },
      { text: "Maintenance Status" },
      { text: "Price (RM)" },
      { text: "Claim Amount (RM)" },
      { text: "Created At" },
    ],
  }),

  computed: {
    isLoading() {
      return this.$store.getters.isLoading;
    },
    totalRentalClaim() {
      console.log(this.data.rentalpayments);
      let total = 0;
      if (
        _.isArray(this.data.rentalpayments) &&
        !_.isEmpty(this.data.rentalpayments)
      ) {
        _.forEach(this.data.rentalpayments, function (item) {
          total += parseFloat(_.get(item, "claim_amount") || 0);
        });
      }
      return total;
    },
    totalMaintenanceClaim() {
      console.log(this.data.maintenances);
      let maintenanceTotal = 0;
      if (
        _.isArray(this.data.maintenances) &&
        !_.isEmpty(this.data.maintenances)
      ) {
        _.forEach(this.data.maintenances, function (item) {
          maintenanceTotal += parseFloat(_.get(item, "claim_amount") || 0);
        });
      }
      return maintenanceTotal;
    },
  },
  created() {
    this.$Progress.start();
    this.showLoadingAction();
    this.getClaimAction({ uid: this.$route.params.uid })
      .then((data) => {
        this.data = data.data;
        console.log(this.data);
        this.$Progress.finish();
        this.endLoadingAction();
      })
      .catch((error) => {
        Toast.fire({
          icon: "warning",
          title: "Fail to retrieve the claim!!!!! ",
        });
        this.$Progress.finish();
        this.endLoadingAction();
      });
  },

  methods: {
    ...mapActions({
      getClaimAction: "getClaim",
      deleteClaimAction: "deleteClaim",
      showLoadingAction: "showLoadingAction",
      endLoadingAction: "endLoadingAction",
    }),
    goToOwner(uid) {
      this.$router.push(`/owner/${uid}`);
    },
    deleteClaim($isConfirmed, $uid) {
      if ($isConfirmed) {
        this.$Progress.start();
        this.showLoadingAction();
        this.deleteClaimAction({ uid: $uid })
          .then((data) => {
            Toast.fire({
              icon: "success",
              title: "Successful Deleted. ",
            });
            this.$Progress.finish();
            this.endLoadingAction();
            this.$router.push("/claims");
          })
          .catch((error) => {
            Toast.fire({
              icon: "warning",
              title: "Fail to delete the claim!!!!! ",
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
    <navbar  :returnRole="(role) => { this.role = role}"></navbar>
    <v-content :class="helpers.managementStyles().backgroundClass">
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
            >Claim - {{ data.uid }}</v-card-title
          >
          <v-divider
            class="mx-3"
            :color="helpers.managementStyles().dividerColor"
          ></v-divider>
          <v-container>
            <v-row justify="start" align="center" class="pa-2">
              <v-col cols="12">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Owner</label>
                  <div class="form-control-plaintext">
                    <v-chip
                      class="ma-2"
                      @click="goToOwner(_.get(data, 'owner.uid'))"
                    >
                      <h4 class="text-center ma-2">
                        {{
                          _.get(data, "owner.name") ||
                          "" | capitalizeFirstLetter
                        }}
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
              <v-col cols="4">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Admin Fees</label>
                  <div class="form-control-plaintext">
                    <h4>
                      RM {{ _.get(data, "admin_fees") || 0 | toDouble }}
                    </h4>
                  </div>
                </div>
              </v-col>
              <v-col cols="4">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Other Fees</label>
                  <div class="form-control-plaintext">
                    <h4 >
                      RM {{ _.get(data, "other_fees") || 0 | toDouble }}
                    </h4>
                  </div>
                </div>
              </v-col>
              <v-col cols="4">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Rental Fees</label>
                  <div class="form-control-plaintext">
                    <h4 >
                      RM {{ totalRentalClaim | toDouble }}
                    </h4>
                  </div>
                </div>
              </v-col>
              <v-col cols="4">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Maintenance Fees</label>
                  <div class="form-control-plaintext">
                    <h4 >
                      RM {{ totalMaintenanceClaim || 0 | toDouble }}
                    </h4>
                  </div>
                </div>
              </v-col>
            </v-row>

            <v-divider
              class="mx-3"
              :color="helpers.managementStyles().dividerColor"
              v-if="
                _.isArray(data.rentalpayments) &&
                !_.isEmpty(data.rentalpayments)
              "
            ></v-divider>

            <v-row
              v-if="
                _.isArray(data.rentalpayments) &&
                !_.isEmpty(data.rentalpayments)
              "
            >
              <v-col cols="12">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Rental Payments</label>
                  <div class="form-control-plaintext">
                    <v-data-table
                      :headers="rentalPaymentHeaders"
                      :items="data.rentalpayments"
                      item-key="uid"
                      class="elevation-1"
                    >
                      <template v-slot:item="props">
                        <tr>
                          <td>{{ props.item.sequence }}</td>
                          <td>{{ props.item.roomcontract.tenant.name }}</td>
                          <td>{{ props.item.roomcontract.name }}</td>
                          <td>{{ props.item.roomcontract.room.name }}</td>
                          <td>{{ props.item.rentaldate | formatDate }}</td>
                          <td>{{ props.item.price | toDouble }}</td>
                          <td>{{ props.item.penalty | toDouble }}</td>
                          <td>{{ props.item.processing_fees | toDouble }}</td>
                          <td>{{ props.item.claim_amount | toDouble }}</td>
                          <td>{{ props.item.paymentdate | formatDate }}</td>
                        </tr>
                      </template>
                      <template v-slot:footer>
                        <div
                          class="px-4 d-flex justify-end align-center w-100 border-blue mt-3"
                        >
                          <span class="d-inline-block h6 bold mr-2">
                            Total Claim Amount :
                          </span>
                          <span class="d-inline-block h6 bold">
                            RM {{ totalRentalClaim | toDouble }}
                          </span>
                        </div>
                      </template>
                    </v-data-table>
                  </div>
                </div>
              </v-col>
            </v-row>
            <v-divider
              class="mx-3"
              :color="helpers.managementStyles().dividerColor"
              v-if="
                _.isArray(data.rentalpayments) &&
                !_.isEmpty(data.rentalpayments)
              "
            ></v-divider>

            <v-row
              v-if="
                _.isArray(data.rentalpayments) &&
                !_.isEmpty(data.rentalpayments)
              "
            >
              <v-col cols="12">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Maintenances</label>
                  <div class="form-control-plaintext">
                    <v-data-table
                      :headers="maintenanceHeaders"
                      :items="data.maintenances"
                      item-key="uid"
                      class="elevation-1"
                    >
                      <template v-slot:item="props">
                        <tr>
                          <td>{{ props.item.id }}</td>
                          <td>{{ props.item.room.name }}</td>
                          <td>{{ props.item.property.name }}</td>
                          <td>{{ props.item.maintenance_type }}</td>
                          <td>{{ props.item.maintenance_status }}</td>
                          <td>{{ props.item.price }}</td>
                          <td>{{ props.item.claim_amount }}</td>
                          <td>{{ props.item.created_at | formatDate }}</td>
                        </tr>
                      </template>
                      <template v-slot:footer>
                        <div
                          class="px-4 d-flex justify-end align-center w-100 border-blue mt-3"
                        >
                          <span class="d-inline-block h6 bold mr-2">
                            Total Claim Amount :
                          </span>
                          <span class="d-inline-block h6 bold">
                            RM {{ totalMaintenanceClaim | toDouble }}
                          </span>
                        </div>
                      </template>
                    </v-data-table>
                  </div>
                </div>
              </v-col>
            </v-row>
            <v-divider
              class="mx-3"
              :color="helpers.managementStyles().dividerColor"
            ></v-divider>

            <v-row justify="start" align="center" class="pa-2">
              <v-col cols="12" md="12">
                <div class="form-group mb-0">
                  <label class="form-label mb-0">Description</label>
                  <v-card tite class="pa-5 my-2">
                    <pre
                      style="height: 200px; overflow: auto"
                      class="d-inline-block form-control-plaintext"
                      >{{ data.desc }}
                      </pre
                    >
                  </v-card>
                </div>
              </v-col>
            </v-row>

            <v-divider
              class="mx-3"
              :color="helpers.managementStyles().dividerColor"
            ></v-divider>
            <v-row class="pa-2" justify="end" align="center">
              <v-col cols="auto">
                <v-btn
                  color="success"
                  class="ma-3"
                  @click="claimFormDialog = true"
                >
                  <v-icon left>mdi-pencil</v-icon>Edit
                </v-btn>
              </v-col>
              <v-col cols="auto">
                <confirm-dialog
                  :activatorStyle="deleteButtonConfig.buttonStyle"
                  @confirmed="deleteClaim($event, data.uid)"
                ></confirm-dialog>
              </v-col>
            </v-row>
          </v-container>
        </v-card>
      </v-container>

      <v-dialog v-model="claimFormDialog" persistent fullscreen hideOverlay>
        <claim-form
          :reset="claimFormDialog"
          :editMode="true"
          :uid="this.$route.params.uid"
          @updated="refreshPage()"
          @close="claimFormDialog = false"
        ></claim-form>
      </v-dialog>
    </v-content>
  </v-app>
</template>
