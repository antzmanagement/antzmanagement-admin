
<script>
import { validationMixin } from "vuelidate";
import {
  required,
  minLength,
  maxLength,
  decimal,
} from "vuelidate/lib/validators";
import { mapActions } from "vuex";
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
        text: "Maintenance Filter",
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
      dialog: false,
      owners: [],
      rooms: [],
      properties : [],
      tenants : [],
      fromdatemenu: false,
      todatemenu: false,
      maintenanceTypes: ["repair", "renew"],
      maintenanceStatus: ["pending", "inprogress", "reject", "done"],
      data: new Form({
        keyword: "",
        fromdate: null,
        todate: null,
        rooms: [],
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
    promises.push(this.filterRoomsAction({ pageNumber: 1, pageSize: this.helpers.maxPaginationSize() }));
    promises.push(this.getOwnersAction({ pageNumber: -1, pageSize: -1 }));
    promises.push(this.getPropertiesAction({ pageNumber: -1, pageSize: -1 }));
    promises.push(this.getTenantsAction({ pageNumber: -1, pageSize: -1 }));


    Promise.all(promises)
      .then(async([roomRes, ownerRes, propertyRes, tenantRes]) => {
        this.endLoadingAction();
        this.rooms = _.get(roomRes, ["data"]) || [];
        this.owners = _.get(ownerRes, ["data"]) || [];
        this.properties = _.get(propertyRes, ["data"]) || [];
        this.tenants = _.get(tenantRes, ["data"]) || [];
        if (roomRes.maximumPages > 1) {
          let appendData = await this.getAllRoomResponses(roomRes.maximumPages);
          this.rooms = _.concat(this.rooms, appendData);
        }
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
      filterRoomsAction: "filterRooms",
      getTenantsAction: "getTenants",
      getPropertiesAction: "getProperties",
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
        <v-toolbar-title >Maintenance Filter</v-toolbar-title>
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
              <div class="d-flex align-center">
                <span className=" d-inline-block">
                  <v-menu
                    ref="menu"
                    v-model="fromdatemenu"
                    :close-on-content-click="false"
                    transition="scale-transition"
                    offset-y
                  >
                    <template v-slot:activator="{ on }">
                      <v-text-field
                        v-model="data.fromdate"
                        label="Maintenace From Date"
                        readonly
                        v-on="on"
                      ></v-text-field>
                    </template>
                    <v-date-picker
                      v-model="data.fromdate"
                      no-title
                      scrollable
                      :max="data.todate"
                    ></v-date-picker>
                  </v-menu>
                </span>
                <span className="d-inline-block">
                  <v-icon
                    class="btn"
                    v-if="data.fromdate"
                    @click="data.fromdate = ''"
                    >mdi-close</v-icon
                  >
                </span>
              </div>
            </v-col>
            <v-col cols="6">
              <div class="d-flex align-center">
                <span className=" d-inline-block">
                  <v-menu
                    ref="menu"
                    v-model="todatemenu"
                    :close-on-content-click="false"
                    transition="scale-transition"
                    offset-y
                  >
                    <template v-slot:activator="{ on }">
                      <v-text-field
                        v-model="data.todate"
                        label="Maintenance To Date"
                        readonly
                        v-on="on"
                      ></v-text-field>
                    </template>
                    <v-date-picker
                      v-model="data.todate"
                      no-title
                      scrollable
                      :min="data.fromdate"
                    ></v-date-picker>
                  </v-menu>
                </span>
                <span className="d-inline-block">
                  <v-icon
                    class="btn"
                    v-if="data.todate"
                    @click="data.todate = ''"
                    >mdi-close</v-icon
                  >
                </span>
              </div>
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
                label="Claim By Owner"
                chips
                return-object
                deletable-chips
              >
              </v-autocomplete>
            </v-col>
            <v-col cols="12">
              <v-autocomplete
                v-model="data.tenant"
                :item-text="(item) => helpers.capitalizeFirstLetter(item.name)"
                :items="tenants || []"
                label="Claim By Tenant"
                chips
                return-object
                deletable-chips
              >
              </v-autocomplete>
            </v-col>
            <v-col cols="12">
              <v-autocomplete
                v-model="data.property"
                :item-text="(item) => helpers.capitalizeFirstLetter(item.name)"
                item-value="id"
                :items="properties || []"
                label="Property"
                chips
                return-object
              >
              </v-autocomplete>
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
          </v-row>
        </v-container>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>