
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
    formLabelClass: "form-label margin-right-md width-30",
    formContentClass:
      "form-content height-100 width-50 thin-border padding-y-sm round-border padding-x-md",
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
            Tenant Information
          </div>
        </div>
        <div class="row">
          <div class="divider my-2"></div>
        </div>
        <div class="row">
          <table>
            <tr>
              <td>
                <div
                  class="
                    flex-justify-start flex-items-align-center
                    padding-left-md
                  "
                >
                  <div :class="formLabelClass">Name</div>
                  <div :class="formContentClass">
                    {{ data.name }}
                  </div>
                </div>
              </td>
              <td>
                <div
                  class="
                    flex-justify-start flex-items-align-center
                    padding-left-md
                  "
                >
                  <div :class="formLabelClass">IC-NO</div>
                  <div :class="formContentClass">
                    {{ data.icno }}
                  </div>
                </div>
              </td>
            </tr>
            <tr>
              <td>
                <div
                  class="
                    flex-justify-start flex-items-align-center
                    padding-left-md
                  "
                >
                  <div :class="formLabelClass">Age</div>
                  <div :class="formContentClass">
                    {{ data.age }}
                  </div>
                </div>
              </td>
              <td>
                <div
                  class="
                    flex-justify-start flex-items-align-center
                    padding-left-md
                  "
                >
                  <div :class="formLabelClass">Birthday</div>
                  <div :class="formContentClass">
                    {{ data.birthday | formatDate }}
                  </div>
                </div>
              </td>
            </tr>
            <tr>
              <td>
                <div
                  class="
                    flex-justify-start flex-items-align-center
                    padding-left-md
                  "
                >
                  <div :class="formLabelClass">Gender</div>
                  <div :class="formContentClass">
                    {{ data.gender }}
                  </div>
                </div>
              </td>
              <td>
                <div
                  class="
                    flex-justify-start flex-items-align-center
                    padding-left-md
                  "
                >
                  <div :class="formLabelClass">Phone 1</div>
                  <div :class="formContentClass">
                    {{ data.tel1 }}
                  </div>
                </div>
              </td>
            </tr>
            <tr>
              <td>
                <div
                  class="
                    flex-justify-start flex-items-align-center
                    padding-left-md
                  "
                >
                  <div :class="formLabelClass">Phone 2</div>
                  <div :class="formContentClass">
                    {{ data.tel2 }}
                  </div>
                </div>
              </td>
              <td>
                <div
                  class="
                    flex-justify-start flex-items-align-center
                    padding-left-md
                  "
                >
                  <div :class="formLabelClass">Phone 3</div>
                  <div :class="formContentClass">
                    {{ data.tel3 }}
                  </div>
                </div>
              </td>
            </tr>
            <tr>
              <td>
                <div
                  class="
                    flex-justify-start flex-items-align-center
                    padding-left-md
                  "
                >
                  <div :class="formLabelClass">Email</div>
                  <div :class="formContentClass">
                    {{ data.email }}
                  </div>
                </div>
              </td>
              <td>
                <div
                  class="
                    flex-justify-start flex-items-align-center
                    padding-left-md
                  "
                >
                  <div :class="formLabelClass">Religion</div>
                  <div :class="formContentClass">
                    {{ data.religion }}
                  </div>
                </div>
              </td>
            </tr>
            <tr>
              <td>
                <div
                  class="
                    flex-justify-start flex-items-align-center
                    padding-left-md
                  "
                >
                  <div :class="formLabelClass">Occupation</div>
                  <div :class="formContentClass">
                    {{ data.occupation }}
                  </div>
                </div>
              </td>
              <td>
                <div
                  class="
                    flex-justify-start flex-items-align-center
                    padding-left-md
                  "
                >
                  <div :class="formLabelClass">Address</div>
                  <div :class="formContentClass">
                    {{ data.address }}
                  </div>
                </div>
              </td>
            </tr>
            <tr>
              <td>
                <div
                  class="
                    flex-justify-start flex-items-align-center
                    padding-left-md
                  "
                >
                  <div :class="formLabelClass">Postcode</div>
                  <div :class="formContentClass">
                    {{ data.postcode }}
                  </div>
                </div>
              </td>
              <td>
                <div
                  class="
                    flex-justify-start flex-items-align-center
                    padding-left-md
                  "
                >
                  <div :class="formLabelClass">City</div>
                  <div :class="formContentClass">
                    {{ data.city }}
                  </div>
                </div>
              </td>
            </tr>
            <tr>
              <td>
                <div
                  class="
                    flex-justify-start flex-items-align-center
                    padding-left-md
                  "
                >
                  <div :class="formLabelClass">Occupation</div>
                  <div :class="formContentClass">
                    {{ data.occupation }}
                  </div>
                </div>
              </td>
              <td>
                <div
                  class="
                    flex-justify-start flex-items-align-center
                    padding-left-md
                  "
                >
                  <div :class="formLabelClass">State</div>
                  <div :class="formContentClass">
                    {{ data.state }}
                  </div>
                </div>
              </td>
            </tr>
            <tr>
              <td>
                <div
                  class="
                    flex-justify-start flex-items-align-center
                    padding-left-md
                  "
                >
                  <div :class="formLabelClass">Mother Name</div>
                  <div :class="formContentClass">
                    {{ data.mother_name }}
                  </div>
                </div>
              </td>
              <td>
                <div
                  class="
                    flex-justify-start flex-items-align-center
                    padding-left-md
                  "
                >
                  <div :class="formLabelClass">Mother Contact</div>
                  <div :class="formContentClass">
                    {{ data.mother_tel }}
                  </div>
                </div>
              </td>
            </tr>
            <tr>
              <td>
                <div
                  class="
                    flex-justify-start flex-items-align-center
                    padding-left-md
                  "
                >
                  <div :class="formLabelClass">Father Name</div>
                  <div :class="formContentClass">
                    {{ data.father_name }}
                  </div>
                </div>
              </td>
              <td>
                <div
                  class="
                    flex-justify-start flex-items-align-center
                    padding-left-md
                  "
                >
                  <div :class="formLabelClass">Father Contact</div>
                  <div :class="formContentClass">
                    {{ data.father_tel }}
                  </div>
                </div>
              </td>
            </tr>
            <tr>
              <td>
                <div
                  class="
                    flex-justify-start flex-items-align-center
                    padding-left-md
                  "
                >
                  <div :class="formLabelClass">Emergency Contact Person</div>
                  <div :class="formContentClass">
                    {{ data.emergency_name }}
                  </div>
                </div>
              </td>
              <td>
                <div
                  class="
                    flex-justify-start flex-items-align-center
                    padding-left-md
                  "
                >
                  <div :class="formLabelClass">Emergency Contact</div>
                  <div :class="formContentClass">
                    {{ data.emergency_contact }}
                  </div>
                </div>
              </td>
            </tr>
            <tr>
              <td>
                <div
                  class="
                    flex-justify-start flex-items-align-center
                    padding-left-md
                  "
                >
                  <div :class="formLabelClass">Emergency Relationship</div>
                  <div :class="formContentClass">
                    {{ data.emergency_relationship }}
                  </div>
                </div>
              </td>
              <td>
                <div
                  class="
                    flex-justify-start flex-items-align-center
                    padding-left-md
                  "
                >
                  <div :class="formLabelClass">How you know Us?</div>
                  <div :class="formContentClass">
                    {{ data.approach_method }}
                  </div>
                </div>
              </td>
            </tr>
            <tr>
              <td>
                <div
                  class="
                    flex-justify-start flex-items-align-center
                    padding-left-md
                  "
                >
                  <div :class="formLabelClass">Person In Charge</div>
                  <div :class="formContentClass">
                    {{ _.get(data, ["creator", "name"]) || "N/A" }}
                  </div>
                </div>
              </td>
            </tr>   
            <tr>
              <td>
                  <div
                  class="width-50"
                    style="
                      height: 70px;
                      border-bottom: solid 1px;
                      border-bottom-color: #EEEEEE;
                    "
                  ></div>
                  <div>
                    Tenant Name : 
                  </div>
                  <div>
                    Date : 
                  </div>
              </td>
              <td>
                  <div
                  class="width-50"
                    style="
                      height: 70px;
                      border-bottom: solid 1px;
                      border-bottom-color: #EEEEEE;
                    "
                  ></div>
                  <div>
                    Person In Charge Name : 
                  </div>
                  <div>
                    Date : 
                  </div>
              </td>
            </tr>
          </table>
        </div>
      </div>
    </div>
  </span>
</template>