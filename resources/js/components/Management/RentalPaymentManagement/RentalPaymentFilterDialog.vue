
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
        text: "RentalPayment Filter",
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
      paymentMethods: ["cash", "online_transfer", "eWallet", "credit"],
      fromdatemenu: false,
      todatemenu: false,
      dialog: false,
      tenants: [],
      rooms: [],
      data: new Form({
        keyword: "",
        fromdate: null,
        todate: null,
        rooms: [],
        paid: "all",
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
    promises.push(this.getRoomsAction({ pageNumber: -1, pageSize: -1 }));

    Promise.all(promises)
      .then(([tenantRes, roomRes]) => {
        this.tenants = _.get(tenantRes, ["data"]) || [];
        this.rooms = _.get(roomRes, ["data"]) || [];
        this.endLoadingAction();
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
      getRoomsAction: "getRooms",
      getTenantsAction: "getTenants",
      showLoadingAction: "showLoadingAction",
      endLoadingAction: "endLoadingAction",
    }),
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
        <v-toolbar-title v->RentalPayment Filter</v-toolbar-title>
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
                <span className=" d-inline-block half-width">
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
                        label="From Date"
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
                <span className=" d-inline-block half-width">
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
                        label="To Date"
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
              <v-text-field
                label="Sequence No"
                :maxlength="300"
                v-model="data.sequence"
              ></v-text-field>
            </v-col>
          </v-row>
          <v-row>
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
            <v-select
              :items="paymentMethods"
              v-model="data.paymentmethod"
              label="Payment Method"
            ></v-select>
          </v-col>
            <v-col cols="6">
              <div>Paid Status</div>
              <v-radio-group v-model="data.paid" row>
                <v-radio label="Paid" :value="1"></v-radio>
                <v-radio label="Unpaid" :value="0"></v-radio>
              </v-radio-group>
            </v-col>
            <v-col cols="6">
              <div>Penalty</div>
              <v-radio-group v-model="data.penalty" row>
                <v-radio label="Yes" :value="1"></v-radio>
                <v-radio label="No" :value="0"></v-radio>
              </v-radio-group>
            </v-col>
          </v-row>
        </v-container>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>
