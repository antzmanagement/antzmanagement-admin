import Vue from "vue";
import PassportServices from "../common/LaravelPassportServices";

const RentalPaymentServices = {

  index(data) {
    PassportServices.AuthenticationServices.setHeader();

    const clonedata = Object.assign({}, data);
    console.log(clonedata);
    return Vue.axios.get('/api/rentalpayment', {
      params: clonedata
    })
  },

  filter(data) {
    PassportServices.AuthenticationServices.setHeader();

    const clonedata = Object.assign({}, data);
    console.log(clonedata);
    return Vue.axios.get('/api/filter/rentalpayment', {
      params: clonedata
    })
  },

  show(data) {
    PassportServices.AuthenticationServices.setHeader();
    return Vue.axios.get('/api/rentalpayment/' + data.uid);
  },

  create(data) {

    //Init axios with oauth headers
    PassportServices.AuthenticationServices.setHeader();
    //The data is pass by reference, any modified data will reflected to front end view.
    //In practice way, we must clone it to prevent any error that caused by manipulation stage.
    const clonedata = Object.assign({}, data);
    console.log(clonedata);
    return Vue.axios.post('/api/rentalpayment', clonedata)
  },

  update(data) {
    PassportServices.AuthenticationServices.setHeader();
    const clonedata = Object.assign({}, data);
    console.log(clonedata);
    return Vue.axios.put('/api/rentalpayment/' + clonedata.uid, clonedata);
  },


  delete(data) {
    PassportServices.AuthenticationServices.setHeader();
    const clonedata = Object.assign({}, data);
    console.log(clonedata);
    return Vue.axios.delete('/api/rentalpayment/' + data.uid);
  }
};

export default RentalPaymentServices;