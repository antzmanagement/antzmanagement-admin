

<script>
import { mapActions } from "vuex";
import { checkIsAccessible, _ } from "../common/common-function";
export default {
  props: {
    source: String,
    returnRole: Function,
  },
  data: () => ({
    drawer: null,
    role: {},
    items: [
      // {
      //   icon: "mdi-home-outline",
      //   text: "Home",
      //   name: "home",
      //   module: "home"
      // },
      {
        icon: "mdi-view-dashboard-outline",
        text: "Dashboard",
        name: "management",
        module: "management",
      },
      {
        icon: "mdi-account-cog",
        text: "Staff",
        name: "staffs",
        module: "staff",
      },
      {
        icon: "mdi-account-tie",
        text: "Owner",
        name: "owners",
        module: "owner",
      },
      {
        icon: "mdi-account-group",
        text: "Tenant",
        name: "tenants",
        module: "tenant",
      },
      {
        icon: "mdi-home-group",
        text: "Room Type",
        name: "roomtypes",
        module: "roomType",
      },
      {
        icon: "mdi-washing-machine",
        text: "Service",
        name: "services",
        module: "service",
      },
      {
        icon: "mdi-home-city-outline",
        text: "Room",
        name: "rooms",
        module: "room",
      },
      {
        icon: "mdi-bank-plus",
        text: "Room Contract",
        name: "roomcontracts",
        module: "roomContract",
      },
      {
        icon: "mdi-home-currency-usd",
        text: "Rental Payment",
        name: "rentalpayments",
        module: "rentalPayment",
      },
      // {
      //   icon: "mdi-home-currency-usd",
      //   text: "Claim",
      //   name: "claims",
      //   module: "claim",
      // },
      {
        icon: "mdi-screwdriver",
        text: "Room Maintenance",
        name: "maintenances",
        module: "roomMaintenance",
      },
      // {
      //   icon: "mdi-chair-rolling",
      //   text: "Property",
      //   name: "properties",
      //   module: "property",
      // },
      // {
      //   icon: "mdi-file-document-edit-outline",
      //   text: "Contract",
      //   name: "contracts",
      //   module: "contract",
      // },
    ],
  }),
  computed: {
    isLoading() {
      return this.$store.getters.isLoading;
    },
    userModules() {
      return this.$store.getters.userModules;
    },
    roleNavItems() {
      let roleItems = [];
      let self = this;
      _.forEach(this.items, function (item) {
        if (checkIsAccessible(self.role.name, item.module, "nav") == true) {
          roleItems.push(item);
        }
      });
      return roleItems;
    },
  },
  created() {
    this.showLoadingAction();
    this.authenticationAction()
      .then((res) => {
        this.role = res.data.data.role;
        this.endLoadingAction();
        try {
          if (this.returnRole) {
            this.returnRole(this.role);
          }
        } catch (error) {
          console.log(error);
        }
      })
      .catch((error) => {
        console.log("error");
        this.endLoadingAction();
        this.$router.push("/login");
      });
  },
  methods: {
    ...mapActions({
      authenticationAction: "authentication",
      getRole: "getRoleAction",
      showLoadingAction: "showLoadingAction",
      endLoadingAction: "endLoadingAction",
      logoutAction: "logout",
    }),
    logout() {
      if (this.logoutAction()) {
        this.$router.push("/login");
      }
    },
  },
};
</script>

<template>
  <div>
    <v-theme-provider dark>
      <v-navigation-drawer v-model="drawer" app clipped color="#222831">
        <v-list dense>
          <v-list-item
            v-for="item in roleNavItems"
            :key="item.text"
            :to="{ name: item.name }"
          >
            <v-list-item-action>
              <v-icon>{{ item.icon }}</v-icon>
            </v-list-item-action>
            <v-list-item-content>
              <v-list-item-title>{{ item.text }}</v-list-item-title>
            </v-list-item-content>
          </v-list-item>
        </v-list>

        <v-list :style="{ position: 'absolute', bottom: '0', left: '0' }" dense>
          <v-list-item @click="logout">
            <v-list-item-action>
              <v-icon>mdi-logout</v-icon>
            </v-list-item-action>
            <v-list-item-content>
              <v-list-item-title>Logout</v-list-item-title>
            </v-list-item-content>
          </v-list-item>
        </v-list>
      </v-navigation-drawer>
    </v-theme-provider>

    <v-theme-provider dark>
      <v-app-bar app clipped-left color="#424242">
        <v-app-bar-nav-icon @click.stop="drawer = !drawer" />
        <v-toolbar-title class="align-center">Antz Management</v-toolbar-title>
        <v-spacer />
        <!-- <v-row align="center" style="max-width: 650px">
          <v-text-field
            :append-icon-cb="() => {}"
            placeholder="Search..."
            single-line
            append-icon="mdi-magnify"
            hide-details
          />
        </v-row> -->
      </v-app-bar>
    </v-theme-provider>
  </div>
</template>