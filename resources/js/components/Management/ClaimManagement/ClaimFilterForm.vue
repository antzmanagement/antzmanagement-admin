
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
    reset: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
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
    reset: function (val) {
      if (val) {
        this.data.reset();
      }
    },
  },
  mounted() {
    this.filterRoomsAction({ pageNumber: 1, pageSize: this.helpers.maxPaginationSize() })
      .then(async (data) => {
        this.rooms = data.data;
        this.endLoadingAction();
        if (data.maximumPages > 1) {
          let appendData = await this.getAllRoomResponses(data.maximumPages);
          this.rooms = _.concat(this.rooms, appendData);
        }
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
      filterRoomsAction: "filterRooms",
      showLoadingAction: "showLoadingAction",
      endLoadingAction: "endLoadingAction",
    }),
    close() {
      this.$emit("close");
    },
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
      this.close();
    },
  },
};
</script>

<template>
  <v-card>
    <v-toolbar dark color="primary">
      <v-btn icon dark @click="close()">
        <v-icon>mdi-close</v-icon>
      </v-btn>
      <v-toolbar-title v->Filter Claims</v-toolbar-title>
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
              label="Keyword"
              :maxlength="300"
              v-model="data.keyword"
            ></v-text-field>
          </v-col>
        </v-row>
        <v-row>
          <v-col cols="12">
            <v-autocomplete
              v-model="data.rooms"
              :item-text="(item) => helpers.capitalizeFirstLetter(item.name)"
              :items="rooms || []"
              label="Room"
              chips
              deletable-chips
              multiple
              :return-object="true"
            ></v-autocomplete>
          </v-col>
        </v-row>
      </v-container>
    </v-card-text>
  </v-card>
</template>
