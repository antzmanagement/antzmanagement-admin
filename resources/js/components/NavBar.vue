<template>
  <div>
    <v-theme-provider dark>
      <v-navigation-drawer v-model="drawer" app clipped color="#222831">
        <v-list dense>
          <v-list-item v-for="item in items" :key="item.text" :to="{ name : item.name}">
            <v-list-item-action>
              <v-icon>{{ item.icon }}</v-icon>
            </v-list-item-action>
            <v-list-item-content>
              <v-list-item-title>{{ item.text }}</v-list-item-title>
            </v-list-item-content>
          </v-list-item>
        </v-list>

        <v-list :style="{ position : 'absolute', bottom : '0', left : '0'}">
          <v-list-item class="mt-4" link>
            <v-list-item-action>
              <v-icon>mdi-plus-circle-outline</v-icon>
            </v-list-item-action>
            <v-list-item-title>Browse Channels</v-list-item-title>
          </v-list-item>
          <v-list-item link>
            <v-list-item-action>
              <v-icon>mdi-settings</v-icon>
            </v-list-item-action>
            <v-list-item-title>Manage Subscriptions</v-list-item-title>
          </v-list-item>
        </v-list>
      </v-navigation-drawer>
    </v-theme-provider>

    <v-theme-provider dark>
      <v-app-bar app clipped-left  color="#424242">
        <v-app-bar-nav-icon @click.stop="drawer = !drawer" />
        <v-toolbar-title class="align-center">Antz Management</v-toolbar-title>
        <v-spacer />
        <v-row align="center" style="max-width: 650px">
          <v-text-field
            :append-icon-cb="() => {}"
            placeholder="Search..."
            single-line
            append-icon="mdi-magnify"
            hide-details
          />
        </v-row>
      </v-app-bar>
    </v-theme-provider>
  </div>
</template>

<script>
export default {
  props: {
    source: String
  },
  data: () => ({
    drawer: null,
    items: [
      {
        icon: "mdi-home-outline",
        text: "Home",
        name: "home"
      },
      {
        icon: "mdi-view-dashboard-outline",
        text: "Dashboard",
        name: "management"
      },
      {
        icon: "mdi-account-multiple",
        text: "User Management",
        name: "usermanagement"
      },
      { icon: "mdi-home-city-outline", text: "Room Management", name: "rooms" },
      { icon: "mdi-screwdriver", text: "Room Maintenance", name: "" }
    ]
  })
};
</script>