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
        <v-icon left>{{buttonStyle.icon}}</v-icon>
        {{buttonStyle.text}}
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
          <v-btn
            dark
            text
            :disabled="isLoading"
            @click="submitFilter()"
          >Apply</v-btn>
        </v-toolbar-items>
      </v-toolbar>
      <v-card-text>
        <v-container>
          <v-row>
            <v-col cols="12">
              <v-autocomplete
                v-model="data.roomTypes"
                :item-text="item => helpers.capitalizeFirstLetter(item.name)"
                :items="roomTypes"
                label="Room Type"
                chips
                deletable-chips
                multiple
                :return-object="true"
              >
              </v-autocomplete>
            </v-col>
          </v-row>
        </v-container>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>

<script>
import { validationMixin } from "vuelidate";
import {
  required,
  minLength,
  maxLength,
  decimal
} from "vuelidate/lib/validators";
import { mapActions } from "vuex";
export default {
  props: {
    editMode: {
      type: Boolean,
      default: false
    },
    uid: {
      type: String,
      default: ""
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
      })
    },
    dialogStyle: {
      type: Object,
      default: () => ({
        persistent: true,
        maxWidth: "",
        fullscreen: true,
        hideOverlay: true
      })
    }
  },
  data() {
    return {
      dialog: false,
      roomTypes: [],
      data: new Form({
        roomTypes: [],
      }),
    };
  },

  computed: {
    isLoading() {
      return this.$store.getters.isLoading;
    },
  },
  watch: {
    dialog: function(val) {
      if (val) {
        this.data.reset();
      }
    }
  },
  mounted() {
    this.$vuetify.theme.dark = true;

    this.showLoadingAction();
    this.getRoomTypesAction({ pageNumber: -1, pageSize: -1 }).then(data => {
      this.roomTypes = data.data;
    this.endLoadingAction();
    });
  },
  methods: {
    ...mapActions({
      getRoomTypesAction: "getRoomTypes",
      showLoadingAction: "showLoadingAction",
      endLoadingAction: "endLoadingAction"
    }),
    submitFilter(){
      this.$emit('submitFilter', this.data);
      this.dialog = false;
    }
  }
};
</script>