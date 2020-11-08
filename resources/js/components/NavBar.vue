

<script>
import { mapActions } from "vuex";
export default {
  props: {
    source: String,
  },
  data: () => ({
    drawer: null,
    role: {},
    items: [
      // {
      //   icon: "mdi-home-outline",
      //   text: "Home",
      //   name: "home",
      //   modulename: "home"
      // },
      {
        icon: "mdi-view-dashboard-outline",
        text: "Dashboard",
        name: "management",
        modulename: "management",
      },
      {
        icon: "mdi-account-multiple",
        text: "User",
        name: "usermanagement",
        modulename: "user",
      },
      {
        icon: "mdi-home-city-outline",
        text: "Room",
        name: "rooms",
        modulename: "room",
      },
      {
        icon: "mdi-bank-plus",
        text: "Room Contract",
        name: "roomcontracts",
        modulename: "roomcontract",
      },
      {
        icon: "mdi-home-currency-usd",
        text: "Rental Payment",
        name: "rentalpayments",
        modulename: "rentalpayment",
      },
      {
        icon: "mdi-screwdriver",
        text: "Room Maintenance",
        name: "maintenances",
        modulename: "maintenance",
      },
      // {
      //   icon: "mdi-chair-rolling",
      //   text: "Property",
      //   name: "properties",
      //   modulename: "property",
      // },
      {
        icon: "mdi-washing-machine",
        text: "Service",
        name: "services",
        modulename: "service",
      },
      // {
      //   icon: "mdi-file-document-edit-outline",
      //   text: "Contract",
      //   name: "contracts",
      //   modulename: "contract",
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
      switch (this.role.name) {
        case "superadmin":
          return this.items;
          break;
        case "admin":
          return this.items;
          break;
        case "manager":
          return this.items.filter(function (item) {
            return item.modulename != "user";
          });
          break;
        case "cashier":
          return this.items.filter(function (item) {
            return (
              item.modulename == "rentalpayment" ||
              item.modulename == "management"
            );
          });
          break;
        case "tenant":
          return [];
          break;
        case "owner":
          return [];
          break;
        default:
          return [];
          break;
      }
    },
  },
  created() {
    this.showLoadingAction();
    this.authenticationAction()
      .then((res) => {
        this.role = res.data.data.role;
        this.endLoadingAction();
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