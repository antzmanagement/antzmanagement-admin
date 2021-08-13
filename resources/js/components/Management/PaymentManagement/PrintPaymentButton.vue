
<script>
import { mapActions } from "vuex";
import { printCss, _ } from "../../../common/common-function";
import print from "print-js";

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
    data: {
      otherpayments : [],
      services : [],
      issueby : {},
      sequence : '',
      paymentdate : '',
      receive_from : '',
      paymentmethod : '',
      referenceno : '',
      remark : '',
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
          printable: `printPayment${this.uid}`,
          type: "html",
          style: printCss,
          scanStyles: false,
          font_size: "7pt",
          documentTitle: `Payment Invoice ${this.data.sequence}`,
          onPrintDialogClose: () => console.log("The print dialog was closed"),
          onError: (e) => console.log(e),
        });
        this.endLoadingAction();
        // console.log(`printPayment${this.uid}`);
        // this.$htmlToPaper(`printPayment${this.uid}`);
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
    <div class="invoice-box d-none" :id="`printPayment${uid}`">
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
                  <div class="font-weight-bold subtitle1">Official Receipt</div>
                  <div class="flex-items-align-center flex-justify-end">
                    <span
                      class="d-inline-block text-align-right margin-right-md"
                    >
                      Receipt No #:
                    </span>
                    <span class="d-inline-block">
                      {{ _.get(data, ["sequence"]) || "N/A" }}
                    </span>
                  </div>
                  <div class="flex-items-align-center flex-justify-end">
                    <span
                      class="d-inline-block text-align-right margin-right-md"
                    >
                      Date:
                    </span>
                    <span class="d-inline-block">
                      {{ _.get(data, ["paymentdate"]) || "N/A" | formatDate }}
                    </span>
                  </div>
                </td>
              </tr>
              <tr>
                <td colspan="2">
                  <div class="font-weight-bold">ANTZ MANAGEMENT SDN. BHD.</div>
                  <div class="small-text">
                    Reg. No : 202101013433 (1413732-W)
                  </div>
                  <div class="small-text">
                    47G, Jalan Kampar Barat 1, Taman Kampar Barat 1, 31900
                    Kampar, Perak.
                  </div>
                  <div class="small-text">010-2898012</div>
                  <div class="small-text">antz.customerservice@gmail.com</div>
                  <div class="margin-top-md small-text">
                    Receive From #: {{ _.get(data, ["receive_from"]) || "N/A" }}
                  </div>
                  <div class="small-text">
                    Room Unit No #:
                    {{ _.get(roomcontract, ["room", "unit"]) || "N/A" }}
                  </div>
                </td>
              </tr>
            </table>
          </td>
        </tr>

        <tr class="heading">
          <td class="text-truncate">Payment For:</td>
          <td class="text-truncate">RM :</td>
        </tr>

        <tr class="item" v-for="service in data.services" :key="service.uid">
          <td class="text-truncate subtitle">
            {{ _.get(service, ["text"]) || "N/A" }}
          </td>

          <td class="text-truncate subtitle">
            RM {{ _.get(service, ["pivot", "price"]) || "N/A" | toDouble }}
          </td>
        </tr>
        <tr
          class="item"
          v-for="otherpayment in data.otherpayments"
          :key="otherpayment.uid"
        >
          <td class="text-truncate subtitle">
            {{ _.get(otherpayment, ["name"]) || "N/A" }}
          </td>

          <td class="text-truncate subtitle">
            RM {{ _.get(otherpayment, ["pivot", "price"]) || _.get(otherpayment, ["price"]) || "N/A" | toDouble }}
          </td>
        </tr>

        <tr class="total">
          <td class="text-truncate subtitle"></td>

          <td class="text-truncate subtitle">
            Total: RM {{ _.get(data, ["totalpayment"]) || "N/A" | toDouble }}
          </td>
        </tr>
        <tr class="information">
          <td class="text-truncate">
            <div class="small-text">
              Payment By : {{ _.get(data, ["paymentmethod"]) || "N/A" }}
            </div>
            <div class="small-text">
              Ref. No : {{ _.get(data, ["referenceno"]) || "N/A" }}
            </div>
            <div class="small-text">
              Issue By : {{ _.get(data, ["issueby", "name"]) || "N/A" }}
            </div>
            <div class="small-text" v-if="_.get(data , ['remark'])">
              Remark : {{ _.get(data, ["remark"]) || "N/A" }}
            </div>
          </td>
        </tr>
      </table>
    </div>
  </span>
</template>