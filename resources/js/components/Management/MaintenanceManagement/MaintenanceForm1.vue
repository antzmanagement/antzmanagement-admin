
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
    selectedData: {
      type: Object,
      default: {},
    },
    returnObject: {
      type: Boolean,
      default: false,
    },

    roomId: {
      type: Number,
      default: () => null,
    },
  },
  data() {
    return {
      propertyFormDialog: false,
      paidStatus : false,
      dateMenu: false,
      rooms: [],
      properties: [],
      roomcontracts: [],
      owners: [],
      maintenanceTypes: ["repair", "renew"],
      maintenanceStatus: ["pending", "inprogress", "reject", "done"],
      data: new Form({
        remark: "",
        price: 0,
        room: "",
        property: "",
        owner: "",
        tenant: "",
        maintenance_type: "repair",
        maintenance_status: "pending",
        claim_by_owner: false,
        claim_by_tenant: false,
        maintenance_date: null,
      }),
    };
  },

  validations() {
    return {
      data: {
        remark: { maxLength: maxLength(2500) },
        price: { required, decimal },
      },
    };
  },

  computed: {
    isLoading() {
      return this.$store.getters.isLoading;
    },
    roomTenants() {
      if (_.isArray(this.roomcontracts) && !_.isEmpty(this.roomcontracts)) {
        let tenants = _.uniqBy(
          _.map(
            _.filter(this.roomcontracts, (roomcontract) => {
              return _.get(roomcontract, `status`) == true;
            }),
            "tenant"
          ),
          "id"
        );
        return _.isArray(tenants) && !_.isEmpty(tenants) ? tenants : [];
      } else {
        return [];
      }
    },
    priceErrors() {
      const errors = [];
      if (!this.$v.data.price.$dirty) {
        return errors;
      }

      if (!this.$v.data.price.required) {
        errors.push("Price is required");
        return errors;
      }

      if (!this.$v.data.price.decimal) {
        errors.push("Price should be decimal");
        return errors;
      }
    },
    remarkErrors() {
      const errors = [];
      if (!this.$v.data.remark.$dirty) {
        return errors;
      }

      if (!this.$v.data.remark.maxLength) {
        errors.push("Remark should be less than 2500 characters");
        return errors;
      }
    },
  },
  watch: {
    roomId: {
      handler: function (val, oldVal) {
        if (val) {
          this.init(); // call it in the context of your component object
        }
      },
      deep: true,
    },
  },
  mounted() {
    this.init();
  },
  methods: {
    ...mapActions({
      getRoomsAction: "getRooms",
      filterRoomContractsAction: "filterRoomContracts",
      getOwnersAction: "getOwners",
      getPropertiesAction: "getProperties",
      getMaintenanceAction: "getMaintenance",
      createMaintenanceAction: "createMaintenance",
      updateMaintenanceAction: "updateMaintenance",
      showLoadingAction: "showLoadingAction",
      endLoadingAction: "endLoadingAction",
    }),
    cancel() {
      this.$emit("cancel");
    },
    init() {
      this.showLoadingAction();
      let promises = [];
      promises.push(this.getRoomsAction({ pageNumber: -1, pageSize: -1 }));
      promises.push(this.getPropertiesAction({ pageNumber: -1, pageSize: -1 }));
      promises.push(this.getOwnersAction({ pageNumber: -1, pageSize: -1 }));

      Promise.all(promises)
        .then(([roomRes, propertyRes, ownerRes]) => {
          this.rooms = roomRes.data || [];
          this.owners = ownerRes.data || [];
          this.properties = propertyRes.data || [];

          if (this.roomId) {
            let selectedRoom = _.find(this.rooms, (room) => {
              return room.id == this.roomId;
            });

            this.data.room = selectedRoom;
            this.getRoomContracts();
          }

          if (this.editMode && this.selectedData) {
            this.selectedData.room = _.find(this.rooms, [
              "id",
              _.get(this.selectedData, `room.id`) ||
                _.get(this.selectedData, `room_id`),
            ]);
            this.selectedData.property = _.find(this.properties, [
              "id",
              _.get(this.selectedData, `property.id`) ||
                _.get(this.selectedData, `property_id`),
            ]);
            this.selectedData.owner = _.find(this.owners, [
              "id",
              _.get(this.selectedData, `owner.id`) ||
                _.get(this.selectedData, `owner_id`),
            ]);

            this.paidStatus = this.selectedData.paid;
            this.data = new Form(this.selectedData);
          }
          this.endLoadingAction();
        })
        .catch((error) => {
          this.endLoadingAction();
          console.log(error);
          Toast.fire({
            icon: "warning",
            title: "Something went wrong...",
          });
        });
    },
    onChangeOwnerClaimSwitch() {
      if (_.get(this.data, `claim_by_owner`)) {
        this.data.owner = _.get(this.data.room, `owners[0]`);
        this.data.claim_by_tenant = false;
        this.data.tenant = {};
      } else {
        this.data.owner = null;
      }
    },
    onChangeTenantClaimSwitch() {
      if (_.get(this.data, `claim_by_tenant`)) {
        this.data.claim_by_owner = false;
        this.data.owner = null;
        this.getRoomContracts();
      } else {
        this.data.tenant = {};
      }
    },
    getRoomContracts() {
      if (_.get(this.data, `room.id`)) {
        this.filterRoomContractsAction({
          room_id: _.get(this.data.room, `id`),
          pageNumber: -1,
          pageSize: -1,
        })
          .then((data) => {
            if (data.data) {
              this.roomcontracts = data.data;
              if (this.editMode && this.selectedData) {
                this.data.tenant = _.get(_.find(this.roomcontracts, [
                  "tenant.id",
                  _.get(this.selectedData, `tenant.id`) ||
                    _.get(this.selectedData, `tenant_id`),
                ]), 'tenant');
              } else {
                this.data.tenant = _.get(data.data, `[0].tenant`);
              }
            } else {
              this.roomcontracts = [];
              this.data.tenant = null;
            }
          })
          .catch((error) => {
            console.log(error);
            Toast.fire({
              icon: "warning",
              title: "Something went wrong... ",
            });
          });
      } else {
        this.roomcontracts = [];
        this.data.tenant = {};
      }
    },
    reset() {
      this.data.reset();
    },
    handleSubmit() {
      let finalData = _.cloneDeep(this.data);
      try {
        finalData.room_id = _.get(finalData, `room.id`);
        finalData.tenant_id = _.get(finalData, `tenant.id`);
        finalData.owner_id = _.get(finalData, `owner.id`);
        finalData.property_id = _.get(finalData, `property.id`);
        if (!this.returnObject) {
          delete finalData.room;
          delete finalData.tenant;
          delete finalData.owner;
          delete finalData.property;
        }
      } catch (error) {
        console.log(error);
      }
      this.$emit("submit", finalData);
      this.reset();
    },
  },
};
</script>

<template>
  <v-card>
    <slot name="header">
      <v-toolbar dark color="primary">
        <v-btn icon dark @click="cancel()">
          <v-icon>mdi-close</v-icon>
        </v-btn>
        <v-toolbar-title v-if="!editMode">Add Maintenance</v-toolbar-title>
        <v-toolbar-title v-else>Edit Maintenance</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-toolbar-items>
          <v-btn dark text :disabled="isLoading" @click="handleSubmit()"
            >Save</v-btn
          >
        </v-toolbar-items>
      </v-toolbar>
    </slot>
    <v-card-text>
      <v-container>
        <v-row>
          <v-col cols="12" v-show="!roomId">
            <v-autocomplete
              v-model="data.room"
              item-text="name"
              :items="rooms || []"
              label="Room"
              chips
              return-object
            >
            </v-autocomplete>
          </v-col>
          <v-col
            cols="6"
            md="6"
            v-if="
              _.isArray(_.get(data, ['room', 'owners'])) &&
              !_.isEmpty(_.get(data, ['room', 'owners']))
            "
          >
            <v-switch
              v-model="data.claim_by_owner"
              :label="`Claimed By Owner`"
              @change="onChangeOwnerClaimSwitch()"
            ></v-switch>
          </v-col>
          <v-col
            cols="6"
            md="6"
            v-if="_.isArray(roomTenants) && !_.isEmpty(roomTenants)"
          >
            <v-switch
              v-model="data.claim_by_tenant"
              :label="`Claimed By Tenant`"
              @change="onChangeTenantClaimSwitch()"
            ></v-switch>
          </v-col>
          <v-col cols="12" v-show="data.claim_by_owner">
            <v-autocomplete
              v-model="data.owner"
              item-text="name"
              :items="_.get(data, ['room', 'owners']) || []"
              label="Owners"
              return-object
            >
            </v-autocomplete>
          </v-col>
          <v-col cols="12" v-show="data.claim_by_tenant">
            <v-autocomplete
              v-model="data.tenant"
              item-text="name"
              :items="roomTenants"
              label="Tenants"
              return-object
            >
            </v-autocomplete>
          </v-col>
          <v-col cols="12">
            <v-autocomplete
              v-model="data.property"
              :item-text="(item) => helpers.capitalizeFirstLetter(item.name)"
              :items="properties || []"
              label="Property"
              chips
              return-object
            >
            </v-autocomplete>
          </v-col>
          <v-col cols="12" md="12">
            <v-text-field
              label="Price"
              type="number"
              step="0.01"
              required
              :maxlength="300"
              v-model="data.price"
              @input="$v.data.price.$touch()"
              @blur="$v.data.price.$touch()"
              :error-messages="priceErrors"
            ></v-text-field>
          </v-col>
          <v-col cols="12" md="6">
            <v-select
              :items="maintenanceTypes"
              v-model="data.maintenance_type"
              label="Maintenance Type"
            ></v-select>
          </v-col>
          <v-col cols="12" md="6">
            <v-select
              :items="maintenanceStatus"
              v-model="data.maintenance_status"
              label="Maintenance status"
            ></v-select>
          </v-col>
          <v-col cols="6">
            <v-menu
              ref="menu"
              v-model="dateMenu"
              :close-on-content-click="false"
              transition="scale-transition"
              offset-y
            >
              <template v-slot:activator="{ on }">
                <v-text-field
                  v-model="data.maintenance_date"
                  label="Maintenance Date"
                  prepend-icon="event"
                  readonly
                  v-on="on"
                ></v-text-field>
              </template>
              <v-date-picker
                v-model="data.maintenance_date"
                no-title
                scrollable
              ></v-date-picker>
            </v-menu>
          </v-col>
          <v-col cols="12" v-if="paidStatus && (data.claim_by_owner || data.claim_by_tenant)">
            <div>Paid Status</div>
            <v-radio-group v-model="data.paid" row>
              <v-radio label="Paid" :value="1"></v-radio>
              <v-radio label="Unpaid" :value="0"></v-radio>
            </v-radio-group>
          </v-col>
          <v-col cols="12">
            <v-textarea
              label="Remark"
              :maxlength="2500"
              v-model="data.remark"
              @input="$v.data.remark.$touch()"
              @blur="$v.data.remark.$touch()"
              :error-messages="remarkErrors"
            ></v-textarea>
          </v-col>
        </v-row>
      </v-container>
    </v-card-text>

    <slot name="footer"> </slot>
  </v-card>
</template>
