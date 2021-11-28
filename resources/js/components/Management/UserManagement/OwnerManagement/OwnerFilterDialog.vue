

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
        text: "Owner Filter",
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
      roomTypes: [],
      rooms: [],
      data: new Form({
        keyword: "",
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

    Promise.all(promises)
      .then(async ([roomRes]) => {
        this.endLoadingAction();
        this.rooms = _.get(roomRes, ["data"]) || [];
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
      getRoomTypesAction: "getRoomTypes",
      filterRoomsAction: "filterRooms",
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
          this.endLoadingAction();
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
        <v-toolbar-title>Owner Filter</v-toolbar-title>
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
                label="Keyword Search (Name, IcNo)"
                :maxlength="300"
                v-model="data.keyword"
              ></v-text-field>
            </v-col>
            <v-col cols="12">
              <v-text-field
                label="Phone"
                :maxlength="300"
                v-model="data.tel"
              ></v-text-field>
            </v-col>
          </v-row>
          <v-col cols="12">
            <v-autocomplete
              v-model="data.room"
              :item-text="
                (item) => helpers.capitalizeFirstLetter(item.name || '')
              "
              :items="rooms || []"
              label="Room"
              chips
              deletable-chips
              :return-object="true"
            ></v-autocomplete>
          </v-col>
        </v-container>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>