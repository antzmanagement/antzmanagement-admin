
<script>
import { mapActions } from "vuex";
import print from "print-js";
import printCss from "../../../common/printCssStr";
import moment from 'moment';

export default {
  props: {
    item: {
      type: Object,
      default: {},
    },
  },
  data: () => ({
    _: _,
    moment : moment,
    data: {
      otherpayments: [],
      services: [],
      issueby: {},
      sequence: "",
      paymentdate: "",
      receive_from: "",
      paymentmethod: "",
      referenceno: "",
      remark: "",
    },
    uid: new Date().getTime(),
  }),

  computed: {
    isLoading() {
      return this.$store.getters.isLoading;
    },
  },
  created() {},

  methods: {
    ...mapActions({
      showLoadingAction: "showLoadingAction",
      endLoadingAction: "endLoadingAction",
    }),
    isEmpty(data) {
      return this._.isEmpty(data);
    },
    print() {
      this.data = this.item;
      this.showLoadingAction();
      console.log(this.data);
      setTimeout(() => {
        print({
          printable: `printRoomCheck${this.uid}`,
          type: "html",
          style: printCss,
          scanStyles: false,
          font_size: "7pt",
          documentTitle: `Payment Invoice ${this.data.sequence}`,
          onPrintDialogClose: () => console.log("The print dialog was closed"),
          onError: (e) => console.log(e),
        });
        this.endLoadingAction();
        // console.log(`printRoomCheck${this.uid}`);
        // this.$htmlToPaper(`printRoomCheck${this.uid}`);
      }, 2000);
    },
  },
};
</script>

<template>
  <span>
    <span class="btn" @click="print()">
      <slot>
        <v-icon small color="success">mdi-printer </v-icon>
      </slot>
    </span>
    <div class="d-none invoice-box" :id="`printRoomCheck${uid}`">
      <div class="container">
        <div class="row">
          <div class="col-12 h6 font-weight-bold">
            Room Check - {{ _.get(data, ["uid"]) }}
          </div>
        </div>
        <div class="row">
          <div class="divider my-2"></div>
        </div>
        <div class="row">
          <div class="col-auto mb-2">
            <div class="form-label">Room</div>
            <div class="form-content">
              {{ _.get(data, `room.unit`) }}
            </div>
          </div>
        </div>
        <div class="row">
          <div class="divider my-2"></div>
        </div>
        <div class="row">
          <div class="col-auto mb-2">
            <div class="form-label">Category</div>
            <div class="form-content">
              {{ data.category }}
            </div>
          </div>
          <div class="col-auto mb-2">
            <div class="form-label">Checked Date</div>
            <div class="form-content">
              {{
                _.get(data, ["checked_date"])
                  ? moment(data.checked_date).format("YYYY-MM-DD HH:mm")
                  : "N/A" || "N/A"
              }}
            </div>
          </div>
          <div class="col-auto mb-2">
            <div class="form-label">Remark</div>
            <div class="form-content">
              {{ data.remark }}
            </div>
          </div>
        </div>
        <div class="row my-3">
          <div class="col-11 thin-border px-2">
            <div class="form-label mb-2">Maintenance</div>
            <table cellpadding="0" cellspacing="0">
              <tr class="heading">
                <td class="text-truncate">Property</td>
                <td class="text-truncate">Repair Type</td>
                <td class="text-truncate">Maintenance Status</td>
                <td class="text-truncate">Price (RM)</td>
                <td class="text-truncate">Claimed By</td>
                <td class="text-truncate">Maintenance Date</td>
              </tr>

              <tr
                class="item"
                v-for="maintenance in data.maintenances"
                :key="maintenance.uid"
              >
                <td class="text-truncate">
                  {{ _.get(maintenance, `property.text`) || "N/A" }}
                </td>
                <td class="text-truncate">
                  {{ maintenance.maintenance_type }}
                </td>
                <td class="text-truncate">
                  {{ maintenance.maintenance_status }}
                </td>
                <td class="text-truncate">{{ maintenance.price }}</td>
                <td class="text-truncate">
                  {{
                    _.get(maintenance, ["claim_by_owner"])
                      ? _.get(maintenance, ["owner", "name"]) || "N/A"
                      : _.get(maintenance, ["claim_by_tenant"])
                      ? _.get(maintenance, ["tenant", "name"]) || "N/A"
                      : "N/A"
                  }}
                </td>
                <td class="text-truncate">
                  {{
                    _.get(maintenance, ["maintenance_date"])
                      ? moment(maintenance.maintenance_date).format(
                          "YYYY-MM-DD HH:mm"
                        )
                      : "N/A" || "N/A"
                  }}
                </td>
              </tr>
            </table>
          </div>
        </div>

        <div class="row my-3">
          <div class="col-11 thin-border px-2">
            <div class="form-label mb-2">Cleaning</div>
            <table cellpadding="0" cellspacing="0">
              <tr class="heading">
                <td class="text-truncate">Cleaning Type</td>
                <td class="text-truncate">Cleaning Status</td>
                <td class="text-truncate">Price (RM)</td>
                <td class="text-truncate">Claimed By</td>
                <td class="text-truncate">Cleaning Date</td>
              </tr>

              <tr
                class="item"
                v-for="cleaning in data.cleanings"
                :key="cleaning.uid"
              >
                <td class="text-truncate">
                  {{ cleaning.cleaning_type }}
                </td>
                <td class="text-truncate">
                  {{ cleaning.cleaning_status }}
                </td>
                <td class="text-truncate">{{ cleaning.price }}</td>
                <td class="text-truncate">
                  {{
                    _.get(cleaning, ["claim_by_owner"])
                      ? _.get(cleaning, ["owner", "name"]) || "N/A"
                      : _.get(cleaning, ["claim_by_tenant"])
                      ? _.get(cleaning, ["tenant", "name"]) || "N/A"
                      : "N/A"
                  }}
                </td>
                <td class="text-truncate">
                  {{ _.get(cleaning, ["cleaning_date"]) ? moment(cleaning.cleaning_date).format('YYYY-MM-DD HH:mm') : 'N/A' || "N/A" }}
                </td>
              </tr>
            </table>
          </div>
        </div>
      </div>
    </div>
  </span>
</template>