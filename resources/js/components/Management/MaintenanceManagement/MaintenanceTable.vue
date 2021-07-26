
<script>
import { validationMixin } from "vuelidate";
import {
  required,
  minLength,
  maxLength,
  decimal,
} from "vuelidate/lib/validators";
import { mapActions } from "vuex";
import { _ } from "../../../common/common-function";
export default {
  props: {
    data: {
      type: Array,
      default: [],
    },
    total: {
      type: Number,
      default: 0,
    },
    hideFilter: {
      type: Boolean,
      default: false,
    },
    hideDownloadExcel: {
      type: Boolean,
      default: false,
    },
    hideFooter: {
      type: Boolean,
      default: false,
    },
    loading: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      options: {},
      tableData: [],
      headers: [
        {
          text: "Unit No",
        },
        {
          text: "Property",
        },
        { text: "Repair Type" },
        { text: "Maintenance Status" },
        { text: "Price (RM)" },
        { text: "Claimed By" },
      ],
      maintenanceFilterDialogConfig: {
        buttonStyle: {
          block: true,
          class: "ma-2",
          text: "Filter",
          icon: "mdi-magnify",
          isIcon: false,
          color: "primary",
        },
        dialogStyle: {
          persistent: true,
          maxWidth: "1200px",
          fullscreen: false,
          hideOverlay: true,
        },
      },
      excelData: [],
      excelFields: {
        id: "id",
        uid: "uid",
        room: {
          field: "room",
          callback: (value) => {
            return _.get(value, `name`) || "N/A";
          },
        },
        room_id: {
          field: "room",
          callback: (value) => {
            return _.get(value, `id`) || "N/A";
          },
        },
        property: {
          field: "property",
          callback: (value) => {
            return _.get(value, `name`) || "N/A";
          },
        },
        property_id: {
          field: "property",
          callback: (value) => {
            return _.get(value, `id`) || "N/A";
          },
        },
        price: "price",
        maintenance_type: "maintenance_type",
        maintenance_status: "maintenance_status",
        remark: "remark",
        created_at: "created_at",
        updated_at: "updated_at",
      },
    };
  },

  computed: {
    isLoading() {
      return this.$store.getters.isLoading;
    },
  },
  watch: {
    options: {
      handler() {},
      deep: true,
    },
    data: {
      handler(val, oldVal) {
        console.log("watch");
        console.log(val);
      },
      deep: true,
    },
  },
  mounted() {
    console.log(this.hideFilter);
  },
  methods: {},
};
</script>

<template>
  <v-data-table
    :headers="headers"
    :items="data || []"
    :options.sync="options"
    :server-items-length="total"
    :loading="loading"
    disable-sort
    :hide-default-footer="hideFooter"
  >
    <template v-slot:top>
      <v-toolbar flat v-if="!hideFilter">
        <maintenance-filter-dialog
          :buttonStyle="maintenanceFilterDialogConfig.buttonStyle"
          :dialogStyle="maintenanceFilterDialogConfig.dialogStyle"
          @submitFilter="initMaintenanceFilter($event)"
        ></maintenance-filter-dialog>
      </v-toolbar>
      <v-toolbar
        flat
        class="mb-5 justify-end d-flex"
        v-if="
          _.isArray(excelData) && !_.isEmpty(excelData) && !hideDownloadExcel
        "
      >
        <download-excel
          :header="`All_Maintenance_${moment().format('YYYY_MM_DD')}`"
          :name="`All_Maintenance_${moment().format('YYYY_MM_DD')}.csv`"
          type="csv"
          :data="excelData || []"
          :fields="excelFields || {}"
          ><v-btn text color="primary">Download as Excel</v-btn></download-excel
        >
      </v-toolbar>
    </template>

    <template v-slot:item="props">
      <tr>
        <slot name="item" :item="props.item">
          <td class="text-truncate">
            {{ _.get(props.item, `room.name`) || "N/A" }}
          </td>
          <td class="text-truncate">
            {{ _.get(props.item, `property.text`) || "N/A" }}
          </td>
          <td class="text-truncate">{{ props.item.maintenance_type }}</td>
          <td class="text-truncate">{{ props.item.maintenance_status }}</td>
          <td class="text-truncate">{{ props.item.price }}</td>
          <td class="text-truncate">
            {{
              _.get(props.item, ["claimed_by_owner"])
                ? _.get(props.item, ["owner", "name"]) || "N/A"
                : _.get(props.item, ["claimed_by_tenant"])
                ? _.get(props.item, ["tenant", "name"]) || "N/A"
                : "N/A"
            }}
          </td>
        </slot>
      </tr>
    </template>
  </v-data-table>
</template>
