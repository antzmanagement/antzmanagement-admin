import Vue from "vue";
import PassportServices from "../common/LaravelPassportServices";

const ReportServices = {

  index(data) {
    PassportServices.AuthenticationServices.setHeader();

    const clonedata = Object.assign({}, data);
    
    return Vue.axios.get('/api/report', {
      params: clonedata
    })
  },

  filter(data) {
    PassportServices.AuthenticationServices.setHeader();

    const clonedata = Object.assign({}, data);
    
    return Vue.axios.get('/api/filter/report', {
      params: clonedata
    })
  },

  show(data) {
    PassportServices.AuthenticationServices.setHeader();
    return Vue.axios.get('/api/report/' + data.uid);
  },

  create(data) {

    //Init axios with oauth headers
    PassportServices.AuthenticationServices.setHeader();
    //The data is pass by reference, any modified data will reflected to front end view.
    //In practice way, we must clone it to prevent any error that caused by manipulation stage.
    const clonedata = Object.assign({}, data);
    
    return Vue.axios.post('/api/report', clonedata)
  },

  update(data) {
    PassportServices.AuthenticationServices.setHeader();
    const clonedata = Object.assign({}, data);
    
    return Vue.axios.put('/api/report/' + clonedata.uid, clonedata);
  },


  delete(data) {
    PassportServices.AuthenticationServices.setHeader();
    const clonedata = Object.assign({}, data);
    
    return Vue.axios.delete('/api/report/' + data.uid);
  },
};

export default ReportServices;