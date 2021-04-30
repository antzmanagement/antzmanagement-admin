
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
          style: styles,
          scanStyles: false,
          font_size : '7pt',
          documentTitle : `Add On Payment Invoice ${this.data.sequence}`,
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
          <td colspan="2">
            <table>
              <tr>
                <td class="title">
                  <img
                    src="https://res.cloudinary.com/dwslzbgaa/image/upload/v1612187469/90207330_3196874863870897_574629034651025408_n_h5scz9.jpg"
                    style="width: 100px; height: 100px"
                  />
                </td>

                <td>
                  Official Receipt No #: {{ _.get(data, ["sequence"]) || "N/A" }}<br />
                  Reference No #: {{ _.get(data, ["referenceno"]) || "N/A" }}<br />
                  Room Unit No #: {{ _.get(roomcontract, ['room', 'unit']) || "N/A" }}<br />
                  Created Date:
                  {{ _.get(data, ["paymentdate"]) || "N/A" | formatDate }}<br />
                </td>
              </tr>
            </table>
          </td>
        </tr>

        <tr class="information">
          <td colspan="2">
            <table>
              <tr>
                <td>
                  47G Jalan Kampar Barat 1,<br />
                  Taman Kampar Barat 1,<br />
                  31900 Kampar, Perak, Malaysia
                </td>

                <td>
                  010-289 8012<br />
                  antz.customerservice@gmail.com<br />
                </td>
              </tr>
            </table>
          </td>
        </tr>

        <tr class="heading">
          <td>Item</td>

          <td>Price</td>
        </tr>

        <tr
          class="item"
          v-for="service in data.services"
          :key="service.uid"
        >
          <td>{{_.get(service , ['text']) || 'N/A'}}</td>

          <td>RM {{_.get(service , ['pivot', 'price']) || 'N/A' | toDouble}}</td>
        </tr>
        <tr
          class="item"
          v-for="otherpayment in data.otherpayments"
          :key="otherpayment.uid"
        >
          <td>{{_.get(otherpayment , ['name']) || 'N/A'}}</td>

          <td>RM {{_.get(otherpayment , ['pivot', 'price']) || 'N/A' | toDouble}}</td>
        </tr>

        <tr class="total">
          <td></td>

          <td>Total: RM {{_.get(data , ['totalpayment']) || 'N/A' | toDouble}}</td>
        </tr>
      </table>
    </div>
  </span>
</template>