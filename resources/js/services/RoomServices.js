import Vue from "vue";
import PassportServices from "../common/LaravelPassportServices";

const RoomServices = {

  index(data) {
    PassportServices.AuthenticationServices.setHeader();

    const clonedata = Object.assign({}, data);
    console.log(clonedata);
    return Vue.axios.get('/api/room', {
      params: clonedata
    })
  },

  show(data) {
    PassportServices.AuthenticationServices.setHeader();
    return Vue.axios.get('/api/room/' + data.uid);
  },

  create(data) {

    //Init axios with oauth headers
    PassportServices.AuthenticationServices.setHeader();
    //The data is pass by reference, any modified data will reflected to front end view.
    //In practice way, we must clone it to prevent any error that caused by manipulation stage.
    const clonedata = Object.assign({}, data);
    console.log(clonedata);
    return Vue.axios.post('/api/room', clonedata)
  },

  update(data) {
    PassportServices.AuthenticationServices.setHeader();
    const clonedata = Object.assign({}, data);
    console.log(clonedata);
    return Vue.axios.put('/api/room/' + clonedata.uid, clonedata);
  },


  delete(data) {
    PassportServices.AuthenticationServices.setHeader();
    const clonedata = Object.assign({}, data);
    console.log(clonedata);
    return Vue.axios.delete('/api/room/' + data.uid);
  }
};

export default RoomServices;