<template>
  <v-app>
    <navbar></navbar>
    <v-content>
      <v-container class="fill-height" fluid>
        <loading></loading>
        <v-row justify="center" align="center" class="ma-3">
          <v-col cols="12">
            <tenant-form :editMode="false" @created="showTenant($event)"></tenant-form>
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
              <template v-slot:item="props">
                <tr @click="showTenant(props.item.uid)">
                  <td>{{props.item.name}}</td>
                  <td>{{props.item.icno}}</td>
                  <td>{{props.item.tel1}}</td>
                  <td>{{props.item.email}}</td>
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
      headers: [
        {
          text: "Name",
          align: "start",
          sortable: false,
          value: "name"
        },
        { text: "Ic No", value: "icno" },
        { text: "Phone", value: "tel1" },
        { text: "Email", value: "email" }
      ]
    };
  },
  watch: {
    options: {
      handler() {
        this.getTenants();
      },
      deep: true
    }
  },
  created(){

    this.$vuetify.theme.dark = true;
  },
  mounted() {
    this.getTenants();
  },
  methods: {
    ...mapActions({
      getTenantsAction: "getTenants",
      showLoadingAction: "showLoadingAction",
      endLoadingAction: "endLoadingAction"
    }),
    showTenant($uid){
      this.$router.push('/tenant/'+$uid);
    },
    getTenants() {
      this.loading = true;
      const { sortBy, sortDesc, page, itemsPerPage } = this.options;

      var totalResult = itemsPerPage;
      //Show All Items
      if (totalResult == -1) {
        totalResult = this.totalDataLength;
      }
      this.getTenantsAction({ pageNumber: page, pageSize: totalResult })
        .then(data => {
          console.log(data);
          this.data = data.data;
          this.totalDataLength = data.totalResult;
        })
        .catch(error => {
        Toast.fire({
          icon: "warning",
          title: "Something went wrong... "
        });
          console.log(error.response);
        });
      this.loading = false;
    }
  }
};
</script>