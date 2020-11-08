

<script>
import { mapActions } from "vuex";
export default {
  data() {
    return {
      totalDataLength: 0,
      data: [],
      loading: true,
      options: {},
      ownerFilterGroup: new Form({
        roomTypes: [],
        selectedRoomTypes: [],
        keyword: null,
        fromdate: null,
        todate: null,
      }),
      ownerFormDialogConfig: {
        buttonStyle: {
          block: true,
          class: "title font-weight-bold ma-2",
          text: "Add Owner",
          icon: "mdi-plus",
          isIcon: false,
          color: "primary",
          evalation: "5",
        },
      },
      ownerFilterDialogConfig: {
        buttonStyle: {
          block: true,
          class: "ma-2",
          text: "Filter",
          icon: "mdi-magnify",
          isIcon: false,
          color: "primary",
        },
        dialogStyle: {
          persistent: true,
          maxWidth: "1200px",
          fullscreen: false,
          hideOverlay: true,
        },
      },
      headers: [
        {
          text: "Name",
          align: "start",
          value: "name",
        },
        { text: "Ic No", value: "icno" },
        { text: "Phone", value: "tel1" },
        { text: "Email", value: "email" },
      ],
    };
  },
  watch: {
    options: {
      handler() {
        this.getOwners();
      },
      deep: true,
    },
  },
  computed: {
    isLoading() {
      return this.$store.getters.isLoading;
    },
    keywordEmpty() {
      return this.helpers.isEmpty(this.ownerFilterGroup.keyword);
    },
    fromdateEmpty() {
      return this.helpers.isEmpty(this.ownerFilterGroup.fromdate);
    },
    todateEmpty() {
      return this.helpers.isEmpty(this.ownerFilterGroup.todate);
    },
    roomTypesEmpty() {
      return this.helpers.isEmpty(this.ownerFilterGroup.selectedRoomTypes);
    },
  },
  created() {
    var styles = this.helpers.managementStyles();
  },
  mounted() {
    this.getOwners();
  },
  methods: {
    ...mapActions({
      getOwnersAction: "getOwners",
      filterOwnersAction: "filterOwners",
      showLoadingAction: "showLoadingAction",
      endLoadingAction: "endLoadingAction",
    }),
    initOwnerFilter(filterGroup) {
      this.ownerFilterGroup.reset();
      if (filterGroup) {
        this.ownerFilterGroup.selectedRoomTypes = filterGroup.roomTypes;
        this.ownerFilterGroup.roomTypes = filterGroup.roomTypes.map(function (
          roomType
        ) {
          return roomType.id;
        });
        this.ownerFilterGroup.keyword = filterGroup.keyword;
      }
      this.getOwners();
    },
    showOwner($data) {
      this.$router.push("/owner/" + $data.uid);
    },
    getOwners() {
      this.loading = true;
      const { sortBy, sortDesc, page, itemsPerPage } = this.options;

      var totalResult = itemsPerPage;
      //Show All Items
      if (totalResult == -1) {
        this.ownerFilterGroup.pageNumber = -1;
        this.ownerFilterGroup.pageSize = -1;
      } else {
        this.ownerFilterGroup.pageNumber = page;
        this.ownerFilterGroup.pageSize = itemsPerPage;
      }

      this.filterOwnersAction(this.ownerFilterGroup)
        .then((data) => {
          if (data.data) {
            this.data = data.data;
          } else {
            this.data = [];
          }
          this.totalDataLength = data.totalResult;
          this.loading = false;
        })
        .catch((error) => {
          this.loading = false;
          Toast.fire({
            icon: "warning",
            title: "Something went wrong... ",
          });
        });
    },
  },
};
</script>

<template>
  <v-app>
    <navbar></navbar>
    <v-content :class="helpers.managementStyles().backgroundClass">
      <v-container class="fill-height" fluid>
        <loading></loading>
        <v-row justify="center" align="center" class="ma-3">
          <v-col cols="12">
            <owner-form
              :editMode="false"
              :buttonStyle="ownerFormDialogConfig.buttonStyle"
              @created="showOwner($event)"
            ></owner-form>
          </v-col>
        </v-row>

        <v-row justify="center" align="center" class="mx-3">
          <v-col cols="12">
            <v-card raised>
              <v-card-subtitle v-show="!keywordEmpty">
                Keyword :
                <v-chip class="mx-2">{{ ownerFilterGroup.keyword }}</v-chip>
              </v-card-subtitle>
              <v-card-subtitle v-show="!fromdateEmpty">
                From Date :
                <v-chip class="mx-2">{{ ownerFilterGroup.keyword }}</v-chip>
              </v-card-subtitle>
              <v-card-subtitle v-show="!todateEmpty">
                To Date :
                <v-chip class="mx-2">{{ ownerFilterGroup.todate }}</v-chip>
              </v-card-subtitle>

              <v-card-subtitle v-show="!roomTypesEmpty">
                Room Types :
                <v-chip
                  class="mx-2"
                  v-for="roomType in ownerFilterGroup.selectedRoomTypes"
                  :key="roomType.id"
                  >{{ roomType.name | capitalizeFirstLetter }}</v-chip
                >
              </v-card-subtitle>
            </v-card>
          </v-col>
        </v-row>
        <v-row justify="center" align="center" class="ma-3">
          <v-col cols="12">
            <v-card class="pa-8" raised>
              <v-data-table
                :headers="headers"
                :items="data"
                :options.sync="options"
                :server-items-length="totalDataLength"
                :loading="loading"
              >
                <template v-slot:top>
                  <v-toolbar flat class="mb-5">
                    <owner-filter-dialog
                      :buttonStyle="ownerFilterDialogConfig.buttonStyle"
                      :dialogStyle="ownerFilterDialogConfig.dialogStyle"
                      @submitFilter="initOwnerFilter($event)"
                    ></owner-filter-dialog>
                  </v-toolbar>
                </template>
                <template v-slot:item="props">
                  <tr @click="showOwner(props.item)">
                    <td>{{ props.item.name }}</td>
                    <td>{{ props.item.icno }}</td>
                    <td>{{ props.item.tel1 }}</td>
                    <td>{{ props.item.email }}</td>
                  </tr>
                </template>
              </v-data-table>
            </v-card>
          </v-col>
        </v-row>
      </v-container>
    </v-content>
  </v-app>
</template>
