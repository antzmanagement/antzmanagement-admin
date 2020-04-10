<template>
  <v-app id="inspire">
    <navbar></navbar>
    <v-content>
      <v-container>
        <!-- cards -->
        <v-row justify="center" align="center" v-for="item in items" :key="item.text">
          <v-col col="12">
            <v-card class="mx-3" color="#1b262c" :to="item.path">
              <h2 :style="{}" class="text-center p-5">
                <v-icon color="grey darken-1" large class="mx-3">{{item.icon}}</v-icon>
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
    
    this.$vuetify.theme.dark = true;
  }
};
</script>