import Vue from "vue";
import PassportServices from "../common/LaravelPassportServices";
import { commonConfig } from "../common/config";

const ID_TOKEN_KEY = "access_token";

const AuthenticationServices = {
  init(){

    Vue.axios.defaults.baseURL = commonConfig.API_URL;
  },
  setHeader() {
    Vue.axios.defaults.headers.common.Authorization = `Bearer ${PassportServices.getToken()}`;
  },

  login(data) {
    this.init();
    this.setHeader();
    //The data is pass by reference, any modified data will reflected to front end view.
    //In practice way, we must clone it to prevent any error that caused by manipulation stage.
    const clonedata = Object.assign({}, data);
    clonedata.grant_type = "password";
    clonedata.client_id = commonConfig.client_id;
    clonedata.client_secret = commonConfig.client_secret;
    clonedata.refresh_token = "the-refresh-token";
    clonedata.scope = null;
    clonedata.username = data.email;
    return Vue.axios.post('oauth/token', clonedata)
  },

  register(resource, params) {
    return Vue.axios.put(`${resource}`, params);
  },

  register(resource, params) {
    return Vue.axios.put(`${resource}`, params);
  },

};

export const getToken = () => {
  return window.localStorage.getItem(ID_TOKEN_KEY);
};

export const saveToken = token => {
  window.localStorage.setItem(ID_TOKEN_KEY, token);
};

export const destroyToken = () => {
  window.localStorage.removeItem(ID_TOKEN_KEY);
};

export default { getToken, saveToken, destroyToken, AuthenticationServices };