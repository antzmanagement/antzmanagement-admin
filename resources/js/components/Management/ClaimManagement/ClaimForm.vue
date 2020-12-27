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
            console.log(res);
            this.rentalpayments = res.data;
            this.data.rentalpayments = _.map(res.data, "id");
            this.updatePrice();
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
            console.log(res);
            this.maintenances = res.data;
            this.data.maintenances = _.map(res.data, "id");
            this.updatePrice();
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

    updatePrice() {
      let totalRental = 0;
      let totalMaintenance = 0;
      let self = this;

      _.forEach(this.data.maintenances, function (maintenance) {
        let selected = _.find(self.maintenances, function (v) {
          return v.id == maintenance;
        });
        totalMaintenance += parseFloat(_.get(selected, ["price"])) || 0;
      });

      _.forEach(this.data.rentalpayments, function (rentalpayment) {
        let selected = _.find(self.rentalpayments, function (v) {
          return v.id == rentalpayment;
        });
        totalRental += parseFloat(_.get(selected, ["price"])) || 0;
      });

      this.data.maintenance_fees = totalMaintenance;
      if (totaRental > 0) {
        this.data.admin_fees =
          parseFloat(totalRental) * 0.1 < 30
            ? 30
            : parseFloat(totalRental) * 0.1;
        this.data.rental_fees =
          parseFloat(totalRental) - parseFloat(this.data.admin_fees);
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
              :items="owners"
              label="Owner"
              @change="getUnclaimData()"
            >
            </v-autocomplete>
          </v-col>
          <v-col cols="12" md="12">
            <v-autocomplete
              v-model="data.rentalpayments"
              :item-text="
                (item) => {
                  return item.roomcontract.room.unit + '-' + item.paymentdate;
                }
              "
              item-value="id"
              :items="rentalpayments"
              label="Rental Payments"
              chips
              deletable-chips
              multiple
              @change="updatePrice()"
            >
            </v-autocomplete>
          </v-col>
          <v-col cols="12" md="12">
            <v-autocomplete
              v-model="data.maintenances"
              :item-text="
                (item) =>
                  helpers.capitalizeFirstLetter(_.get(item, 'name') || '')
              "
              item-value="id"
              :items="maintenances"
              label="Maintenances"
              chips
              deletable-chips
              @change="updatePrice()"
              multiple
            >
            </v-autocomplete>
          </v-col>
          <v-col cols="12" md="12">
            <v-text-field
              label="Rental Fees"
              type="number"
              step="0.01"
              required
              :maxlength="300"
              v-model="data.rental_fees"
            ></v-text-field>
          </v-col>
          <v-col cols="12" md="12">
            <v-text-field
              label="Maintenance Fees"
              type="number"
              step="0.01"
              required
              :maxlength="300"
              v-model="data.maintenance_fees"
            ></v-text-field>
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
              @input="$v.data.desc.$touch()"
              @blur="$v.data.desc.$touch()"
              :error-messages="descErrors"
            ></v-textarea>
          </v-col>
        </v-row>
      </v-container>
    </v-card-text>
  </v-card>
</template>
