import Vue from "vue";
import PassportServices from "../common/LaravelPassportServices";

const OwnerServices = {

  index(data) {
    PassportServices.AuthenticationServices.setHeader();

    const clonedata = Object.assign({}, data);
    
    return Vue.axios.get('/api/owner', {
      params: clonedata
    })
  },

  filter(data) {
    PassportServices.AuthenticationServices.setHeader();

    const clonedata = Object.assign({}, data);
    
    return Vue.axios.get('/api/filter/owner', {
      params: clonedata
    })
  },

  show(data) {
    PassportServices.AuthenticationServices.setHeader();
    return Vue.axios.get('/api/owner/' + data.uid);
  },

  create(data) {

    //Init axios with oauth headers
    PassportServices.AuthenticationServices.setHeader();
    //The data is pass by reference, any modified data will reflected to front end view.
    //In practice way, we must clone it to prevent any error that caused by manipulation stage.
    const clonedata = Object.assign({}, data);
    
    return Vue.axios.post('/api/owner', clonedata)
  },

  update(data) {
    PassportServices.AuthenticationServices.setHeader();
    const clonedata = Object.assign({}, data);
    
    return Vue.axios.put('/api/owner/' + clonedata.uid, clonedata);
  },


  delete(data) {
    PassportServices.AuthenticationServices.setHeader();
    const clonedata = Object.assign({}, data);
    
    return Vue.axios.delete('/api/owner/' + data.uid);
  },

  getUnclaimRentalPayments(data) {
    PassportServices.AuthenticationServices.setHeader();
    
    return Vue.axios.get('/api/owner/'+ data.uid + '/getUnclaimRentalPayments');
  },
  getUnclaimMaintenances(data) {
    PassportServices.AuthenticationServices.setHeader();
    console.log(data);
    return Vue.axios.get('/api/owner/'+ data.uid + '/getUnclaimMaintenances');
  }
};

export default OwnerServices;