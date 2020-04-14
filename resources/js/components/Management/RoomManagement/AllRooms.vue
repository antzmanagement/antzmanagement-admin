<template>
  <v-app>
    <navbar></navbar>
    <v-content>
      <v-container class="fill-height" fluid>
        <loading></loading>
        <v-row justify="center" align="center" class="ma-3">
          <v-col cols="12">
            <room-form :editMode="false" @created="showRoom($event)" ></room-form>
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
      headers: [
        {
          text: "Unit No",
          align: "start",
          sortable: false,
          value: "name"
        },
        { text: "Price", value: "price" }
      ],
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
  created(){
    this.$vuetify.theme.dark = true;
  },
  mounted() {
    // this.getRooms();
  },
  methods: {
    ...mapActions({
      getRoomsAction: "getRooms",
      showLoadingAction: "showLoadingAction",
      endLoadingAction: "endLoadingAction"
    }),
    showRoom($data) {
      this.$router.push("/room/" + $data.uid);
    },
    getRooms() {
      this.loading = true;
      console.log("here")
      const { sortBy, sortDesc, page, itemsPerPage } = this.options;

      var totalResult = itemsPerPage;
      //Show All Items
      if (totalResult == -1) {
        totalResult = this.totalDataLength;
      }
      this.getRoomsAction({ pageNumber: page, pageSize: totalResult })
        .then((data) => {
          this.data = data.data;
          this.totalDataLength = data.totalResult;
        })
        .catch(error => {
          Toast.fire({
            icon: "warning",
            title: "Something went wrong... "
          });
        });
      this.loading = false;
    }
  }
};
</script>