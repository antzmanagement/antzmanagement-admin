
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
      categories: ["first inspection", "complain", "check out"],
      dialog: false,
      rooms: [],
      fromdatemenu: false,
      todatemenu: false,
      data: new Form({
        room: null,
        category: "",
        fromdate: null,
        todate: null,
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
    this.filterRoomsAction({ pageNumber: 1, pageSize: this.helpers.maxPaginationSize() })
      .then(async (data) => {
        this.endLoadingAction();
        this.rooms = data.data;
        if (data.maximumPages > 1) {
          let appendData = await this.getAllRoomResponses(data.maximumPages);
          this.rooms = _.concat(this.rooms, appendData);
        }
      })
      .catch((error) => {
        console.log(error);
        Toast.fire({
          icon: "warning",
          title: "Something went wrong... ",
        });
        this.endLoadingAction();
      });
  },
  methods: {
    ...mapActions({
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
        <v-toolbar-title>Room Check Filter</v-toolbar-title>
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
