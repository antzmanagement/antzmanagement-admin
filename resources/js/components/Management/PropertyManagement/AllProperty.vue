<template>
  <v-app>
    <navbar></navbar>
    <v-content :class="helpers.managementStyles().backgroundClass">
      <v-container class="fill-height" fluid>
        <loading></loading>
        <v-row justify="center" align="center" class="ma-3">
          <v-col cols="12">
            <v-btn
              block
              color="primary"
              class="ma-1"
              elevation="5"
              :disabled="isLoading"
              @click="propertyFormDialog = true"
            >Add Property</v-btn>
          </v-col>
        </v-row>

        <v-row justify="center" align="center" class="mx-3">
          <v-col cols="12">
            <v-card raised>
              <v-card-subtitle v-show="!keywordEmpty">
                Keyword :
                <v-chip class="mx-2">{{ propertyFilterGroup.keyword }}</v-chip>
              </v-card-subtitle>
              <v-card-subtitle v-show="!fromdateEmpty">
                From Date :
                <v-chip class="mx-2">{{ propertyFilterGroup.keyword }}</v-chip>
              </v-card-subtitle>
              <v-card-subtitle v-show="!todateEmpty">
                To Date :
                <v-chip class="mx-2">{{ propertyFilterGroup.todate }}</v-chip>
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
                    <v-btn color="primary" block class="ma-2" :disabled="isLoading" @click="propertyFilterDialog = true">
                      <v-icon left>mdi-magnify</v-icon>Filter Property
                    </v-btn>
                  </v-toolbar>
                </template>
                <template v-slot:item="props">
                  <tr @click="showProperty(props.item)">
                    <td>{{props.item.uid}}</td>
                    <td>{{props.item.name}}</td>
                  </tr>
                </template>
              </v-data-table>
            </v-card>
          </v-col>
        </v-row>
      </v-container>

      <v-dialog v-model="propertyFormDialog" persistent fullscreen hideOverlay>
        <property-form
          :reset="propertyFormDialog"
          :editmode="false"
          @created="showProperty($event)"
          @close="propertyFormDialog = false"
        ></property-form>
      </v-dialog>

      <v-dialog v-model="propertyFilterDialog" persistent maxWidth="1200px" hideOverlay>
        <property-filter-form
          @submitFilter="initPropertyFilter($event)"
          @close="propertyFilterDialog = false"
          :reset="propertyFilterDialog"
        ></property-filter-form>
      </v-dialog>
    </v-content>
  </v-app>
</template>

<script>
import { mapActions } from "vuex";
export default {
  data() {
    return {
      propertyFormDialog: false,
      propertyFilterDialog : false,
      totalDataLength: 0,
      data: [],
      loading: true,
      options: {},
      propertyFilterGroup: new Form({
        rooms: [],
        selectedRooms: [],
        keyword: null,
        fromdate: null,
        todate: null
      }),
      propertyFilterDialogConfig: {
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

      propertyFormDialogConfig: {
        buttonStyle: {
          block: true,
          class: "title font-weight-bold ma-2",
          text: "Add Property",
          icon: "mdi-plus",
          isIcon: false,
          color: "primary",
          evalation: "5"
        }
      },
      headers: [
        {
          text: "uid"
        },
        {
          text: "Name"
        }
      ]
    };
  },
  watch: {
    options: {
      handler() {
        this.getProperties();
      },
      deep: true
    }
  },
  computed: {
    isLoading() {
      return this.$store.getters.isLoading;
    },
    keywordEmpty() {
      return this.helpers.isEmpty(this.propertyFilterGroup.keyword);
    },
    fromdateEmpty() {
      return this.helpers.isEmpty(this.propertyFilterGroup.fromdate);
    },
    todateEmpty() {
      return this.helpers.isEmpty(this.propertyFilterGroup.todate);
    }
  },
  created() {},
  mounted() {
    this.getProperties();
  },
  methods: {
    ...mapActions({
      filterPropertiesAction: "filterProperties",
      showLoadingAction: "showLoadingAction",
      endLoadingAction: "endLoadingAction"
    }),
    initPropertyFilter(filterGroup) {
      this.propertyFilterGroup.reset();
      if (filterGroup) {
        this.propertyFilterGroup.selectedRooms = filterGroup.rooms;
        this.propertyFilterGroup.rooms = filterGroup.rooms.map(function(
          propertyType
        ) {
          return propertyType.id;
        });
        this.propertyFilterGroup.keyword = filterGroup.keyword;
      }
      this.getProperties();
    },
    showProperty($data) {
      this.$router.push("/property/" + $data.uid);
    },
    getProperties() {
      this.loading = true;
      const { sortBy, sortDesc, page, itemsPerPage } = this.options;

      var totalResult = itemsPerPage;
      //Show All Items
      if (totalResult == -1) {
        this.propertyFilterGroup.pageNumber = -1;
        this.propertyFilterGroup.pageSize = -1;
      } else {
        this.propertyFilterGroup.pageNumber = page;
        this.propertyFilterGroup.pageSize = itemsPerPage;
      }

      this.filterPropertiesAction(this.propertyFilterGroup)
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