
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
      categories: ["furniture", "electric_appliances", "wash_room", 'main_structure'],
      dialog: false,
      data: new Form({
        category: "",
        name : '',
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
  },
  methods: {
    ...mapActions({
      getRoomsAction: "getRooms",
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
        <v-toolbar-title >Property Filter</v-toolbar-title>
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
            <v-col cols="12">
              <v-text-field
                label="Name"
                :maxlength="300"
                v-model="data.name"
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="6">
              <v-select
                :items="categories"
                v-model="data.category"
                label="Category"
              ></v-select>
            </v-col>
          </v-row>
          <v-row> </v-row>
        </v-container>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>
