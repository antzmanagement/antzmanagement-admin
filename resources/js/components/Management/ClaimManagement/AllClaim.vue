
<script>
import { mapActions } from "vuex";
export default {
  data() {
    return {
      claimFormDialog: false,
      claimFilterDialog : false,
      totalDataLength: 0,
      data: [],
      loading: true,
      options: {},
      claimFilterGroup: new Form({
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
          text: "Owner"
        },
        {
          text: "Total Rental"
        },
        {
          text: "Maintenance Fees"
        },
        {
          text: "Admin Fees"
        },
        {
          text: "Other Fees"
        },
      ]
    };
  },
  watch: {
    options: {
      handler() {
        this.getClaims();
      },
      deep: true
    }
  },
  computed: {
    isLoading() {
      return this.$store.getters.isLoading;
    },
    keywordEmpty() {
      return this.helpers.isEmpty(this.claimFilterGroup.keyword);
    },
    fromdateEmpty() {
      return this.helpers.isEmpty(this.claimFilterGroup.fromdate);
    },
    todateEmpty() {
      return this.helpers.isEmpty(this.claimFilterGroup.todate);
    }
  },
  created() {},
  mounted() {
    this.getClaims();
  },
  methods: {
    ...mapActions({
      filterClaimsAction: "filterClaims",
      showLoadingAction: "showLoadingAction",
      endLoadingAction: "endLoadingAction"
    }),
    initClaimFilter(filterGroup) {
      this.claimFilterGroup.reset();
      if (filterGroup) {
        this.claimFilterGroup.selectedRooms = filterGroup.rooms;
        this.claimFilterGroup.rooms = filterGroup.rooms.map(function(
          claimType
        ) {
          return claimType.id;
        });
        this.claimFilterGroup.keyword = filterGroup.keyword;
      }
      this.getClaims();
    },
    showClaim($data) {
      this.$router.push("/claim/" + $data.uid);
    },
    getClaims() {
      this.loading = true;
      const { sortBy, sortDesc, page, itemsPerPage } = this.options;

      var totalResult = itemsPerPage;
      //Show All Items
      if (totalResult == -1) {
        this.claimFilterGroup.pageNumber = -1;
        this.claimFilterGroup.pageSize = -1;
      } else {
        this.claimFilterGroup.pageNumber = page;
        this.claimFilterGroup.pageSize = itemsPerPage;
      }

      this.filterClaimsAction(this.claimFilterGroup)
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
              @click="claimFormDialog = true"
            >Add Claim</v-btn>
          </v-col>
        </v-row>

        <v-row justify="center" align="center" class="mx-3">
          <v-col cols="12">
            <v-card raised>
              <v-card-subtitle v-show="!keywordEmpty">
                Keyword :
                <v-chip class="mx-2">{{ claimFilterGroup.keyword }}</v-chip>
              </v-card-subtitle>
              <v-card-subtitle v-show="!fromdateEmpty">
                From Date :
                <v-chip class="mx-2">{{ claimFilterGroup.keyword }}</v-chip>
              </v-card-subtitle>
              <v-card-subtitle v-show="!todateEmpty">
                To Date :
                <v-chip class="mx-2">{{ claimFilterGroup.todate }}</v-chip>
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
                    <v-btn color="primary" block class="ma-2" :disabled="isLoading" @click="claimFilterDialog = true">
                      <v-icon left>mdi-magnify</v-icon>Filter Claim
                    </v-btn>
                  </v-toolbar>
                </template>
                <template v-slot:item="props">
                  <tr @click="showClaim(props.item)">
                    <td>{{props.item.uid}}</td>
                    <td>{{props.item.owner.name}}</td>
                    <td>{{props.item.rental_fees}}</td>
                    <td>{{props.item.maintenance_fees}}</td>
                    <td>{{props.item.admin_fees}}</td>
                    <td>{{props.item.other_fees}}</td>
                  </tr>
                </template>
              </v-data-table>
            </v-card>
          </v-col>
        </v-row>
      </v-container>

      <v-dialog v-model="claimFormDialog" persistent fullscreen hideOverlay>
        <claim-form
          :reset="claimFormDialog"
          :editmode="false"
          @created="showClaim($event)"
          @close="claimFormDialog = false"
        ></claim-form>
      </v-dialog>

      <v-dialog v-model="claimFilterDialog" persistent maxWidth="1200px" hideOverlay>
        <claim-filter-form
          @submitFilter="initClaimFilter($event)"
          @close="claimFilterDialog = false"
          :reset="claimFilterDialog"
        ></claim-filter-form>
      </v-dialog>
    </v-content>
  </v-app>
</template>
