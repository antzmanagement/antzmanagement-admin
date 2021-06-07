
<script>
import { mapActions } from "vuex";
import { _ } from "../../../common/common-function";
import print from "print-js";

const styles = `.invoice-box {
    max-width: 800px;
    margin: auto;
    padding: 30px;
    border: 1px solid #eee;
    box-shadow: 0 0 10px rgba(0, 0, 0, .15);
    font-size: 10px;
    line-height: 20px;
    font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
    color: #555;
}

.subtitle td{
    font-size: 10px;
}
.invoice-box table {
    width: 100%;
    line-height: inherit;
    text-align: left;
}

.invoice-box table td {
    padding: 5px;
    vertical-align: top;
}

.invoice-box table tr td:nth-child(2) {
    text-align: right;
}

.invoice-box table tr.top table td {
    padding-bottom: 20px;
}

.invoice-box table tr.top table td.title {
    font-size: 45px;
    line-height: 30px;
    color: #333;
}

.invoice-box table tr.information table td {
    padding-bottom: 20px;
}

.invoice-box table tr.heading td {
    background: #eee;
    border-bottom: 1px solid #ddd;
    font-weight: bold;
}

.invoice-box table tr.details td {
    padding-bottom: 20px;
}

.invoice-box table tr.item td {
    border-bottom: 1px solid #eee;
}

.invoice-box table tr.item.last td {
    border-bottom: none;
}

.invoice-box table tr.total td:nth-child(2) {
    border-top: 2px solid #eee;
    font-weight: bold;
}
.font-weight-bold{
    font-weight: bold;
}
.small-text {
    font-size: 0.50rem;
    letter-spacing: 0.01em;
}
.overline {
    font-size: 0.75rem;
    letter-spacing: 0.1666666667em;
}

.caption {
    font-size: 0.75rem;
    letter-spacing: 0.025rem;
}
.subtitle1 {
    font-size: 1rem;
    letter-spacing: 0.009375rem;
}

.text-align-right {
    text-align: right;
}

.flex-items-align-center {
    display: flex;
    align-items: center;
}
.flex-justify-end {
    display: flex;
    justify-content: flex-end;
}
.d-inline-block {
    display: inline-block
}
.margin-right-md {
    margin-right: 1em;
}
.margin-top-md {
    margin-top: 1em;
}
@media only screen and (max-width: 600px) {
    .invoice-box table tr.top table td {
        width: 100%;
        display: block;
        text-align: center;
    }
    .invoice-box table tr.information table td {
        width: 100%;
        display: block;
        text-align: center;
    }
}

/** RTL **/

.rtl {
    direction: rtl;
    font-family: Tahoma, 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
}

.rtl table {
    text-align: right;
}

.rtl table tr td:nth-child(2) {
    text-align: left;
}`;
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
          style: styles,
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

        <tr class="item subtitle">
          <td class="text-truncate">
            Rental {{ _.get(data, ["rentaldate"]) || "N/A" | formatDate }}
          </td>
          <td class="text-truncate">
            {{ _.get(data, ["price"]) || "N/A" | toDouble }}
          </td>
        </tr>
        <tr class="item subtitle">
          <td class="text-truncate">Late Penalty</td>
          <td class="text-truncate">
            {{ _.get(data, ["penalty"]) || "N/A" | toDouble }}
          </td>
        </tr>
        <tr class="item subtitle">
          <td class="text-truncate">Processing Fee</td>
          <td class="text-truncate">
            {{ _.get(data, ["processing_fees"]) || "N/A" | toDouble }}
          </td>
        </tr>

        <tr class="total subtitle">
          <td class="text-truncate"></td>

          <td class="text-truncate">
            Total: {{ totalPayment || "N/A" | toDouble }}
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
          </td>
        </tr>
      </table>
    </div>
  </span>
</template>