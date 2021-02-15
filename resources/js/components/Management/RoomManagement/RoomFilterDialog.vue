
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
        text: "Room Filter",
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
      roomStatusOptions: ["empty", "maintaining", "occupied"],
      dialog: false,
      roomTypes: [],
      owners: [],
      data: new Form({
        unit: "",
        jalan: "",
        block: "",
        floor: "",
        room_status: "",
        roomType: "",
        owner: "",
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
    this.getRoomTypesAction({ pageNumber: -1, pageSize: -1 })
      .then((data) => {
        this.roomTypes = data.data;
        this.getOwnersAction({ pageNumber: -1, pageSize: -1 })
          .then((data) => {
            this.owners = data.data;
            this.endLoadingAction();
          })
          .catch((error) => {
            Toast.fire({
              icon: "warning",
              title: "Something went wrong... ",
            });
            this.endLoadingAction();
          });
      })
      .catch((error) => {
        Toast.fire({
          icon: "warning",
          title: "Something went wrong... ",
        });
        this.endLoadingAction();
      });
  },
  methods: {
    ...mapActions({
      getRoomTypesAction: "getRoomTypes",
      getOwnersAction: "getOwners",
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
        <v-toolbar-title v->Room Filter</v-toolbar-title>
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
              <v-text-field
                label="Unit"
                :maxlength="300"
                v-model="data.unit"
              ></v-text-field>
            </v-col>
            <v-col cols="6">
              <v-text-field
                label="Jalan"
                :maxlength="300"
                v-model="data.jalan"
              ></v-text-field>
            </v-col>
            <v-col cols="6">
              <v-text-field
                label="Block"
                :maxlength="300"
                v-model="data.block"
              ></v-text-field>
            </v-col>
            <v-col cols="6">
              <v-text-field
                label="Floor"
                :maxlength="300"
                v-model="data.floor"
              ></v-text-field>
            </v-col>
            <v-col cols="12">
              <v-autocomplete
                v-model="data.owner"
                :item-text="(item) => helpers.capitalizeFirstLetter(item.name)"
                :items="owners"
                label="Owner"
                chips
                return-object
                deletable-chips
              >
              </v-autocomplete>
            </v-col>
            <v-col cols="12">
              <v-autocomplete
                v-model="data.roomType"
                :item-text="(item) => helpers.capitalizeFirstLetter(item.name)"
                :items="roomTypes"
                label="Room Type"
                chips
                deletable-chips
                :return-object="true"
              ></v-autocomplete>
            </v-col>
            <v-col cols="12" md="12">
              <v-select
                :items="roomStatusOptions"
                v-model="data.room_status"
                label="Room Status"
              ></v-select>
            </v-col>
          </v-row>
          <v-row> </v-row>
        </v-container>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>
