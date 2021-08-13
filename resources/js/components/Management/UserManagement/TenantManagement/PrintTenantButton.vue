
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
          printable: `printTenant${this.uid}`,
          type: "html",
          style: printCss,
          scanStyles: false,
          font_size: "7pt",
          documentTitle: `Payment Invoice ${this.data.sequence}`,
          onPrintDialogClose: () => console.log("The print dialog was closed"),
          onError: (e) => console.log(e),
        });
        this.endLoadingAction();
        // console.log(`printTenant${this.uid}`);
        // this.$htmlToPaper(`printTenant${this.uid}`);
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
    <div class="d-none invoice-box" :id="`printTenant${uid}`">
      <div class="container">
        <div class="row">
          <div class="col-12 h6 font-weight-bold">
            Tenant - {{ _.get(data, ["uid"]) }}
          </div>
        </div>
        <div class="row">
          <div class="divider my-2"></div>
        </div>
        <div class="row">
          <div class="col-12 mb-2">
            <div class="form-label">Room Contracts</div>
            <span
              v-for="roomcontract in data.roomcontracts"
              :key="roomcontract.uid"
              class="mr-2 my-2 d-inline-block"
            >
              <div class="text-align-center ma-2 form-content round-border thin-border px-3">
                {{ `${_.get(roomcontract , ['room.unit']) || ''} ${_.get(roomcontract , ['name']) || ''}` || 'N/A' }}
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
            <div class="form-label">Age</div>
            <div class="form-content">
              {{ data.age }}
            </div>
          </div>
          <div class="col-2 mb-2">
            <div class="form-label">Birthday</div>
            <div class="form-content">
              {{ data.birthday | formatDate }}
            </div>
          </div>
          <div class="col-2 mb-2">
            <div class="form-label">Gender</div>
            <div class="form-content">
              {{ data.gender }}
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
            <div class="form-label">Religion</div>
            <div class="form-content">
              {{ data.religion }}
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
          <div class="col-2 mb-2">
            <div class="form-label">Mother Name</div>
            <div class="form-content">
              {{ data.mother_name }}
            </div>
          </div>
          <div class="col-2 mb-2">
            <div class="form-label">Mother Contact</div>
            <div class="form-content">
              {{ data.mother_tel }}
            </div>
          </div>
          <div class="col-2 mb-2">
            <div class="form-label">Father Name</div>
            <div class="form-content">
              {{ data.father_name }}
            </div>
          </div>
          <div class="col-2 mb-2">
            <div class="form-label">Father Contact</div>
            <div class="form-content">
              {{ data.father_tel }}
            </div>
          </div>
          <div class="col-2 mb-2">
            <div class="form-label">Emergency Contact Person</div>
            <div class="form-content">
              {{ data.emergency_name }}
            </div>
          </div>
          <div class="col-2 mb-2">
            <div class="form-label">Emergency Contact</div>
            <div class="form-content">
              {{ data.emergency_contact }}
            </div>
          </div>
          <div class="col-2 mb-2">
            <div class="form-label">Emergency Relationship</div>
            <div class="form-content">
              {{ data.emergency_relationship }}
            </div>
          </div>
          <div class="col-2 mb-2">
            <div class="form-label">How you know Us?</div>
            <div class="form-content">
              {{ data.approach_method }}
            </div>
          </div>
          <div class="col-2 mb-2">
            <div class="form-label">Person In Charge</div>
            <div class="form-content">
              {{ _.get(data, ["creator", "name"]) || "N/A" }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </span>
</template>