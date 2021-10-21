
<script>
import { mapActions } from "vuex";
import { _ } from "../../../common/common-function";
import print from "print-js";
import printCss from '../../../common/printCssStr';


export default {
  props: {
    item: {
      type: Object,
      default: {},
    },
    roomcontract: {
      type: Object,
      default: {},
    },
  },
  data: () => ({
    _: _,
    data: {},
    uid: new Date().getTime(),
  }),

  computed: {
    isLoading() {
      return this.$store.getters.isLoading;
    },
    totalPayment() {
      let totalPayment =
        (parseFloat(this.data.price) || 0) +
        (parseFloat(this.data.processing_fees) || 0) +
        (parseFloat(this.data.penalty) || 0);
      return totalPayment;
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
      console.log(this.roomcontract);
      console.log(this.data);
      this.data = this.item;
      this.showLoadingAction();
      console.log(this.data);
      setTimeout(() => {
        print({
          printable: `printRentalPayment${this.uid}`,
          type: "html",
          style: printCss,
          scanStyles: false,
          font_size: "7pt",
          documentTitle: `Rental Payment Invoice ${this.data.sequence}`,
          onPrintDialogClose: () => console.log("The print dialog was closed"),
          onError: (e) => console.log(e),
        });
        this.endLoadingAction();
        // console.log(`printRentalPayment${this.uid}`);
        // this.$htmlToPaper(`printRentalPayment${this.uid}`);
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
    <div class="invoice-box d-none" :id="`printRentalPayment${uid}`">
      <table cellpadding="0" cellspacing="0">
        <tr class="top">
          <td class="text-truncate" colspan="2">
            <table>
              <tr>
                <td class="text-truncate title">
                  <img
                    src="https://res.cloudinary.com/dwslzbgaa/image/upload/v1623056803/Screenshot_2021-06-07_at_5.05.50_PM_xooqkt.png"
                    style="width: 70px; height: 70px"
                  />
                </td>
                <td>
                  <div class="font-weight-bold  subtitle">Official Receipt</div>
                  <div class="flex-items-align-center flex-justify-end subtitle">
                    <span
                      class="d-inline-block text-align-right margin-right-md"
                    >
                      Receipt No #:
                    </span>
                    <span class="d-inline-block subtitle">
                      {{ _.get(data, ["sequence"]) || "N/A" }}
                    </span>
                  </div>
                  <div class="flex-items-align-center flex-justify-end">
                    <span
                      class="d-inline-block text-align-right margin-right-md subtitle"
                    >
                      Date:
                    </span>
                    <span class="d-inline-block subtitle">
                      {{ _.get(data, ["paymentdate"]) || "N/A" | formatDate }}
                    </span>
                  </div>
                </td>
              </tr>
              <tr>
                <td colspan="2">
                  <div class="font-weight-bold  subtitle">ANTZ MANAGEMENT SDN. BHD.</div>
                  <div class=" subtitle">
                    Reg. No : 202101013433 (1413732-W)
                  </div>
                  <div class=" subtitle">
                    47G, Jalan Kampar Barat 1, Taman Kampar Barat 1, 31900
                    Kampar, Perak.
                  </div>
                  <div class=" subtitle">010-2898012</div>
                  <div class=" subtitle">antz.customerservice@gmail.com</div>
                  <div class="margin-top-md subtitle">
                    Receive From #: {{ _.get(data, ["receive_from"]) || "N/A" }}
                  </div>
                  <div class=" subtitle">
                    Room Unit No #:
                    {{ _.get(roomcontract, ["room", "unit"]) || "N/A" }}
                  </div>
                </td>
              </tr>
            </table>
          </td>
        </tr>

        <tr class="heading invoice">
          <td class="text-truncate subtitle">Payment For:</td>
          <td class="text-truncate subtitle">RM :</td>
        </tr>

        <tr class="item subtitle invoice">
          <td class="text-truncate">
            Rental {{ _.get(data, ["rentaldate"]) || "N/A" | formatDate }}
          </td>
          <td class="text-truncate">
            {{ _.get(data, ["price"]) || "N/A" | toDouble }}
          </td>
        </tr>
        <tr class="item subtitle invoice">
          <td class="text-truncate">Late Penalty</td>
          <td class="text-truncate">
            {{ _.get(data, ["penalty"]) || "N/A" | toDouble }}
          </td>
        </tr>
        <tr class="item subtitle invoice">
          <td class="text-truncate">Processing Fee</td>
          <td class="text-truncate">
            {{ _.get(data, ["processing_fees"]) || "N/A" | toDouble }}
          </td>
        </tr>

        <tr class="subtitle invoice font-weight-bold">
          <td class="text-truncate"></td>

          <td class="text-truncate">
            Total: {{ totalPayment || "N/A" | toDouble }}
          </td>
        </tr>

        <tr class="subtitle invoice font-weight-bold">
          <td class="text-truncate"></td>
          <td class="text-truncate">
            Payment : {{ _.get(data, ["payment"]) || "N/A" | toDouble }}
          </td>
        </tr>

        <tr class="subtitle invoice font-weight-bold">
          <td class="text-truncate"></td>
          <td class="text-truncate">
            Outstanding : {{ _.get(data, ["outstanding"]) || "N/A" | toDouble }}
          </td>
        </tr>
        <tr>
          <td class="text-truncate">
            <div class=" subtitle">
              Payment By : {{ _.get(data, ["paymentmethod"]) || "N/A" }}
            </div>
            <div class=" subtitle">
              Ref. No : {{ _.get(data, ["referenceno"]) || "N/A" }}
            </div>
            <div class=" subtitle">
              Issue By : {{ _.get(data, ["issueby", "name"]) || "N/A" }}
            </div>
            <div class=" subtitle" v-if="_.get(data , ['remark'])">
              Remark : {{ _.get(data, ["remark"]) || "N/A" }}
            </div>
          </td>
        </tr>
      </table>
    </div>
  </span>
</template>