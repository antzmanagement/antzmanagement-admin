<script>
import { validationMixin } from "vuelidate";
import {
  required,
  minLength,
  maxLength,
  decimal,
} from "vuelidate/lib/validators";
import { mapActions } from "vuex";
import { _ } from "../../../common/common-function";
export default {
  props: {
    editMode: {
      type: Boolean,
      default: false,
    },
    uid: {
      type: String,
      default: "",
    },
    reset: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      _: _,
      maintenances: [],
      rentalpayments: [],
      owners: [],
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
      data: new Form({
        rentalpayments: [],
        maintenances: [],
        maintenance_fees: 0,
        admin_fees: 0,
        other_fees: 0,
        rental_fees: 0,
      }),
    };
  },

  validations() {
    return {
      data: {},
    };
  },

  computed: {
    isLoading() {
      return this.$store.getters.isLoading;
    },
    totalRentalClaim() {
      console.log(this.data.rentalpayments);
      let total = 0;
      if (_.isArray(this.data.rentalpayments) && !_.isEmpty(this.data.rentalpayments)) {
        _.forEach(this.data.rentalpayments, function(item) { 
         total += parseFloat(_.get(item,'claim_amount') || 0); 
        })
      }
      return total;
    },
    totalMaintenanceClaim() {
      console.log(this.data.maintenances);
      let maintenanceTotal = 0;
      if (_.isArray(this.data.maintenances) && !_.isEmpty(this.data.maintenances)) {
        _.forEach(this.data.maintenances, function(item) { 
         maintenanceTotal += parseFloat(_.get(item,'claim_amount') || 0); 
        })
      }
      return maintenanceTotal;
    },
  },
  watch: {
    reset: function (val) {
      if (val) {
        this.data.reset();
        this.$v.$reset();
      }
    },
  },
  mounted() {
    this.getOwnersAction({ pageNumber: -1, pageSize: -1 })
      .then((res) => {
        this.owners = res.data;
      })
      .catch((err) => {
        Toast.fire({
          icon: "warning",
          title: "Something went wrong...",
        });
        this.endLoadingAction();
      });

    if (this.editMode) {
      this.showLoadingAction();
      this.getClaimAction({ uid: this.uid })
        .then((data) => {
          this.data = new Form(data.data);
          this.rentalpayments = data.data.rentalpayments || [];
          this.maintenances = data.data.maintenances || [];
          this.endLoadingAction();
        })
        .catch((error) => {
          this.endLoadingAction();
          Toast.fire({
            icon: "warning",
            title: "Something went wrong...",
          });
        });
    }
  },
  methods: {
    ...mapActions({
      getClaimAction: "getClaim",
      getOwnersAction: "getOwners",
      getUnclaimRentalPaymentsAction: "getUnclaimRentalPayments",
      getUnclaimMaintenancesAction: "getUnclaimMaintenances",
      createClaimAction: "createClaim",
      updateClaimAction: "updateClaim",
      showLoadingAction: "showLoadingAction",
      endLoadingAction: "endLoadingAction",
    }),
    close() {
      this.$emit("close");
    },
    getUnclaimData() {
      this.getUnclaimMaintenances(this.data.owner);
      this.getUnclaimRentalPayments(this.data.owner);
    },
    getUnclaimRentalPayments(uid) {
      if (uid) {
        this.getUnclaimRentalPaymentsAction({ uid: uid })
          .then((res) => {
            this.rentalpayments = _.map(res.data, function (item) {
              item.claim_amount = _.get(item, "roomcontract.room.sublet")
                ? _.get(item, "roomcontract.room.sublet_claim") || 0
                : _.get(item, "roomcontract.room.owner_claim") || 0;
              return item;
            });
            this.data.rentalpayments = this.rentalpayments;
          })
          .catch((err) => {
            // Toast.fire({
            //   icon: "warning",
            //   title: "Something went wrong...",
            // });
            this.endLoadingAction();
          });
      }
    },
    getUnclaimMaintenances(uid) {
      if (uid) {
        this.getUnclaimMaintenancesAction({ uid: uid })
          .then((res) => {
            this.maintenances = _.map(res.data, function (item) {
              item.claim_amount = _.get(item, "price") || 0;
              return item;
            });
            this.data.maintenances = this.maintenances;
          })
          .catch((err) => {
            // Toast.fire({
            //   icon: "warning",
            //   title: "Something went wrong...",
            // });
            this.endLoadingAction();
          });
      }
    },
    createClaim() {
      this.$v.$touch(); //it will validate all fields

      if (this.$v.$invalid) {
        Toast.fire({
          icon: "warning",
          title: "Please make sure all the data is valid. ",
        });
      } else {
        this.$Progress.start();
        this.showLoadingAction();
        this.createClaimAction(this.data)
          .then((data) => {
            Toast.fire({
              icon: "success",
              title: "Successful Created. ",
            });
            this.$Progress.finish();
            this.endLoadingAction();
            this.$emit("created", data.data);
            this.close();
          })
          .catch((error) => {
            Toast.fire({
              icon: "error",
              title: "Something went wrong. ",
            });
            this.$Progress.finish();
            this.endLoadingAction();
          });
      }
    },


    updateClaim() {
      this.$v.$touch(); //it will validate all fields

      if (this.$v.$invalid) {
        Toast.fire({
          icon: "warning",
          title: "Please make sure all the data is valid. ",
        });
      } else {
        this.$Progress.start();
        this.showLoadingAction();
        this.updateClaimAction(this.data)
          .then((data) => {
            Toast.fire({
              icon: "success",
              title: "Successful Updated. ",
            });
            this.$Progress.finish();
            this.endLoadingAction();
            this.$emit("updated", data.data);
            this.close();
          })
          .catch((error) => {
            Toast.fire({
              icon: "error",
              title: "Something went wrong. ",
            });
            this.$Progress.finish();
            this.endLoadingAction();
          });
      }
    },
  },
};
</script>

<template>
  <v-card>
    <v-toolbar dark color="primary">
      <v-btn icon dark @click="close()">
        <v-icon>mdi-close</v-icon>
      </v-btn>
      <v-toolbar-title v-if="!editMode">Add Claim</v-toolbar-title>
      <v-toolbar-title v-else>Edit Claim</v-toolbar-title>
      <v-spacer></v-spacer>
      <v-toolbar-items>
        <v-btn
          dark
          text
          :disabled="isLoading"
          @click="editMode ? updateClaim() : createClaim()"
          >Save</v-btn
        >
      </v-toolbar-items>
    </v-toolbar>
    <v-card-text>
      <v-container>
        <v-row>
          <v-col cols="12" md="12">
            <v-autocomplete
              v-model="data.owner"
              :item-text="(item) => helpers.capitalizeFirstLetter(item.name)"
              item-value="uid"
              :items="owners || []"
              label="Owner"
              :disabled="editMode"
              @change="getUnclaimData()"
            >
            </v-autocomplete>
          </v-col>
          <v-col cols="12" md="12" v-if="_.isArray(rentalpayments) && !_.isEmpty(rentalpayments)">
            <v-data-table
              v-model="data.rentalpayments"
              :headers="rentalPaymentHeaders"
              :items="rentalpayments"
              show-select
              class="elevation-1"
            >
              <template v-slot:item="props">
                <tr>
                  <td class="text-truncate">
                    <v-checkbox
                      :input-value="props.isSelected"
                      @change="props.select($event)"
                    ></v-checkbox>
                  </td>
                  <td class="text-truncate">{{ props.item.sequence }}</td>
                  <td class="text-truncate">{{ props.item.roomcontract.tenant.name }}</td>
                  <td class="text-truncate">{{ props.item.roomcontract.name }}</td>
                  <td class="text-truncate">{{ props.item.roomcontract.room.name }}</td>
                  <td class="text-truncate">{{ props.item.rentaldate | formatDate }}</td>
                  <td class="text-truncate">{{ props.item.price | toDouble }}</td>
                  <td class="text-truncate">{{ props.item.penalty | toDouble }}</td>
                  <td class="text-truncate">{{ props.item.processing_fees | toDouble }}</td>
                  <td class="text-truncate">
                    <v-text-field
                      type="number"
                      step="0.01"
                      required
                      :maxlength="300"
                      v-model="props.item.claim_amount"
                    ></v-text-field>
                  </td>
                  <td class="text-truncate">{{ props.item.paymentdate | formatDate }}</td>
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
                    RM {{totalRentalClaim | toDouble}}
                  </span>
                </div>
              </template>
            </v-data-table>
          </v-col>
          <v-col cols="12" md="12" v-if="_.isArray(maintenances) && !_.isEmpty(maintenances)">
            <v-data-table
              v-model="data.maintenances"
              :headers="maintenanceHeaders"
              :items="maintenances"
              show-select
              item-key="uid"
              class="elevation-1"
            >
              <template v-slot:item="props">
                <tr>
                  <td class="text-truncate">
                    <v-checkbox
                      :input-value="props.isSelected"
                      @change="props.select($event)"
                    ></v-checkbox>
                  </td>
                  <td class="text-truncate">{{ props.item.id }}</td>
                  <td class="text-truncate">{{ props.item.room.name }}</td>
                  <td class="text-truncate">{{ props.item.property.name }}</td>
                  <td class="text-truncate">{{ props.item.maintenance_type }}</td>
                  <td class="text-truncate">{{ props.item.maintenance_status }}</td>
                  <td class="text-truncate">{{ props.item.price }}</td>
                  <td class="text-truncate">
                    <v-text-field
                      type="number"
                      step="0.01"
                      required
                      :maxlength="300"
                      v-model="props.item.claim_amount"
                    ></v-text-field>
                  </td>
                  <td class="text-truncate">{{ props.item.created_at | formatDate }}</td>
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
                    RM {{totalMaintenanceClaim | toDouble}}
                  </span>
                </div>
              </template>
            </v-data-table>
          </v-col>
          <v-col cols="12" md="12">
            <v-text-field
              label="Admin Fees"
              type="number"
              step="0.01"
              required
              :maxlength="300"
              v-model="data.admin_fees"
            ></v-text-field>
          </v-col>
          <v-col cols="12" md="12">
            <v-text-field
              label="Other Fees"
              type="number"
              step="0.01"
              required
              :maxlength="300"
              v-model="data.other_fees"
            ></v-text-field>
          </v-col>
          <v-col cols="12" md="12">
            <v-textarea
              label="Description"
              :maxlength="2500"
              v-model="data.desc"
            ></v-textarea>
          </v-col>
        </v-row>
      </v-container>
    </v-card-text>
  </v-card>
</template>
