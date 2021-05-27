

<script>
import { mapActions } from "vuex";
export default {
  data() {
    return {
      serviceFormDialog: false,
      serviceFilterDialog: false,
      totalDataLength: 0,
      data: [],
      loading: true,
      options: {},
      serviceFilterGroup: new Form({
        rooms: [],
        selectedRooms: [],
        keyword: null,
        fromdate: null,
        todate: null,
      }),
      serviceFilterDialogConfig: {
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

      serviceFormDialogConfig: {
        buttonStyle: {
          block: true,
          class: "title font-weight-bold ma-2",
          text: "Add Service",
          icon: "mdi-plus",
          isIcon: false,
          color: "primary",
          evalation: "5",
        },
      },
      headers: [
        {
          text: "id",
        },
        {
          text: "Name",
        },
      ],
    };
  },
  watch: {
    options: {
      handler() {
        this.getServices();
      },
      deep: true,
    },
  },
  computed: {
    isLoading() {
      return this.$store.getters.isLoading;
    },
    keywordEmpty() {
      return this.helpers.isEmpty(this.serviceFilterGroup.keyword);
    },
    fromdateEmpty() {
      return this.helpers.isEmpty(this.serviceFilterGroup.fromdate);
    },
    todateEmpty() {
      return this.helpers.isEmpty(this.serviceFilterGroup.todate);
    },
  },
  created() {},
  mounted() {
    this.getServices();
  },
  methods: {
    ...mapActions({
      filterServicesAction: "filterServices",
      showLoadingAction: "showLoadingAction",
      endLoadingAction: "endLoadingAction",
    }),
    initServiceFilter(filterGroup) {
      this.serviceFilterGroup.reset();
      if (filterGroup) {
        this.serviceFilterGroup.keyword = filterGroup.keyword;
      }
      this.options.page = 1;
      this.getServices();
    },
    showService($data) {
      this.$router.push("/service/" + $data.uid);
    },
    getServices() {
      this.loading = true;
      const { sortBy, sortDesc, page, itemsPerPage } = this.options;

      var totalResult = itemsPerPage;
      //Show All Items
      if (totalResult == -1) {
        this.serviceFilterGroup.pageNumber = -1;
        this.serviceFilterGroup.pageSize = -1;
      } else {
        this.serviceFilterGroup.pageNumber = page;
        this.serviceFilterGroup.pageSize = itemsPerPage;
      }

      this.filterServicesAction(this.serviceFilterGroup)
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
    <navbar
      :returnRole="
        (role) => {
          this.role = role;
        }
      "
    ></navbar>
    <v-content :class="helpers.managementStyles().backgroundClass">
      <v-container class="fill-height" fluid>
        <loading></loading>
        <v-row
          justify="center"
          align="center"
          class="ma-3"
          v-if="
            helpers.isAccessible(_.get(role, ['name']), 'service', 'create')
          "
        >
          <v-col cols="12">
            <v-btn
              block
              color="primary"
              class="ma-1"
              elevation="5"
              :disabled="isLoading"
              @click="serviceFormDialog = true"
              >Add Service</v-btn
            >
          </v-col>
        </v-row>

        <v-row justify="center" align="center" class="mx-3">
          <v-col cols="12">
            <v-card raised>
              <v-card-subtitle v-show="!keywordEmpty">
                Keyword :
                <v-chip class="mx-2">{{ serviceFilterGroup.keyword }}</v-chip>
              </v-card-subtitle>
              <v-card-subtitle v-show="!fromdateEmpty">
                From Date :
                <v-chip class="mx-2">{{ serviceFilterGroup.keyword }}</v-chip>
              </v-card-subtitle>
              <v-card-subtitle v-show="!todateEmpty">
                To Date :
                <v-chip class="mx-2">{{ serviceFilterGroup.todate }}</v-chip>
              </v-card-subtitle>
            </v-card>
          </v-col>
        </v-row>
        <v-row
          justify="center"
          align="center"
          class="ma-3"
          v-if="helpers.isAccessible(_.get(role, ['name']), 'service', 'read')"
        >
          <v-col cols="12">
            <v-card class="pa-8" raised>
              <v-data-table
                :headers="headers"
                :items="data"
                :options.sync="options"
                :server-items-length="totalDataLength"
                :loading="loading"
                disable-sort
              >
                <!-- <template v-slot:top>
                  <v-toolbar flat class="mb-5">
                    <v-btn
                      color="primary"
                      block
                      class="ma-2"
                      :disabled="isLoading"
                      @click="serviceFilterDialog = true"
                    >
                      <v-icon left>mdi-magnify</v-icon>Filter Service
                    </v-btn>
                  </v-toolbar>
                </template> -->
                <template v-slot:item="props">
                  <tr @click="showService(props.item)">
                    <td class="text-truncate">{{ props.item.id }}</td>
                    <td class="text-truncate">{{ props.item.name }}</td>
                  </tr>
                </template>
              </v-data-table>
            </v-card>
          </v-col>
        </v-row>
      </v-container>

      <v-dialog v-model="serviceFormDialog" persistent fullscreen hideOverlay>
        <service-form
          :reset="serviceFormDialog"
          :editmode="false"
          @created="showService($event)"
          @close="serviceFormDialog = false"
        ></service-form>
      </v-dialog>

      <v-dialog
        v-model="serviceFilterDialog"
        persistent
        maxWidth="1200px"
        hideOverlay
      >
        <service-filter-form
          @submitFilter="initServiceFilter($event)"
          @close="serviceFilterDialog = false"
          :reset="serviceFilterDialog"
        ></service-filter-form>
      </v-dialog>
    </v-content>
  </v-app>
</template>