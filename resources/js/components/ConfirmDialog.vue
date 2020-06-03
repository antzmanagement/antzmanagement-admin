<template>
  <v-dialog v-model="dialog" :persistent="dialogStyle.persistent" :max-width="dialogStyle.maxWidth">
    <template v-slot:activator="{ on }">
      <v-btn
        :class="activatorStyle.class"
        tile
        :color="activatorStyle.color"
        :block="activatorStyle.block"
        v-on="on"
        :icon="activatorStyle.isIcon"
        :disabled="isLoading"
      >
        <v-icon left :small="activatorStyle.smallIcon">{{activatorStyle.icon}}</v-icon>
        {{activatorStyle.text}}
      </v-btn>
    </template>

    <v-card>
      <v-card-title :class="titleStyle.class">
        <slot name="header">{{titleStyle.text}}</slot>
      </v-card-title>

      <v-card-text :class="contentStyle.class">
        <slot name="body">{{contentStyle.text}}</slot>
      </v-card-text>

      <v-card-actions>
        <slot name="footer">
          <v-spacer></v-spacer>
          <v-btn
            :class="yesButtonStyle.class"
            text
            :color="yesButtonStyle.color"
            :block="yesButtonStyle.block"
            :disabled="isLoading"
            @click="clicked(true)"
          >
            <v-icon left>{{yesButtonStyle.icon}}</v-icon>
            {{yesButtonStyle.text}}
          </v-btn>
          <v-btn
            :class="noButtonStyle.class"
            text
            :color="noButtonStyle.color"
            :block="noButtonStyle.block"
            :disabled="isLoading"
            @click="clicked(false)"
          >
            <v-icon left>{{noButtonStyle.icon}}</v-icon>
            {{noButtonStyle.text}}
          </v-btn>
        </slot>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
export default {
  props: {
    dialogStyle: {
      type: Object,
      default: () => ({
        persistent: false,
        maxWidth: "600px"
      })
    },
    activatorStyle: {
      type: Object,
      default: () => ({
        block: false,
        color: "",
        class: " headline",
        text: "Are you sure?",
        icon: "",
        isIcon: false,
        smallIcon: false
      })
    },

    titleStyle: {
      type: Object,
      default: () => ({
        text: "Are you sure?",
        class: "ma-1"
      })
    },

    contentStyle: {
      type: Object,
      default: () => ({
        text: "This action will not be able to undo. Do you want to continue?",
        class: ""
      })
    },

    yesButtonStyle: {
      type: Object,
      default: () => ({
        block: false,
        color: "green darken-1",
        class: "",
        text: "Yes",
        icon: ""
      })
    },

    noButtonStyle: {
      type: Object,
      default: () => ({
        block: false,
        color: "red darken-1",
        class: "",
        text: "No",
        icon: ""
      })
    }
  },
  data() {
    return {
      dialog: false
    };
  },

  computed: {
    isLoading() {
      return this.$store.getters.isLoading;
    }
  },
  created() {},
  methods: {
    clicked($isConfirmed) {
      this.$emit("confirmed", $isConfirmed);
      this.dialog = false;
    }
  }
};
</script>