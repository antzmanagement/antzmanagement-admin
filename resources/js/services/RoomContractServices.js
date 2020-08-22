import Vue from "vue";
import PassportServices from "../common/LaravelPassportServices";

const RoomContractServices = {

  index(data) {
    PassportServices.AuthenticationServices.setHeader();

    const clonedata = Object.assign({}, data);
    console.log(clonedata);
    return Vue.axios.get('/api/roomcontract', {
      params: clonedata
    })
  },

  filter(data) {
    PassportServices.AuthenticationServices.setHeader();

    const clonedata = Object.assign({}, data);
    console.log(clonedata);
    return Vue.axios.get('/api/filter/roomcontract', {
      params: clonedata
    })
  },
  show(data) {
    PassportServices.AuthenticationServices.setHeader();
    return Vue.axios.get('/api/roomcontract/' + data.uid);
  },

  create(data) {

    //Init axios with oauth headers
    PassportServices.AuthenticationServices.setHeader();
    //The data is pass by reference, any modified data will reflected to front end view.
    //In practice way, we must clone it to prevent any error that caused by manipulation stage.
    const clonedata = Object.assign({}, data);
    console.log(clonedata);
    return Vue.axios.post('/api/roomcontract', clonedata)
  },

  update(data) {
    PassportServices.AuthenticationServices.setHeader();
    const clonedata = Object.assign({}, data);
    console.log(clonedata);
    return Vue.axios.put('/api/roomcontract/' + clonedata.uid, clonedata);
  },


  delete(data) {
    PassportServices.AuthenticationServices.setHeader();
    const clonedata = Object.assign({}, data);
    console.log(clonedata);
    return Vue.axios.delete('/api/roomcontract/' + data.uid);
  },

  
  transferRoomContract(data) {
    PassportServices.AuthenticationServices.setHeader();
    const clonedata = Object.assign({}, data);
    console.log(clonedata);
    return Vue.axios.post('/api/transfer/roomcontract', clonedata);
  },

  
};

export default RoomContractServices;