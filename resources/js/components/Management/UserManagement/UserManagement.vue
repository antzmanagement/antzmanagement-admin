<template>
  <v-app id="inspire">
    <navbar></navbar>
    <v-content class="grey lighten-2">
      <v-container>
        <!-- cards -->
        <v-row justify="center" align="center" v-for="(item, i) in items" :key="i">
          <v-col col="12">
            <v-card class="mx-3" color="#30475e" :to="item.path">
              <h2  class="text-center p-5 font-weight-black grey--text text--lighten-3">
                <v-icon color="grey lighten-3" large class="mx-3">{{item.icon}}</v-icon>
                {{item.text}}
              </h2>
            </v-card>
          </v-col>
        </v-row>
        <!-- End of cards -->
      </v-container>
    </v-content>
  </v-app>
</template>

<script>
export default {
  data: () => ({
    colors : [
      '#c1a57b',
      '#c1a57b',  
      '#c1a57b',
      '',
    ],
    items: [
      {
        icon: "mdi-account-cog-outline",
        text: "Management Staff",
        path: "management"
      },
      {
        icon: "mdi-nature-people",
        text: "Tenant",
        path: "tenants"
      },
      {
        icon: "mdi-account-tie-outline",
        text: "Owner",
        path: "management"
      },
    ]
  }),
  created() {
  }
};
</script>