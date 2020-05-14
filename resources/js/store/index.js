import Vue from "vue";
import Vuex from "vuex";

import auth from "./auth.module";
import loading from "./loading,module";
import maintenance from "./maintenance.module";
import tenant from "./tenant.module";
import owner from "./owner.module";
import property from "./property.module";
import staff from "./staff.module";
import user from "./user.module";
import room from "./room.module";
import roomType from "./roomType.module";

Vue.use(Vuex);

export default new Vuex.Store({
  modules: {
    tenant,
    owner,
    staff,
    room,
    roomType,
    maintenance,
    property,
    user,
    auth,
    loading,
  }
});