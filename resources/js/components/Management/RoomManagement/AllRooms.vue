<template>
  <v-app>
    <navbar></navbar>
    <v-content>
      <v-container class="fill-height" fluid>
        <loading></loading>
        <v-row justify="center" align="center" class="ma-3">
          <v-col cols="12">
            <room-form :editMode="false" @created="showRoom($event)"></room-form>
          </v-col>
        </v-row>

        <v-row justify="center" align="center" class="mx-3">
          <v-col cols="12">
            <v-card>
              <v-card-subtitle v-show="!keywordEmpty">
                Keyword :
                <v-chip class="mx-2">{{ roomFilterGroup.keyword }}</v-chip>
              </v-card-subtitle>
              <v-card-subtitle v-show="!fromdateEmpty">
                From Date :
                <v-chip class="mx-2">{{ roomFilterGroup.keyword }}</v-chip>
              </v-card-subtitle>
              <v-card-subtitle v-show="!todateEmpty">
                To Date :
                <v-chip class="mx-2">{{ roomFilterGroup.todate }}</v-chip>
              </v-card-subtitle>

              <v-card-subtitle v-show="!roomTypesEmpty">
                Room Types :
                <v-chip
                  class="mx-2"
                  v-for="roomType in roomFilterGroup.selectedRoomTypes"
                  :key="roomType.id"
                >{{ roomType.name | capitalizeFirstLetter }}</v-chip>
              </v-card-subtitle>
            </v-card>
          </v-col>
        </v-row>
        <v-row justify="center" align="center" class="ma-3">
          <v-col cols="12">
            <v-data-table
              :headers="headers"
              :items="data"
              :options.sync="options"
              :server-items-length="totalDataLength"
              :loading="loading"
              class="elevation-1"
            >
              <template v-slot:top>
                <v-toolbar flat>
                  <room-filter-dialog
                    :buttonStyle="roomFilterDialogConfig.buttonStyle"
                    :dialogStyle="roomFilterDialogConfig.dialogStyle"
                    @submitFilter="initRoomFilter($event)"
                  ></room-filter-dialog>
                </v-toolbar>
              </template>
              <template v-slot:item="props">
                <tr @click="showRoom(props.item)">
                  <td>{{props.item.name}}</td>
                  <td>{{props.item.price}}</td>
                </tr>
              </template>
            </v-data-table>
          </v-col>
        </v-row>
      </v-container>
    </v-content>
  </v-app>
</template>


<script>
import { mapActions } from "vuex";
export default {
  data() {
    return {
      totalDataLength: 0,
      data: [],
      loading: true,
      options: {},
      roomFilterGroup: new Form({
        roomTypes: [],
        selectedRoomTypes: [],
        keyword: null,
        fromdate: null,
        todate: null
      }),
      roomFilterDialogConfig: {
        buttonStyle: {
          block: true,
          class: "ma-2",
          text: "Filter",
          icon: "mdi-magnify",
          isIcon: false,
          color: "primary"
        },
        dialogStyle: {
          persistent: true,
          maxWidth: "1200px",
          fullscreen: false,
          hideOverlay: true
        }
      },
      headers: [
        {
          text: "Unit No",
          align: "start",
          sortable: true,
          value: "name"
        },
        { text: "Price (RM)", value: "price", sortable: true }
      ]
    };
  },
  watch: {
    options: {
      handler() {
        this.getRooms();
      },
      deep: true
    }
  },
  computed: {
    isLoading() {
      return this.$store.getters.isLoading;
    },
    keywordEmpty() {
      return this.helpers.isEmpty(this.roomFilterGroup.keyword);
    },
    fromdateEmpty() {
      return this.helpers.isEmpty(this.roomFilterGroup.fromdate);
    },
    todateEmpty() {
      return this.helpers.isEmpty(this.roomFilterGroup.todate);
    },
    roomTypesEmpty() {
      return this.helpers.isEmpty(this.roomFilterGroup.selectedRoomTypes);
    }
  },
  created() {
    this.$vuetify.theme.dark = true;
  },
  mounted() {
    this.getRooms();
  },
  methods: {
    ...mapActions({
      getRoomsAction: "getRooms",
      filterRoomsAction: "filterRooms",
      showLoadingAction: "showLoadingAction",
      endLoadingAction: "endLoadingAction"
    }),
    initRoomFilter(filterGroup) {
      this.roomFilterGroup.reset();
      if (filterGroup) {
        this.roomFilterGroup.selectedRoomTypes = filterGroup.roomTypes;
        this.roomFilterGroup.roomTypes = filterGroup.roomTypes.map(function(
          roomType
        ) {
          return roomType.id;
        });
        this.roomFilterGroup.keyword = filterGroup.keyword;
      }
      this.getRooms();
    },
    showRoom($data) {
      this.$router.push("/room/" + $data.uid);
    },
    getRooms() {
      this.loading = true;
      const { sortBy, sortDesc, page, itemsPerPage } = this.options;

      var totalResult = itemsPerPage;
      //Show All Items
      if (totalResult == -1) {
        this.roomFilterGroup.pageNumber = -1;
        this.roomFilterGroup.pageSize = -1;
      } else {
        this.roomFilterGroup.pageNumber = page;
        this.roomFilterGroup.pageSize = itemsPerPage;
      }

      this.filterRoomsAction(this.roomFilterGroup)
        .then(data => {
          if (data.data) {
            this.data = data.data;
          } else {
            this.data = [];
          }
          this.totalDataLength = data.totalResult;
          this.loading = false;
        })
        .catch(error => {
          this.loading = false;
          Toast.fire({
            icon: "warning",
            title: "Something went wrong... "
          });
        });
    }
  }
};
</script>