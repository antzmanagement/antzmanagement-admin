
<script>
import Vue from "vue";
import { validationMixin } from "vuelidate";
import {
  required,
  minLength,
  maxLength,
  decimal,
} from "vuelidate/lib/validators";
import { mapActions } from "vuex";
import { _ } from "../../../common/common-function";
import moment from "moment";
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
    buttonStyle: {
      type: Object,
      default: () => ({
        block: true,
        color: "primary",
        class: "ma-1",
        text: "Add Room Check",
        icon: "",
        elevation: 5,
        isIcon: false,
      }),
    },
    dialogStyle: {
      type: Object,
      default: () => ({
        persistent: true,
        maxWidth: "",
        fullscreen: true,
        hideOverlay: true,
      }),
    },
  },
  data() {
    return {
      moment: moment,
      dialog: false,
      dateMenu: false,
      maintenanceEditMode: false,
      selectedMaintenance: {},
      cleaningEditMode: false,
      selectedCleaning: {},
      maintenanceFormDialog: false,
      cleaningFormDialog: false,
      propertyFormDialog: false,
      rooms: [],
      selectedRoom: {},
      properties: [],
      owners: [],
      categories: ["first inspection", "complain", "check out"],
      data: new Form({
        remark: "",
        room: "",
        checked_date: null,
        maintenances: [],
        cleanings: [],
      }),
      maintenanceHeaders: [
        {
          text: "Property",
        },
        { text: "Repair Type" },
        { text: "Maintenance Status" },
        { text: "Price (RM)" },
        { text: "Claimed By" },
        { text: "Maintenance Date" },
        { text: "Claimed By" },
      ],
      cleaningHeaders: [
        { text: "Cleaning Type" },
        { text: "Cleaning Status" },
        { text: "Price (RM)" },
        { text: "Claimed By" },
        { text: "Cleaning Date" },
        { text: "Actions" },
      ],
    };
  },

  validations() {
    return {
      data: {
        remark: { maxLength: maxLength(2500) },
      },
    };
  },

  computed: {
    isLoading() {
      return this.$store.getters.isLoading;
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
    uid: {
      handler: function (val, oldVal) {
        if (val) {
          console.log(val, oldVal);
          this.init(); // call it in the context of your component object
        }
      },
      deep: true,
    },
    // maintenanceFormDialog: function (val) {
    //   if (!val) {
    //     this.selectedRoom = {};
    //     this.selectedMaintenance = {};
    //     this.maintenanceEditMode = false;
    //   }
    // },
    // cleaningFormDialog: function (val) {
    //   if (!val) {
    //     this.selectedRoom = {};
    //     this.selectedCleaning = {};
    //     this.cleaningEditMode = false;
    //   }
    // },
  },
  mounted() {
    this.init();
  },
  methods: {
    ...mapActions({
      filterRoomsAction: "filterRooms",
      getRoomCheckAction: "getRoomCheck",
      createRoomCheckAction: "createRoomCheck",
      updateRoomCheckAction: "updateRoomCheck",
      showLoadingAction: "showLoadingAction",
      endLoadingAction: "endLoadingAction",
    }),
    async getAllRoomResponses(maxPage, size = this.helpers.maxPaginationSize()) {
      let promises = [];
      for (let index = 1; index <= maxPage; index++) {
        promises.push(
          this.filterRoomsAction({ pageNumber: index + 1, pageSize: size })
        );
      }
      return await Promise.all(promises)
        .then((responses) => {
          this.endLoadingAction();
          let finalData = [];
          responses.forEach((loopResponse) => {
            finalData = _.concat(
              finalData,
              _.get(loopResponse, ["data"]) || []
            );
          });

          return finalData;
        })
        .catch((err) => {
          console.log(err);
          Toast.fire({
            icon: "warning",
            title: "Something went wrong...",
          });
        });
    },
    init() {
      console.log('init');
      this.showLoadingAction();
      let promises = [];
      promises.push(this.filterRoomsAction({ pageNumber: 1, pageSize: this.helpers.maxPaginationSize() }));
      if (this.editMode) {
        promises.push(this.getRoomCheckAction({ uid: this.uid }));
      }
      Promise.all(promises)
        .then(async ([roomRes, roomCheckRes]) => {
          this.endLoadingAction();
          this.rooms = roomRes.data || [];
          if (roomRes.maximumPages > 1) {
            let appendData = await this.getAllRoomResponses(roomRes.maximumPages)
            this.rooms = _.concat(this.rooms, appendData)
          }
          if (this.uid && this.editMode && _.get(roomCheckRes, `data`)) {
            roomCheckRes.data.room = _.find(this.rooms, [
              "id",
              _.get(roomCheckRes.data, `room_id`),
            ]);
            this.data = new Form(roomCheckRes.data);
            console.log(this.data);
          }
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
    createRoomCheck() {
      this.$v.$touch(); //it will validate all fields

      if (this.$v.$invalid) {
        Toast.fire({
          icon: "warning",
          title: "Please make sure all the data is valid. ",
        });
      } else {
        this.$Progress.start();
        this.showLoadingAction();
        let finalData = _.cloneDeep(this.data);
        try {
          finalData.room_id = _.get(finalData, `room.id`);
          delete finalData.room;
        } catch (error) {
          console.log(error);
        }
        console.log(finalData);
        this.createRoomCheckAction(finalData)
          .then((data) => {
            Toast.fire({
              icon: "success",
              title: "Successful Created. ",
            });
            this.$Progress.finish();
            this.endLoadingAction();
            this.$emit("created", data.data);
            this.data.reset();
            this.dialog = false;
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
    updateRoomCheck() {
      this.$v.$touch(); //it will validate all fields

      if (this.$v.$invalid) {
        Toast.fire({
          icon: "warning",
          title: "Please make sure all the data is valid. ",
        });
      } else {
        this.$Progress.start();
        this.showLoadingAction();
        let finalData = _.cloneDeep(this.data);
        try {
          finalData.room_id = _.get(finalData, `room.id`);
          delete finalData.room;
        } catch (error) {
          console.log(error);
        }
        console.log(finalData);
        this.updateRoomCheckAction(finalData)
          .then((data) => {
            Toast.fire({
              icon: "success",
              title: "Successful Updated. ",
            });
            this.$Progress.finish();
            this.endLoadingAction();
            this.$emit("updated", data.data);
            this.dialog = false;
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
    openMaintenanceDialog(item, editMode) {
      this.selectedRoom = _.get(this.data, `room`);
      if (!_.get(this.selectedRoom, `id`)) {
        Toast.fire({
          icon: "error",
          title: "Please Select A Room First. ",
        });
      } else {
        this.maintenanceEditMode = editMode == true;
        this.selectedMaintenance = item || {};
        this.maintenanceFormDialog = true;
      }
    },
    deleteMaintenance(item) {
      console.log("delete");
      console.log(item);
      let newData =
        _.filter(_.cloneDeep(this.data.maintenances), (maintenance) => {
          return item.uid != maintenance.uid;
        }) || [];
      this.data.maintenances = newData;
    },
    updateMaintenances($data) {
      try {
        this.maintenanceFormDialog = false;
        if (!_.get($data, "uid")) {
          $data.uid = new Date().getTime();
        }

        this.data.maintenances = _.unionBy(
          [$data],
          this.data.maintenances,
          "uid"
        );
      } catch (error) {
        console.log(error);
      }
    },
    openCleaningDialog(item, editMode) {
      this.selectedRoom = _.get(this.data, `room`);
      if (!_.get(this.selectedRoom, `id`)) {
        Toast.fire({
          icon: "error",
          title: "Please Select A Room First. ",
        });
      } else {
        this.cleaningEditMode = editMode == true;
        this.selectedCleaning = item || {};
        this.cleaningFormDialog = true;
      }
    },
    deleteCleaning(item) {
      let newData =
        _.filter(_.cloneDeep(this.data.cleanings), (cleaning) => {
          return item.uid != cleaning.uid;
        }) || [];
      this.data.cleanings = newData;
    },
    updateCleanings($data) {
      try {
        this.cleaningFormDialog = false;
        if (!_.get($data, "uid")) {
          $data.uid = new Date().getTime();
        }

        this.data.cleanings = _.unionBy([$data], this.data.cleanings, "uid");
      } catch (error) {
        console.log(error);
      }
    },
  },
};
</script>

<template>
  <v-dialog
    v-model="dialog"
    :fullscreen="dialogStyle.fullscreen"
    :hide-overlay="dialogStyle.hideOverlay"
    :persistent="dialogStyle.persistent"
    :max-width="dialogStyle.maxWidth"
    transition="dialog-bottom-transition"
  >
    <template v-slot:activator="{ on }">
      <v-btn
        :class="buttonStyle.class"
        tile
        :color="buttonStyle.color"
        :block="buttonStyle.block"
        :elevation="buttonStyle.elevation"
        v-on="on"
        :icon="buttonStyle.isIcon"
        :disabled="isLoading"
      >
        <v-icon>{{ buttonStyle.icon }}</v-icon>
        {{ buttonStyle.text }}
      </v-btn>
    </template>
    <v-card>
      <v-toolbar dark color="primary">
        <v-btn icon dark @click="dialog = false">
          <v-icon>mdi-close</v-icon>
        </v-btn>
        <v-toolbar-title v-if="!editMode">Add Room Check</v-toolbar-title>
        <v-toolbar-title v-else>Edit Room Check</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-toolbar-items>
          <v-btn
            dark
            text
            :disabled="isLoading"
            @click="editMode ? updateRoomCheck() : createRoomCheck()"
            >Save</v-btn
          >
        </v-toolbar-items>
      </v-toolbar>
      <v-card-text>
        <v-container>
          <v-row>
            <v-col cols="12">
              <v-autocomplete
                v-model="data.room"
                item-text="name"
                item-value="id"
                :items="rooms || []"
                label="Room"
                chips
                return-object
              >
              </v-autocomplete>
            </v-col>
            <v-col cols="12" md="6">
              <v-select
                :items="categories"
                v-model="data.category"
                label="Category"
              ></v-select>
            </v-col>
            <v-col cols="6">
              <v-datetime-picker
                label="Checked Date"
                v-model="data.checked_date"
                no-title
                scrollable
                timeFormat="HH:mm"
              ></v-datetime-picker>
            </v-col>
            <v-col cols="12" md="12">
              <div class="align-center d-flex">
                <span class="d-inline-block mr-10"> Maintenance </span>
                <span class="d-inline-block mr-4">
                  <v-btn @click="openMaintenanceDialog()"
                    >Add Maintenance</v-btn
                  >
                </span>
              </div>
            </v-col>
            <v-col
              cols="12"
              v-if="
                _.isArray(_.get(data, ['maintenances'])) &&
                !_.isEmpty(_.get(data, ['maintenances']))
              "
            >
              <v-data-table
                :headers="maintenanceHeaders"
                :items="_.get(data, ['maintenances']) || []"
                item-key="uid"
              >
                <template v-slot:item="props">
                  <tr :key="props.item.uid">
                    <td class="text-truncate">
                      {{
                        _.get(props.item, ["property", "name"]) == "others"
                          ? `${
                              _.get(props.item, ["property", "text"]) || ""
                            } - ${_.get(props.item, ["other_property"]) || ""}`
                          : _.get(props.item, ["property", "text"]) || "N/A"
                      }}
                    </td>
                    <td class="text-truncate">
                      {{ props.item.maintenance_type }}
                    </td>
                    <td class="text-truncate">
                      {{ props.item.maintenance_status }}
                    </td>
                    <td class="text-truncate">{{ props.item.price }}</td>
                    <td class="text-truncate">
                      {{
                        _.get(props.item, ["claim_by_owner"])
                          ? _.get(props.item, ["owner", "name"]) || "N/A"
                          : _.get(props.item, ["claim_by_tenant"])
                          ? _.get(props.item, ["tenant", "name"]) || "N/A"
                          : "N/A"
                      }}
                    </td>
                    <td class="text-truncate">
                      {{
                        _.get(props.item, ["maintenance_date"])
                          ? moment(props.item.maintenance_date).format(
                              "YYYY-MM-DD HH:mm"
                            )
                          : "N/A" || "N/A"
                      }}
                    </td>
                    <td class="text-truncate">
                      <!-- <v-icon
                        small
                        class="mr-2"
                        @click="openMaintenanceDialog(props.item, true)"
                        color="success"
                        >mdi-pencil</v-icon
                      > -->

                      <confirm-dialog
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
            </v-col>
            <v-col cols="12" md="12">
              <div class="align-center d-flex">
                <span class="d-inline-block mr-10"> Cleaning </span>
                <span class="d-inline-block mr-4">
                  <v-btn @click="openCleaningDialog()">Add Cleaning</v-btn>
                </span>
              </div>
            </v-col>

            <v-col
              cols="12"
              v-if="
                _.isArray(_.get(data, ['cleanings'])) &&
                !_.isEmpty(_.get(data, ['cleanings']))
              "
            >
              <v-data-table
                :headers="cleaningHeaders"
                :items="_.get(data, ['cleanings']) || []"
                item-key="uid"
              >
                <template v-slot:item="props">
                  <tr :key="props.item.uid">
                    <td class="text-truncate">
                      {{ props.item.cleaning_type }}
                    </td>
                    <td class="text-truncate">
                      {{ props.item.cleaning_status }}
                    </td>
                    <td class="text-truncate">{{ props.item.price }}</td>
                    <td class="text-truncate">
                      {{
                        _.get(props.item, ["claim_by_owner"])
                          ? _.get(props.item, ["owner", "name"]) || "N/A"
                          : _.get(props.item, ["claim_by_tenant"])
                          ? _.get(props.item, ["tenant", "name"]) || "N/A"
                          : "N/A"
                      }}
                    </td>
                    <td class="text-truncate">
                      {{
                        _.get(props.item, ["cleaning_date"])
                          ? moment(props.item.cleaning_date).format(
                              "YYYY-MM-DD HH:mm"
                            )
                          : "N/A" || "N/A"
                      }}
                    </td>
                    <td class="text-truncate">
                      <!-- <v-icon
                        small
                        class="mr-2"
                        @click="openCleaningDialog(props.item, true)"
                        color="success"
                        >mdi-pencil</v-icon
                      > -->

                      <confirm-dialog
                        @confirmed="$event ? deleteCleaning(props.item) : null"
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
    </v-card>

    <v-dialog
      v-model="propertyFormDialog"
      persistent
      hideOverlay
      max-width="600px"
    >
      <property-form
        :reset="propertyFormDialog"
        :editMode="false"
        @created="appendPropertyList($event)"
        @close="propertyFormDialog = false"
      ></property-form>
    </v-dialog>

    <v-dialog
      v-model="maintenanceFormDialog"
      persistent
      hideOverlay
      max-width="600px"
    >
      <maintenance-form-1
        returnObject
        :roomId="_.get(selectedRoom, `id`) || null"
        :selectedData="selectedMaintenance || {}"
        :reset="maintenanceFormDialog"
        :editMode="maintenanceEditMode"
        @cancel="maintenanceFormDialog = false"
        @submit="updateMaintenances"
      >
      </maintenance-form-1>
    </v-dialog>

    <v-dialog
      v-model="cleaningFormDialog"
      persistent
      hideOverlay
      max-width="600px"
    >
      <cleaning-form-1
        returnObject
        :roomId="_.get(selectedRoom, `id`) || null"
        :selectedData="selectedCleaning || {}"
        :reset="cleaningFormDialog"
        :editMode="cleaningEditMode"
        @cancel="cleaningFormDialog = false"
        @submit="updateCleanings"
      >
      </cleaning-form-1>
    </v-dialog>
  </v-dialog>
</template>
