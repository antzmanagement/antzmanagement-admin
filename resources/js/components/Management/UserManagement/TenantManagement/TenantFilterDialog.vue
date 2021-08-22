

<script>
import { validationMixin } from "vuelidate";
import {
  required,
  minLength,
  maxLength,
  decimal,
} from "vuelidate/lib/validators";
import { mapActions } from "vuex";
import { getDaysInMonth } from "../../../../common/common-function";

export default {
  props: {
    editMode: {
      type: Boolean,
      default: false,
    },
    uid: {
      type: String,
      default: "",
    },
    buttonStyle: {
      type: Object,
      default: () => ({
        block: true,
        color: "primary",
        class: "ma-1",
        text: "Tenant Filter",
        icon: "",
        isIcon: false,
      }),
    },
    dialogStyle: {
      type: Object,
      default: () => ({
        persistent: true,
        maxWidth: "",
        fullscreen: true,
        hideOverlay: true,
      }),
    },
  },
  data() {
    return {
      fromdatemenu: false,
      todatemenu: false,
      dialog: false,
      roomTypes: [],
      rooms: [],
      staffs: [],
      approachmethods: ["fb", "friend", "banner", "others"],
      genders: ["male", "female"],
      religions: [
        "islam",
        "buddhism",
        "christianity",
        "hinduism",
        "taoism",
        "no-region",
        "others",
      ],
      data: new Form({
        keyword: "",
        gender: "",
        approach_method: "",
        religion: "",
        pic: "",
        birthdayFromMonth: "",
        birthdayFromDay: "",
        birthdayToMonth: "",
        birthdayToDay: "",
        birthdayfromdate: "",
        birthdaytodate: "",
      }),
    };
  },

  computed: {
    isLoading() {
      return this.$store.getters.isLoading;
    },
    fromDays() {
      return (
        _.map(
          _.range(getDaysInMonth(this.data.birthdayFromMonth)),
          (i) => i + 1
        ) || []
      );
    },
    toDays() {
      return (
        _.map(
          _.range(getDaysInMonth(this.data.birthdayToMonth)),
          (i) => i + 1
        ) || []
      );
    },
  },
  watch: {
    dialog: function (val) {
      if (val) {
        this.data.reset();
      }
    },
  },
  mounted() {
    this.showLoadingAction();
    let promises = [];
    promises.push(this.getStaffsAction({ pageNumber: -1, pageSize: -1 }));
    promises.push(this.getRoomsAction({ pageNumber: -1, pageSize: -1 }));

    Promise.all(promises)
      .then(([staffRes, roomRes]) => {
        this.staffs = _.get(staffRes, ["data"]) || [];
        this.rooms = _.get(roomRes, ["data"]) || [];
        this.endLoadingAction();
      })
      .catch((err) => {
        console.log(err);
        Toast.fire({
          icon: "warning",
          title: "Something went wrong...",
        });
        this.endLoadingAction();
      });
  },
  methods: {
    ...mapActions({
      getStaffsAction: "getStaffs",
      getRoomsAction: "getRooms",
      showLoadingAction: "showLoadingAction",
      endLoadingAction: "endLoadingAction",
    }),
    submitFilter() {
      this.$emit("submitFilter", this.data);
      this.dialog = false;
    },
  },
};
</script>
<template>
  <v-dialog
    v-model="dialog"
    :fullscreen="dialogStyle.fullscreen"
    :hide-overlay="dialogStyle.hideOverlay"
    :persistent="dialogStyle.persistent"
    :max-width="dialogStyle.maxWidth"
    transition="dialog-bottom-transition"
  >
    <template v-slot:activator="{ on }">
      <v-btn
        :class="buttonStyle.class"
        tile
        :color="buttonStyle.color"
        :block="buttonStyle.block"
        :icon="buttonStyle.isIcon"
        v-on="on"
        :disabled="isLoading"
      >
        <v-icon left>{{ buttonStyle.icon }}</v-icon>
        {{ buttonStyle.text }}
      </v-btn>
    </template>
    <v-card>
      <v-toolbar dark color="primary">
        <v-btn icon dark @click="dialog = false">
          <v-icon>mdi-close</v-icon>
        </v-btn>
        <v-toolbar-title>Tenant Filter</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-toolbar-items>
          <v-btn dark text :disabled="isLoading" @click="submitFilter()"
            >Apply</v-btn
          >
        </v-toolbar-items>
      </v-toolbar>
      <v-card-text>
        <v-container>
          <v-row>
            <v-col cols="6">

              <div>Birthday From Date</div>
              <div class="d-flex justify-start align-center">
                <v-select
                  v-model="data.birthdayFromMonth"
                  :items="_.map(_.range(12), (i) => i + 1)"
                  label="Month"
                  class="mr-2"
                ></v-select>
                <v-select
                  v-model="data.birthdayFromDay"
                  :items="fromDays"
                  label="Day"
                ></v-select>
              </div>
            </v-col>
            <v-col cols="6">

              <div>Birthday To Date</div>
              <div class="d-flex justify-start align-center">
                <v-select
                  v-model="data.birthdayToMonth"
                  :items="_.map(_.range(12), (i) => i + 1)"
                  label="Month"
                  class="mr-2"
                ></v-select>
                <v-select
                  v-model="data.birthdayToDay"
                  :items="toDays"
                  label="Day"
                ></v-select>
              </div>
            </v-col>
            <v-col cols="12">
              <v-text-field
                label="Keyword Search(Name, IcNo)"
                :maxlength="300"
                v-model="data.keyword"
              ></v-text-field>
            </v-col>
            <v-col cols="12">
              <v-text-field
                label="Occupation"
                :maxlength="300"
                v-model="data.occupation"
              ></v-text-field>
            </v-col>
            <v-col cols="12">
              <v-text-field
                label="Phone"
                :maxlength="300"
                v-model="data.tel"
              ></v-text-field>
            </v-col>
            <v-col cols="12">
              <v-text-field
                label="State"
                :maxlength="300"
                v-model="data.state"
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="12">
              <v-autocomplete
                v-model="data.gender"
                :items="genders || []"
                label="Gender"
              ></v-autocomplete>
            </v-col>
            <!-- <v-col cols="12" md="12">
              <v-autocomplete
                v-model="data.religion"
                :items="religions || []"
                label="Religion"
              ></v-autocomplete>
            </v-col> -->
            <v-col cols="12" md="12">
              <v-autocomplete
                v-model="data.approach_method"
                :items="approachmethods || []"
                label="How you know us?"
              ></v-autocomplete>
            </v-col>
            <v-col cols="12">
              <v-autocomplete
                v-model="data.pic"
                :item-text="(item) => helpers.capitalizeFirstLetter(item.name)"
                :items="staffs || []"
                label="Person In Charge"
                chips
                deletable-chips
                return-object
              >
                <!-- <template v-slot:append>
                  <room-type-form
                    :editMode="false"
                    :dialogStyle="roomFormDialogConfig.dialogStyle"
                    :buttonStyle="roomFormDialogConfig.buttonStyle"
                    @created="appendRoomTypeList($event)"
                  ></room-type-form>
                </template>-->
              </v-autocomplete>
            </v-col>
            <v-col cols="12">
              <v-autocomplete
                v-model="data.room"
                :item-text="(item) => helpers.capitalizeFirstLetter(item.name)"
                :items="rooms || []"
                label="Room"
                chips
                deletable-chips
                :return-object="true"
              ></v-autocomplete>
            </v-col>
          </v-row>
        </v-container>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>