
import * as VueGoogleMaps from "vue2-google-maps";
import { commonConfig } from "./common/config";

Vue.use(VueGoogleMaps, {
    load: {
      key: commonConfig.google_maps_api_key,
      libraries: "places" // necessary for places input
    }
  });