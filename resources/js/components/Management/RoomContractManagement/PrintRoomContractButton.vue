
<script>
import { mapActions } from "vuex";
import print from "print-js";
import printCss from "../../../common/printCssStr";

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
    tenant: {},
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
      this.tenant = _.get(this.data, `tenant`) || {};
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
        <v-icon small color="success">mdi-printer</v-icon>
      </slot>
    </span>
    <div class="d-none" :id="`printTenant${uid}`">
      <div class="invoice-box">
        <div class="container">
          <div class="row">
            <div class="col-12 h6 font-weight-bold flex-items-align-center">
              <img
                src="https://res.cloudinary.com/dwslzbgaa/image/upload/v1623056803/Screenshot_2021-06-07_at_5.05.50_PM_xooqkt.png"
                style="width: 50px; height: 50px"
                class="margin-right-md"
              />
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
                      {{ tenant.name }}
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
                      {{ tenant.icno }}
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
                      {{ tenant.age }}
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
                      {{ tenant.birthday | formatDate }}
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
                      {{ tenant.gender }}
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
                      {{ tenant.tel1 }}
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
                      {{ tenant.tel2 }}
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
                      {{ tenant.tel3 }}
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
                      {{ tenant.email }}
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
                      {{ tenant.religion }}
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
                      {{ tenant.occupation }}
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
                      {{ tenant.address }}
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
                      {{ tenant.postcode }}
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
                      {{ tenant.city }}
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
                      {{ tenant.occupation }}
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
                      {{ tenant.state }}
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
                      {{ tenant.mother_name }}
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
                      {{ tenant.mother_tel }}
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
                      {{ tenant.father_name }}
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
                      {{ tenant.father_tel }}
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
                      {{ tenant.emergency_name }}
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
                      {{ tenant.emergency_contact }}
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
                      {{ tenant.emergency_relationship }}
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
                      {{ tenant.approach_method }}
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
                      {{ _.get(tenant, ["creator", "name"]) || "N/A" }}
                    </div>
                  </div>
                </td>
              </tr>
            </table>
          </div>
        </div>
      </div>

      <div class="row page-break"></div>

      <div class="invoice-box">
        <div class="container">
          <div class="row">
            <div class="col-12 h6 font-weight-bold flex-items-align-center">
              <img
                src="https://res.cloudinary.com/dwslzbgaa/image/upload/v1623056803/Screenshot_2021-06-07_at_5.05.50_PM_xooqkt.png"
                style="width: 50px; height: 50px"
                class="margin-right-md"
              />
              Contract Information
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
                    <div :class="formLabelClass">Room</div>
                    <div :class="formContentClass">
                      {{ _.get(data, "room.unit") }}
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
                    <div :class="formLabelClass">Type</div>
                    <div :class="formContentClass">
                      {{ _.get(data, "room.room_types[0].name") }}
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
                    <div :class="formLabelClass">Start Date</div>
                    <div :class="formContentClass">
                      {{ _.get(data, "startdate") }}
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
                    <div :class="formLabelClass">End Date</div>
                    <div :class="formContentClass">
                      {{ _.get(data, "enddate") }}
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
                    <div :class="formLabelClass">Duration</div>
                    <div :class="formContentClass">
                      {{ _.get(data, "duration") }}
                      {{ _.get(data, "rental_type") }}
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
                    <div :class="formLabelClass">Rental</div>
                    <div :class="formContentClass">
                      {{ _.get(data, "rental") }}
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
                    <div :class="formLabelClass">Deposit</div>
                    <div :class="formContentClass">
                      {{ _.get(data, "deposit") }}
                    </div>
                  </div>
                </td>
              </tr>
            </table>
          </div>

          <div
            class="row"
            v-if="
              (_.isArray(_.get(data, 'origservices')) &&
                !_.isEmpty(_.get(data, 'origservices'))) ||
              (_.isArray(_.get(data, 'addonservices')) &&
                !_.isEmpty(_.get(data, 'addonservices')))
            "
          >
            <div class="divider my-2"></div>
          </div>

          <div
            class="row"
            v-if="
              (_.isArray(_.get(data, 'origservices')) &&
                !_.isEmpty(_.get(data, 'origservices'))) ||
              (_.isArray(_.get(data, 'addonservices')) &&
                !_.isEmpty(_.get(data, 'addonservices')))
            "
          >
            <div class="col-12 h6 font-weight-bold">Services</div>
          </div>

          <div
            class="row"
            v-if="
              (_.isArray(_.get(data, 'origservices')) &&
                !_.isEmpty(_.get(data, 'origservices'))) ||
              (_.isArray(_.get(data, 'addonservices')) &&
                !_.isEmpty(_.get(data, 'addonservices')))
            "
          >
            <div class="divider my-2"></div>
          </div>

          <div
            class="row margin-bottom-lg padding-x-md"
            v-if="
              (_.isArray(_.get(data, 'origservices')) &&
                !_.isEmpty(_.get(data, 'origservices'))) ||
              (_.isArray(_.get(data, 'addonservices')) &&
                !_.isEmpty(_.get(data, 'addonservices')))
            "
          >
            <table>
              <tr
                class="item invoice padding-x-md"
                v-for="service in _.get(data, 'origservices')"
                :key="service.uid"
              >
                <td class="text-truncate subtitle">
                  {{ _.get(service, ["text"]) || "N/A" }}
                </td>

                <td class="text-truncate subtitle green">Package Included</td>
              </tr>
              <tr
                class="item invoice padding-x-md"
                v-for="service in _.get(data, 'addonservices')"
                :key="service.uid"
              >
                <td class="text-truncate subtitle">
                  {{ _.get(service, ["text"]) || "N/A" }}
                </td>

                <td class="text-truncate subtitle">
                  RM
                  {{ _.get(service, ["price"]) || "N/A" | toDouble }} / month
                </td>
              </tr>
            </table>
          </div>

          <div
            class="row"
            v-if="
              !(
                (_.isArray(_.get(data, 'origservices')) &&
                  !_.isEmpty(_.get(data, 'origservices'))) ||
                (_.isArray(_.get(data, 'addonservices')) &&
                  !_.isEmpty(_.get(data, 'addonservices')))
              )
            "
          >
            <div class="divider my-2"></div>
          </div>

          <div class="row">
            <div class="col-12 h6 font-weight-bold">Payment Information</div>
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
                    <div :class="formLabelClass">Partial Payment(Deposit)</div>
                    <div :class="formContentClass">
                      {{
                        _.get(
                          _.find(_.get(data, `payments`), (payment) => {
                            return _.some(_.get(payment, "otherpayments"), [
                              "name",
                              "Partial Payment(Deposit)",
                            ]);
                          }),
                          "other_charges"
                        )
                      }}
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
                    <div :class="formLabelClass">Receipt No</div>
                    <div :class="formContentClass">
                      {{
                        _.get(
                          _.find(_.get(data, `payments`), (payment) => {
                            return _.some(_.get(payment, "otherpayments"), [
                              "name",
                              "Partial Payment(Deposit)",
                            ]);
                          }),
                          "receiptno"
                        )
                      }}
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
                    <div :class="formLabelClass">Agreement Fees</div>
                    <div :class="formContentClass">
                      {{ _.get(data, "agreement_fees") }}
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
                    <div :class="formLabelClass">Receipt No</div>
                    <div :class="formContentClass">
                      {{
                        _.get(
                          _.find(_.get(data, `payments`), (payment) => {
                            return _.some(_.get(payment, "otherpayments"), [
                              "name",
                              "Agreement Fees",
                            ]);
                          }),
                          "receiptno"
                        )
                      }}
                    </div>
                  </div>
                </td>
              </tr>
              <tr>
                <td colspan="2">
                  <div class="text-overflow-break margin-top-md">
                    I hereby declare that all information furnished by me are
                    true
                  </div>
                  <div class="text-overflow-break">
                    I agree the booking fee is non refundable, it only can use
                    to be the partial payment of the security deposit.
                  </div>
                </td>
              </tr>
              <tr>
                <td colspan="2">
                  <div
                    class="width-50"
                    style="
                      height: 70px;
                      border-bottom: solid 1px;
                      border-bottom-color: #eeeeee;
                    "
                  ></div>
                  <div>Signature</div>
                  <div>Name :</div>
                  <div>Date :</div>
                </td>
              </tr>
            </table>
          </div>
        </div>
      </div>
    </div>
  </span>
</template>