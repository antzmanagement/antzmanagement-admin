

<script>
import { validationMixin } from "vuelidate";
import {
  required,
  minLength,
  maxLength,
  decimal,
} from "vuelidate/lib/validators";
import { mapActions } from "vuex";
import { notEmptyLength, _ } from "../../../common/common-function";
export default {
  props: {
    editMode: {
      type: Boolean,
      default: false,
    },
    buttonStyle: {
      type: Object,
      default: () => ({
        block: true,
        color: "primary",
        class: "ma-1",
        text: "Services",
        icon: "mdi-filter-menu",
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
    services: {
      type: Array,
      default: () => [],
    },
    origServices: {
      type: Array,
      default: () => [],
    },
  },
  data() {
    return {
      dialog: false,
      serviceList: [],
      fixedServices: [],
      data: new Form({
        services: this.services,
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
        this.data = new Form({
          services: this.services,
        });
      }
    },
    services: function (val) {
      if (_.isArray(val) && notEmptyLength(val)) {
        this.data.services = val;
      } else {
        this.data.services = [];
      }
    },
  },
  mounted() {
    this.showLoadingAction();
    this.getServicesAction({ pageNumber: -1, pageSize: -1 })
      .then((data) => {
        this.serviceList = data.data;
        this.endLoadingAction();
      })
      .catch((error) => {
        Toast.fire({
          icon: "warning",
          title: "Something went wrong... ",
        });
        this.endLoadingAction();
      });

    this.data.services = this.services;
    this.fixedServices = this.origServices;
  },
  updated(){
  },
  methods: {
    ...mapActions({
      getServicesAction: "getServices",
      showLoadingAction: "showLoadingAction",
      endLoadingAction: "endLoadingAction",
    }),
    submit() {
      this.data.services = this.serviceList.filter(function (service) {
        return this.data.services.some(function (item) {
          return item == service.uid;
        });
      }, this);
      this.$emit("submit", this.data);
      this.dialog = false;
    },
    existedUid(items, uid) {
      return items.some(function (service) {
        return service == uid;
      });
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
      <span class="d-inline-block" v-on="on">
        <slot>
          <v-btn tile isIcon :disabled="isLoading">
            <v-icon>mdi-filter-menu</v-icon>
          </v-btn>
        </slot>
      </span>
    </template>
    <v-card>
      <v-toolbar dark color="primary">
        <v-btn icon dark @click="dialog = false">
          <v-icon>mdi-close</v-icon>
        </v-btn>
        <v-toolbar-title>Services</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-toolbar-items>
          <v-btn dark text :disabled="isLoading" @click="submit()">Save</v-btn>
        </v-toolbar-items>
      </v-toolbar>
      <v-card-text>
        <v-container v-if="!this.helpers.isEmpty(serviceList)">
          <v-row>
            <v-col :cols="6" v-for="service in serviceList" :key="service.uid">
              <v-checkbox
                v-model="data.services"
                :value="service.uid"
                :label="service.text + ' - RM' + service.price"
                :readonly="
                  (existedUid(services, service.uid) &&
                    existedUid(fixedServices, service.uid)) ||
                  !editMode
                "
              ></v-checkbox>
            </v-col>
          </v-row>
        </v-container>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>