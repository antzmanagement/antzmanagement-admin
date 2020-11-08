
<script>
import { validationMixin } from "vuelidate";
import {
  required,
  minLength,
  maxLength,
  decimal
} from "vuelidate/lib/validators";
import { mapActions } from "vuex";
export default {
  props: {
    reset: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      data: new Form({
        keyword: "",
        fromdate: null,
        todate: null,
        rooms: []
      })
    };
  },

  computed: {
    isLoading() {
      return this.$store.getters.isLoading;
    }
  },
  watch: {
    reset: function(val) {
      if (val) {
        this.data.reset();
      }
    }
  },
  mounted() {
    this.showLoadingAction();
    this.getRoomsAction({ pageNumber: -1, pageSize: -1 })
      .then(data => {
        this.rooms = data.data;
        this.endLoadingAction();
      })
      .catch(error => {
        Toast.fire({
          icon: "warning",
          title: "Something went wrong... "
        });
        this.endLoadingAction();
      });
  },
  methods: {
    ...mapActions({
      getRoomsAction: "getRooms",
      showLoadingAction: "showLoadingAction",
      endLoadingAction: "endLoadingAction"
    }),
    close(){
      this.$emit("close");
    },
    submitFilter() {
      this.$emit("submitFilter", this.data);
      this.close();
    }
  }
};
</script>

<template>
  <v-card>
    <v-toolbar dark color="primary">
      <v-btn icon dark @click="close()">
        <v-icon>mdi-close</v-icon>
      </v-btn>
      <v-toolbar-title v->Filter Properties</v-toolbar-title>
      <v-spacer></v-spacer>
      <v-toolbar-items>
        <v-btn dark text :disabled="isLoading" @click="submitFilter()">Apply</v-btn>
      </v-toolbar-items>
    </v-toolbar>
    <v-card-text>
      <v-container>
        <v-row>
          <v-col cols="12">
            <v-text-field label="Keyword" :maxlength="300" v-model="data.keyword"></v-text-field>
          </v-col>
        </v-row>
        <!-- <v-row>
          <v-col cols="12">
            <v-autocomplete
              v-model="data.rooms"
              :item-text="item => helpers.capitalizeFirstLetter(item.name)"
              :items="rooms"
              label="Room"
              chips
              deletable-chips
              multiple
              :return-object="true"
            ></v-autocomplete>
          </v-col>
        </v-row> -->
      </v-container>
    </v-card-text>
  </v-card>
</template>
