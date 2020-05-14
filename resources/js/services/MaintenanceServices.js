import Vue from "vue";
import PassportServices from "../common/LaravelPassportServices";

const MaintenanceServices = {

  index(data) {
    PassportServices.AuthenticationServices.setHeader();

    const clonedata = Object.assign({}, data);
    console.log(clonedata);
    return Vue.axios.get('/api/maintenance', {
      params: clonedata
    })
  },

  filter(data) {
    PassportServices.AuthenticationServices.setHeader();

    const clonedata = Object.assign({}, data);
    console.log(clonedata);
    return Vue.axios.get('/api/filter/maintenance', {
      params: clonedata
    })
  },

  show(data) {
    PassportServices.AuthenticationServices.setHeader();
    return Vue.axios.get('/api/maintenance/' + data.uid);
  },

  create(data) {

    //Init axios with oauth headers
    PassportServices.AuthenticationServices.setHeader();
    //The data is pass by reference, any modified data will reflected to front end view.
    //In practice way, we must clone it to prevent any error that caused by manipulation stage.
    const clonedata = Object.assign({}, data);
    console.log(clonedata);
    return Vue.axios.post('/api/maintenance', clonedata)
  },

  update(data) {
    PassportServices.AuthenticationServices.setHeader();
    const clonedata = Object.assign({}, data);
    console.log(clonedata);
    return Vue.axios.put('/api/maintenance/' + clonedata.uid, clonedata);
  },


  delete(data) {
    PassportServices.AuthenticationServices.setHeader();
    const clonedata = Object.assign({}, data);
    console.log(clonedata);
    return Vue.axios.delete('/api/maintenance/' + data.uid);
  }
};

export default MaintenanceServices;