import Vue from "vue";
import PassportServices from "../common/LaravelPassportServices";

const CleaningServices = {

  index(data) {
    PassportServices.AuthenticationServices.setHeader();

    const clonedata = Object.assign({}, data);
    
    return Vue.axios.get('/api/cleaning', {
      params: clonedata
    })
  },

  filter(data) {
    PassportServices.AuthenticationServices.setHeader();

    const clonedata = Object.assign({}, data);
    
    return Vue.axios.get('/api/filter/cleaning', {
      params: clonedata
    })
  },

  show(data) {
    PassportServices.AuthenticationServices.setHeader();
    return Vue.axios.get('/api/cleaning/' + data.uid);
  },

  create(data) {

    //Init axios with oauth headers
    PassportServices.AuthenticationServices.setHeader();
    //The data is pass by reference, any modified data will reflected to front end view.
    //In practice way, we must clone it to prevent any error that caused by manipulation stage.
    const clonedata = Object.assign({}, data);
    
    return Vue.axios.post('/api/cleaning', clonedata)
  },

  update(data) {
    PassportServices.AuthenticationServices.setHeader();
    const clonedata = Object.assign({}, data);
    
    return Vue.axios.put('/api/cleaning/' + clonedata.uid, clonedata);
  },


  delete(data) {
    PassportServices.AuthenticationServices.setHeader();
    const clonedata = Object.assign({}, data);
    
    return Vue.axios.delete('/api/cleaning/' + data.uid);
  },

};

export default CleaningServices;