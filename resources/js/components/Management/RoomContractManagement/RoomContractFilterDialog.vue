
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
    buttonStyle: {
      type: Object,
      default: () => ({
        block: true,
        color: "primary",
        class: "ma-1",
        text: "RoomContract Filter",
        icon: "",
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
      startDateFromMenu: false,
      startDateToMenu: false,
      endDateFromMenu: false,
      endDateToMenu: false,
      createdDateFromMenu: false,
      createdDateToMenu: false,
      dialog: false,
      tenants: [],
      services: [],
      owners: [],
      rooms: [],
      data: new Form({
        startDateFromDate: null,
        startDateToDate: null,
        services: [],
        room: "",
        owner: "",
        tenant: "",
        outstanding: "",
        checkedout: "",
      }),
    };
  },

  computed: {
    isLoading() {
      return this.$store.getters.isLoading;
    },
  },
  watch: {
    dialog: function (val) {
      if (val) {
        this.data.reset();
      }
    },
  },
  mounted() {
    this.showLoadingAction();
    let promises = [];
    promises.push(this.getTenantsAction({ pageNumber: -1, pageSize: -1 }));
    promises.push(this.getServicesAction({ pageNumber: -1, pageSize: -1 }));
    promises.push(this.filterRoomsAction({ pageNumber: 1, pageSize: this.helpers.maxPaginationSize() }));
    promises.push(this.getOwnersAction({ pageNumber: -1, pageSize: -1 }));

    Promise.all(promises)
      .then(async ([tenantRes, serviceRes, roomRes, ownerRes]) => {
        this.endLoadingAction();
        this.tenants = _.get(tenantRes, ["data"]) || [];
        this.services = _.get(serviceRes, ["data"]) || [];
        this.rooms = _.get(roomRes, ["data"]) || [];
        if (roomRes.maximumPages > 1) {
          let appendData = await this.getAllRoomResponses(roomRes.maximumPages);
          this.rooms = _.concat(this.rooms, appendData);
        }
        this.owners = _.get(ownerRes, ["data"]) || [];
      })
      .catch((err) => {
        console.log(err);
        Toast.fire({
          icon: "warning",
          title: "Something went wrong...",
        });
        this.endLoadingAction();
      });
  },
  methods: {
    ...mapActions({
      getServicesAction: "getServices",
      getTenantsAction: "getTenants",
      filterRoomsAction: "filterRooms",
      getOwnersAction: "getOwners",
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
          let finalData = [];
          responses.forEach((loopResponse) => {
            finalData = _.concat(
              finalData,
              _.get(loopResponse, ["data"]) || []
            );
          });

          return finalData;
          this.endLoadingAction();
        })
        .catch((err) => {
          console.log(err);
          Toast.fire({
            icon: "warning",
            title: "Something went wrong...",
          });
        });
    },
    submitFilter() {
      this.$emit("submitFilter", this.data);
      this.dialog = false;
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
        :icon="buttonStyle.isIcon"
        v-on="on"
        :disabled="isLoading"
      >
        <v-icon left>{{ buttonStyle.icon }}</v-icon>
        {{ buttonStyle.text }}
      </v-btn>
    </template>
    <v-card>
      <v-toolbar dark color="primary">
        <v-btn icon dark @click="dialog = false">
          <v-icon>mdi-close</v-icon>
        </v-btn>
        <v-toolbar-title v->RoomContract Filter</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-toolbar-items>
          <v-btn dark text :disabled="isLoading" @click="submitFilter()"
            >Apply</v-btn
          >
        </v-toolbar-items>
      </v-toolbar>
      <v-card-text>
        <v-container>
          <v-row>
            <v-col cols="6">
              <div>Room Contract Start Date</div>
              <div class="d-flex justify-start">
                <span class="d-inline-block mr-5">
                  <div class="d-flex align-center">
                    <span class="d-inline-block half-width">
                      <v-menu
                        ref="menu"
                        v-model="startDateFromMenu"
                        :close-on-content-click="false"
                        transition="scale-transition"
                        offset-y
                      >
                        <template v-slot:activator="{ on }">
                          <v-text-field
                            v-model="data.startDateFromDate"
                            label="From Date"
                            readonly
                            v-on="on"
                          ></v-text-field>
                        </template>
                        <v-date-picker
                          v-model="data.startDateFromDate"
                          no-title
                          scrollable
                          :max="data.startDateToDate"
                        ></v-date-picker>
                      </v-menu>
                    </span>
                    <span class="d-inline-block">
                      <v-icon
                        class="btn"
                        v-if="data.startDateFromDate"
                        @click="data.startDateFromDate = ''"
                        >mdi-close</v-icon
                      >
                    </span>
                  </div>
                </span>
                <span class="d-inline-block">
                  <div class="d-flex align-center">
                    <span class="d-inline-block half-width">
                      <v-menu
                        ref="menu"
                        v-model="startDateToMenu"
                        :close-on-content-click="false"
                        transition="scale-transition"
                        offset-y
                      >
                        <template v-slot:activator="{ on }">
                          <v-text-field
                            v-model="data.startDateToDate"
                            label="To Date"
                            readonly
                            v-on="on"
                          ></v-text-field>
                        </template>
                        <v-date-picker
                          v-model="data.startDateToDate"
                          no-title
                          scrollable
                          :min="data.startDateFromDate"
                        ></v-date-picker>
                      </v-menu>
                    </span>
                    <span class="d-inline-block">
                      <v-icon
                        class="btn"
                        v-if="data.startDateToDate"
                        @click="data.startDateToDate = ''"
                        >mdi-close</v-icon
                      >
                    </span>
                  </div>
                </span>
              </div>
            </v-col>
            <v-col cols="6">
              <div>Room Contract End Date</div>
              <div class="d-flex justify-start">
                <span class="d-inline-block mr-5">
                  <div class="d-flex align-center">
                    <span class="d-inline-block half-width">
                      <v-menu
                        ref="menu"
                        v-model="endDateFromMenu"
                        :close-on-content-click="false"
                        transition="scale-transition"
                        offset-y
                      >
                        <template v-slot:activator="{ on }">
                          <v-text-field
                            v-model="data.endDateFromDate"
                            label="From Date"
                            readonly
                            v-on="on"
                          ></v-text-field>
                        </template>
                        <v-date-picker
                          v-model="data.endDateFromDate"
                          no-title
                          scrollable
                          :max="data.endDateToDate"
                        ></v-date-picker>
                      </v-menu>
                    </span>
                    <span class="d-inline-block">
                      <v-icon
                        class="btn"
                        v-if="data.endDateFromDate"
                        @click="data.endDateFromDate = ''"
                        >mdi-close</v-icon
                      >
                    </span>
                  </div>
                </span>
                <span class="d-inline-block">
                  <div class="d-flex align-center">
                    <span class="d-inline-block half-width">
                      <v-menu
                        ref="menu"
                        v-model="endDateToMenu"
                        :close-on-content-click="false"
                        transition="scale-transition"
                        offset-y
                      >
                        <template v-slot:activator="{ on }">
                          <v-text-field
                            v-model="data.endDateToDate"
                            label="To Date"
                            readonly
                            v-on="on"
                          ></v-text-field>
                        </template>
                        <v-date-picker
                          v-model="data.endDateToDate"
                          no-title
                          scrollable
                          :min="data.endDateFromDate"
                        ></v-date-picker>
                      </v-menu>
                    </span>
                    <span class="d-inline-block">
                      <v-icon
                        class="btn"
                        v-if="data.endDateToDate"
                        @click="data.endDateToDate = ''"
                        >mdi-close</v-icon
                      >
                    </span>
                  </div>
                </span>
              </div>
            </v-col>
            <v-col cols="6">
              <div>Room Contract Created Date</div>
              <div class="d-flex justify-start">
                <span class="d-inline-block mr-5">
                  <div class="d-flex align-center">
                    <span class="d-inline-block half-width">
                      <v-menu
                        ref="menu"
                        v-model="createdDateFromMenu"
                        :close-on-content-click="false"
                        transition="scale-transition"
                        offset-y
                      >
                        <template v-slot:activator="{ on }">
                          <v-text-field
                            v-model="data.createdDateFromDate"
                            label="From Date"
                            readonly
                            v-on="on"
                          ></v-text-field>
                        </template>
                        <v-date-picker
                          v-model="data.createdDateFromDate"
                          no-title
                          scrollable
                          :max="data.createdDateToDate"
                        ></v-date-picker>
                      </v-menu>
                    </span>
                    <span class="d-inline-block">
                      <v-icon
                        class="btn"
                        v-if="data.createdDateFromDate"
                        @click="data.createdDateFromDate = ''"
                        >mdi-close</v-icon
                      >
                    </span>
                  </div>
                </span>
                <span class="d-inline-block">
                  <div class="d-flex align-center">
                    <span class="d-inline-block half-width">
                      <v-menu
                        ref="menu"
                        v-model="createdDateToMenu"
                        :close-on-content-click="false"
                        transition="scale-transition"
                        offset-y
                      >
                        <template v-slot:activator="{ on }">
                          <v-text-field
                            v-model="data.createdDateToDate"
                            label="To Date"
                            readonly
                            v-on="on"
                          ></v-text-field>
                        </template>
                        <v-date-picker
                          v-model="data.createdDateToDate"
                          no-title
                          scrollable
                          :min="data.createdDateFromDate"
                        ></v-date-picker>
                      </v-menu>
                    </span>
                    <span class="d-inline-block">
                      <v-icon
                        class="btn"
                        v-if="data.createdDateToDate"
                        @click="data.createdDateToDate = ''"
                        >mdi-close</v-icon
                      >
                    </span>
                  </div>
                </span>
              </div>
            </v-col>
            <v-col cols="12">
              <v-text-field
                label="Sequence No"
                :maxlength="300"
                v-model="data.sequence"
              ></v-text-field>
            </v-col>
            <v-col cols="6">
              <div>Checked out</div>
              <v-radio-group v-model="data.checkedout" row>
                <v-radio label="Yes" :value="1"></v-radio>
                <v-radio label="No" :value="0"></v-radio>
              </v-radio-group>
            </v-col>
            <!-- <v-col cols="6">
              <div>Outstanding Deposit</div>
              <v-radio-group v-model="data.outstanding" row>
                <v-radio label="Yes" :value="1"></v-radio>
                <v-radio label="No" :value="0"></v-radio>
              </v-radio-group>
            </v-col> -->
            <v-col cols="12">
              <v-autocomplete
                v-model="data.tenant"
                :item-text="(item) => helpers.capitalizeFirstLetter(item.name)"
                :items="tenants || []"
                label="Tenant"
                chips
                deletable-chips
                :return-object="true"
              ></v-autocomplete>
            </v-col>
            <v-col cols="12">
              <v-autocomplete
                v-model="data.room"
                :item-text="(item) => helpers.capitalizeFirstLetter(item.name)"
                :items="rooms || []"
                label="Room"
                chips
                deletable-chips
                :return-object="true"
              ></v-autocomplete>
            </v-col>
            <v-col cols="12">
              <v-autocomplete
                v-model="data.owner"
                :item-text="(item) => helpers.capitalizeFirstLetter(item.name)"
                :items="owners || []"
                label="Owner"
                chips
                deletable-chips
                :return-object="true"
              ></v-autocomplete>
            </v-col>
            <v-col cols="12">
              <v-autocomplete
                v-model="data.services"
                :item-text="(item) => helpers.capitalizeFirstLetter(item.name)"
                :items="services || []"
                label="Service"
                chips
                multiple
                deletable-chips
                :return-object="true"
              ></v-autocomplete>
            </v-col>
          </v-row>
        </v-container>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>
