import Vue from "vue";
import Vuex from "vuex";

import auth from "./auth.module";
import loading from "./loading,module";
import maintenance from "./maintenance.module";
import contract from "./contract.module";
import tenant from "./tenant.module";
import owner from "./owner.module";
import property from "./property.module";
import rentalpayment from "./rentalpayment.module";
import staff from "./staff.module";
import user from "./user.module";
import report from "./report.module";
import role from "./role.module";
import room from "./room.module";
import roomContract from "./roomContract.module";
import roomType from "./roomType.module";
import service from "./service.module";
import claim from "./claim.module";
import payment from "./payment.module";

Vue.use(Vuex);

export default new Vuex.Store({
  modules: {
    tenant,
    owner,
    staff,
    room, 
    role,
    report,
    roomContract,
    roomType,
    maintenance,
    contract,
    property,
    service,
    rentalpayment,
    claim,
    user,
    auth,
    loading,
    payment,
  }
});