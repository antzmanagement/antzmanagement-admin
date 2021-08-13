
<script>
import { mapActions } from "vuex";
import print from "print-js";
import printCss from "../../../../common/printCssStr";

export default {
  props: {
    item: {
      type: Object,
      default: {},
    },
  },
  data: () => ({
    _: _,
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
          printable: `printOwner${this.uid}`,
          type: "html",
          style: printCss,
          scanStyles: false,
          font_size: "7pt",
          documentTitle: `Payment Invoice ${this.data.sequence}`,
          onPrintDialogClose: () => console.log("The print dialog was closed"),
          onError: (e) => console.log(e),
        });
        this.endLoadingAction();
        // console.log(`printOwner${this.uid}`);
        // this.$htmlToPaper(`printOwner${this.uid}`);
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
    <div class="d-none invoice-box" :id="`printOwner${uid}`">
      <div class="container">
        <div class="row">
          <div class="col-12 h6 font-weight-bold">
            Owner - {{ _.get(data, ["uid"]) }}
          </div>
        </div>
        <div class="row">
          <div class="divider my-2"></div>
        </div>
        <div class="row">
          <div class="col-12 mb-2">
            <div class="form-label">Room</div>
            <span
              v-for="room in data.ownrooms"
              :key="room.uid"
              class="mr-2 my-2 d-inline-block"
            >
              <div class="text-align-center ma-2 form-content round-border thin-border px-3">
                {{ room.unit || 'N/A' }}
              </div>
            </span>
          </div>
        </div>
        <div class="row">
          <div class="divider my-2"></div>
        </div>
        <div class="row">
          <div class="col-2 mb-2">
            <div class="form-label">Name</div>
            <div class="form-content">
              {{ data.name }}
            </div>
          </div>
          <div class="col-2 mb-2">
            <div class="form-label">IC-NO</div>
            <div class="form-content">
              {{ data.icno }}
            </div>
          </div>
          <div class="col-2 mb-2">
            <div class="form-label">Bank</div>
            <div class="form-content">
              {{ data.banktype }}
            </div>
          </div>
          <div class="col-2 mb-2">
            <div class="form-label">Bank</div>
            <div class="form-content">
              {{ data.banktype }}
            </div>
          </div>
          <div class="col-2 mb-2">
            <div class="form-label">Other Bank</div>
            <div class="form-content">
              {{ data.otherbanktype }}
            </div>
          </div>
          <div class="col-2 mb-2">
            <div class="form-label">Bank Account</div>
            <div class="form-content">
              {{ data.bankaccount }}
            </div>
          </div>
          <div class="col-2 mb-2">
            <div class="form-label">Bank Account Name</div>
            <div class="form-content">
              {{ data.bankaccountname }}
            </div>
          </div>
          <div class="col-2 mb-2">
            <div class="form-label">Phone 1</div>
            <div class="form-content">
              {{ data.tel1 }}
            </div>
          </div>
          <div class="col-2 mb-2">
            <div class="form-label">Phone 2</div>
            <div class="form-content">
              {{ data.tel2 }}
            </div>
          </div>
          <div class="col-2 mb-2">
            <div class="form-label">Phone 3</div>
            <div class="form-content">
              {{ data.tel3 }}
            </div>
          </div>
          <div class="col-2 mb-2">
            <div class="form-label">Email</div>
            <div class="form-content">
              {{ data.email }}
            </div>
          </div>
          <div class="col-2 mb-2">
            <div class="form-label">Occupation</div>
            <div class="form-content">
              {{ data.occupation }}
            </div>
          </div>
          <div class="col-2 mb-2">
            <div class="form-label">Address</div>
            <div class="form-content">
              {{ data.address }}
            </div>
          </div>
          <div class="col-2 mb-2">
            <div class="form-label">Postcode</div>
            <div class="form-content">
              {{ data.postcode }}
            </div>
          </div>
          <div class="col-2 mb-2">
            <div class="form-label">City</div>
            <div class="form-content">
              {{ data.city }}
            </div>
          </div>
          <div class="col-2 mb-2">
            <div class="form-label">State</div>
            <div class="form-content">
              {{ data.state }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </span>
</template>