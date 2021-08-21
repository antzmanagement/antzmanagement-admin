
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
      paidStatus: false,
      dateMenu: false,
      rooms: [],
      roomcontracts: [],
      owners: [],
      cleaningTypes: ["normal", "deep"],
      cleaningStatus: ["pending", "inprogress", "reject", "done"],
      initStatus: "pending",
      data: new Form({
        remark: "",
        price: 0,
        room: "",
        owner: "",
        tenant: "",
        cleaning_type: "normal",
        cleaning_status: "pending",
        claim_by_owner: false,
        claim_by_tenant: false,
        cleaning_date: null,
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
    selectedData: {
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
      filterRoomsAction: "filterRooms",
      getOwnersAction: "getOwners",
      getCleaningAction: "getCleaning",
      createCleaningAction: "createCleaning",
      updateCleaningAction: "updateCleaning",
      showLoadingAction: "showLoadingAction",
      endLoadingAction: "endLoadingAction",
    }),
    cancel() {
      this.$emit("cancel");
    },
    reset() {
      this.data = new Form({
        remark: "",
        price: 0,
        room: "",
        owner: "",
        tenant: "",
        cleaning_type: "normal",
        cleaning_status: "pending",
        claim_by_owner: false,
        claim_by_tenant: false,
        cleaning_date: null,
      });
      this.cleaningStatus = ["pending", "inprogress", "reject", "done"];
      this.initStatus = "pending";
    },
    init() {
      this.showLoadingAction();
      this.reset();
      let promises = [];
      if (!this.roomId) {
        promises.push(this.getRoomsAction({ pageNumber: -1, pageSize: -1 }));
      } else {
        promises.push(
          this.filterRoomsAction({
            pageNumber: -1,
            pageSize: -1,
            id: this.roomId,
          })
        );
      }
      Promise.all(promises)
        .then(([roomRes]) => {
          this.rooms = roomRes.data || [];

          if (this.roomId) {
            let selectedRoom = _.find(this.rooms, (room) => {
              return room.id == this.roomId;
            });

            this.data.room = selectedRoom;
          }

          if (this.editMode && this.selectedData) {
            let cloneData = _.cloneDeep(this.selectedData) || {};
            cloneData.room = _.find(this.rooms, [
              "id",
              _.get(cloneData, `room.id`) || _.get(cloneData, `room_id`),
            ]);
            cloneData.owner = _.find(_.get(cloneData, `room.owners`), [
              "id",
              _.get(cloneData, `owner.id`) || _.get(cloneData, `owner_id`),
            ]);

            this.paidStatus = cloneData.paid;
            this.data = new Form(cloneData);

            this.initStatus = _.get(this.data, `cleaning_status`) || "pending";
          }

          if (_.get(this.data, `room.id`)) {
            this.getRoomContracts();
          }

          if (this.initStatus == "inprogress") {
            this.cleaningStatus = ["inprogress", "done"];
          } else if (this.initStatus == "reject") {
            this.cleaningStatus = ["reject"];
          } else if (this.initStatus == "done") {
            this.cleaningStatus = ["done"];
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
                this.data.tenant = _.get(
                  _.find(this.roomcontracts, [
                    "tenant.id",
                    _.get(this.selectedData, `tenant.id`) ||
                      _.get(this.selectedData, `tenant_id`),
                  ]),
                  "tenant"
                );
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
    handleSubmit() {
      let finalData = _.cloneDeep(this.data);
      try {
        finalData.room_id = _.get(finalData, `room.id`);
        finalData.tenant_id = _.get(finalData, `tenant.id`);
        finalData.owner_id = _.get(finalData, `owner.id`);
        if (!this.returnObject) {
          delete finalData.room;
          delete finalData.tenant;
          delete finalData.owner;
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
        <v-toolbar-title v-if="!editMode">Add Cleaning</v-toolbar-title>
        <v-toolbar-title v-else>Edit Cleaning</v-toolbar-title>
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
              :disabled="initStatus != 'pending'"
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
              :disabled="initStatus != 'pending'"
            ></v-text-field>
          </v-col>
          <v-col cols="12" md="6">
            <v-select
              :items="cleaningTypes"
              v-model="data.cleaning_type"
              label="Cleaning Type"
              :disabled="initStatus != 'pending'"
            ></v-select>
          </v-col>
          <v-col cols="12" md="6" v-if="editMode">
            <v-select
              :items="cleaningStatus"
              v-model="data.cleaning_status"
              label="Cleaning status"
            ></v-select>
          </v-col>
          <v-col cols="6">
            <v-datetime-picker
              :disabled="initStatus != 'pending'"
              label="Cleaning Date"
              v-model="data.cleaning_date"
              no-title
              scrollable
              timeFormat="HH:mm"
            ></v-datetime-picker>
          </v-col>
          <!-- <v-col cols="12" v-if="paidStatus && (data.claim_by_owner || data.claim_by_tenant)">
            <div>Paid Status</div>
            <v-radio-group v-model="data.paid" row>
              <v-radio label="Paid" :value="1"></v-radio>
              <v-radio label="Unpaid" :value="0"></v-radio>
            </v-radio-group>
          </v-col> -->
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
