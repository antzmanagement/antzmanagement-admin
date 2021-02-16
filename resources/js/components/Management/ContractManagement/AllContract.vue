
<script>
import { mapActions } from "vuex";
export default {
  data() {
    return {
      contractFormDialog: false,
      contractFilterDialog : false,
      totalDataLength: 0,
      data: [],
      loading: true,
      options: {},
      contractFilterGroup: new Form({
        rooms: [],
        selectedRooms: [],
        keyword: null,
        fromdate: null,
        todate: null
      }),
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
        this.getContracts();
      },
      deep: true
    }
  },
  computed: {
    isLoading() {
      return this.$store.getters.isLoading;
    },
    keywordEmpty() {
      return this.helpers.isEmpty(this.contractFilterGroup.keyword);
    },
    fromdateEmpty() {
      return this.helpers.isEmpty(this.contractFilterGroup.fromdate);
    },
    todateEmpty() {
      return this.helpers.isEmpty(this.contractFilterGroup.todate);
    }
  },
  created() {},
  mounted() {
    this.getContracts();
  },
  methods: {
    ...mapActions({
      filterContractsAction: "filterContracts",
      showLoadingAction: "showLoadingAction",
      endLoadingAction: "endLoadingAction"
    }),
    initContractFilter(filterGroup) {
      this.contractFilterGroup.reset();
      if (filterGroup) {
        this.contractFilterGroup.selectedRooms = filterGroup.rooms;
        this.contractFilterGroup.rooms = filterGroup.rooms.map(function(
          contractType
        ) {
          return contractType.id;
        });
        this.contractFilterGroup.keyword = filterGroup.keyword;
      }
      this.getContracts();
    },
    showContract($data) {
      this.$router.push("/contract/" + $data.uid);
    },
    getContracts() {
      this.loading = true;
      const { sortBy, sortDesc, page, itemsPerPage } = this.options;

      var totalResult = itemsPerPage;
      //Show All Items
      if (totalResult == -1) {
        this.contractFilterGroup.pageNumber = -1;
        this.contractFilterGroup.pageSize = -1;
      } else {
        this.contractFilterGroup.pageNumber = page;
        this.contractFilterGroup.pageSize = itemsPerPage;
      }

      this.filterContractsAction(this.contractFilterGroup)
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
              @click="contractFormDialog = true"
            >Add Contract</v-btn>
          </v-col>
        </v-row>

        <v-row justify="center" align="center" class="mx-3">
          <v-col cols="12">
            <v-card raised>
              <v-card-subtitle v-show="!keywordEmpty">
                Keyword :
                <v-chip class="mx-2">{{ contractFilterGroup.keyword }}</v-chip>
              </v-card-subtitle>
              <v-card-subtitle v-show="!fromdateEmpty">
                From Date :
                <v-chip class="mx-2">{{ contractFilterGroup.keyword }}</v-chip>
              </v-card-subtitle>
              <v-card-subtitle v-show="!todateEmpty">
                To Date :
                <v-chip class="mx-2">{{ contractFilterGroup.todate }}</v-chip>
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
                disable-sort
              >
                <template v-slot:top>
                  <v-toolbar flat class="mb-5">
                    <v-btn color="primary" block class="ma-2" :disabled="isLoading" @click="contractFilterDialog = true">
                      <v-icon left>mdi-magnify</v-icon>Filter Contract
                    </v-btn>
                  </v-toolbar>
                </template>
                <template v-slot:item="props">
                  <tr @click="showContract(props.item)">
                    <td>{{props.item.uid}}</td>
                    <td>{{props.item.name}}</td>
                  </tr>
                </template>
              </v-data-table>
            </v-card>
          </v-col>
        </v-row>
      </v-container>

      <v-dialog v-model="contractFormDialog" persistent fullscreen hideOverlay>
        <contract-form
          :reset="contractFormDialog"
          :editmode="false"
          @created="showContract($event)"
          @close="contractFormDialog = false"
        ></contract-form>
      </v-dialog>

      <v-dialog v-model="contractFilterDialog" persistent maxWidth="1200px" hideOverlay>
        <contract-filter-form
          @submitFilter="initContractFilter($event)"
          @close="contractFilterDialog = false"
          :reset="contractFilterDialog"
        ></contract-filter-form>
      </v-dialog>
    </v-content>
  </v-app>
</template>
