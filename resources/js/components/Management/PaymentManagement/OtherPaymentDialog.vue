

<script>
import { validationMixin } from "vuelidate";
import {
  required,
  minLength,
  maxLength,
  decimal,
} from "vuelidate/lib/validators";
import { mapActions } from "vuex";
import { notEmptyLength, _ } from "../../../common/common-function";
export default {
  props: {
    editMode: {
      type: Boolean,
      default: false,
    },
    otherpayments: {
      type: Array,
      default: () => [],
    },
  },
  data() {
    return {
      otherPaymentDialog: false,
      data: new Form({
        otherpayments: this.otherpayments,
      }),
    };
  },

  computed: {
    isLoading() {
      return this.$store.getters.isLoading;
    },
  },
  watch: {
    otherPaymentDialog: function (val) {
      if (val) {
        this.data = new Form({
          otherpayments: this.otherpayments,
        });
      }
    },
    otherpayments: function (val) {
      if (_.isArray(val) && notEmptyLength(val)) {
        this.data.otherpayments = val;
      } else {
        this.data.otherpayments = [];
      }
    },
  },
  mounted() {},
  updated() {},
  methods: {
    ...mapActions({
      showLoadingAction: "showLoadingAction",
      endLoadingAction: "endLoadingAction",
    }),
    submit() {
      this.$emit("submit", this.data);
      this.otherPaymentDialog = false;
    },
  },
};
</script>







<template>
  <v-dialog v-model="otherPaymentDialog">
    <template v-slot:activator="{ on }">
      <span class="d-inline-block" v-on="on">
        <slot>
          <v-btn color="yellow darken-4" text>Other Payment</v-btn>
        </slot>
      </span>
    </template>
    <v-card>
      <v-toolbar dark color="primary">
        <v-btn icon dark @click="otherPaymentDialog = false">
          <v-icon>mdi-close</v-icon>
        </v-btn>
        <v-toolbar-title>Other Payment</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-toolbar-items>
          <v-btn
            dark
            text
            :disabled="isLoading || !editMode"
            @click="
              () => {
                this.submit();
              }
            "
            >Save</v-btn
          >
        </v-toolbar-items>
      </v-toolbar>
      <v-card-text>
        <v-container>
          <v-row>
            <v-col cols="12">
              <v-data-table
                :items="otherpayments || []"
                :headers="[
                  {
                    text: 'Name',
                  },
                  {
                    text: 'Price',
                  },
                  {
                    text: 'Actions',
                  },
                ]"
              >
                <template v-slot:top>
                  <v-toolbar flat color="white">
                    <v-spacer></v-spacer>
                    <v-btn
                      :disabled="!editMode"
                      @click="
                        () => {
                          otherpayments.push({
                            id: moment().valueOf(),
                            name: '',
                            price: 0,
                          });
                        }
                      "
                      >Add Other Payment</v-btn
                    >
                  </v-toolbar>
                </template>
                <template v-slot:item="props">
                  <tr>
                    <td class="text-truncate">
                      <v-text-field
                        :disabled="!editMode"
                        label="Item Name"
                        v-model="props.item.name"
                      ></v-text-field>
                    </td>
                    <td class="text-truncate">
                      <v-text-field
                        :disabled="!editMode"
                        label="Item Price"
                        type="number"
                        step="0.01"
                        v-model="props.item.price"
                      ></v-text-field>
                    </td>
                    <td class="text-truncate">
                      <v-btn
                        :disabled="!editMode"
                        icon
                        color="red"
                        @click="
                          () => {
                            otherpayments = (otherpayments || []).filter(
                              function (item) {
                                return item.id != props.item.id;
                              }
                            );
                          }
                        "
                        ><v-icon>mdi-trash-can-outline</v-icon></v-btn
                      >
                    </td>
                  </tr>
                </template>
              </v-data-table>
            </v-col>
          </v-row>
        </v-container>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>