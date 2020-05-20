<template>
  <v-dialog
    v-model="dialog"
    :fullscreen="dialogStyle.fullscreen"
    :hide-overlay="dialogStyle.hideOverlay"
    :persistent="dialogStyle.persistent"
    :max-width="dialogStyle.maxWidth"
    transition="dialog-bottom-transition"
  >
    <v-card>
      <v-card-text>
        <v-container>
          <v-row>
            <v-col cols="12">
              <v-text-field label="Penalty" type="number" step="0.01" v-model="data.penalty"></v-text-field>
            </v-col>
          </v-row>
        </v-container>
      </v-card-text>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn color="blue darken-1" text @click="close()">Close</v-btn>
        <v-btn color="blue darken-1" text @click="makePayment()">Save</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

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
    editMode: {
      type: Boolean,
      default: false
    },
    uid: {
      type: String,
      default: ""
    },
    dialogStyle: {
      type: Object,
      default: () => ({
        persistent: true,
        maxWidth: "30%",
        fullscreen: false,
        hideOverlay: true
      })
    },

    dialog: {
      type: Boolean,
      default: () => false
    }
  },
  data() {
    return {
      data: new Form({
        penalty: ""
      })
    };
  },

  computed: {
    isLoading() {
      return this.$store.getters.isLoading;
    }
  },
  watch: {
    dialog: function(val) {
      if (val) {
        this.data.reset();
      }
    },

    uid: function(val) {
      this.showLoadingAction();
      this.$Progress.start();
      this.getRentalPaymentAction({ uid: this.uid })
        .then(data => {
          this.data = data.data;
          this.$Progress.finish();
          this.endLoadingAction();
        })
        .catch(error => {
          Toast.fire({
            icon: "warning",
            title: "Fail to retrieve the rental!!!!! "
          });
          this.$Progress.finish();
          this.endLoadingAction();
        });
    }
  },
  mounted() {},
  methods: {
    ...mapActions({
      makePaymentAction: "makePayment",
      getRentalPaymentAction: "getRentalPayment",
      showLoadingAction: "showLoadingAction",
      endLoadingAction: "endLoadingAction"
    }),
    close() {
      this.$emit("close");
    },
    makePayment() {
      this.showLoadingAction();
      this.$Progress.start();
      this.makePaymentAction(this.data)
        .then(data => {
         
          Toast.fire({
            icon: "success",
            title: "Successful Created. "
          });
          this.$Progress.finish();
          this.endLoadingAction();
          this.$emit('makePayment', data.data);
          this.close();
        })
        .catch(error => {
          Toast.fire({
            icon: "warning",
            title: "Something went wrong!!!!! "
          });
          this.$Progress.finish();
          this.endLoadingAction();
          this.close();
        });
    }
  }
};
</script>