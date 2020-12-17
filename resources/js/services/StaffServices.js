import Vue from "vue";
import PassportServices from "../common/LaravelPassportServices";

const StaffServices = {

  index(data) {
    PassportServices.AuthenticationServices.setHeader();

    const clonedata = Object.assign({}, data);

    return Vue.axios.get('/api/staff', {
      params: clonedata
    })
  },

  filter(data) {
    PassportServices.AuthenticationServices.setHeader();

    const clonedata = Object.assign({}, data);

    return Vue.axios.get('/api/filter/staff', {
      params: clonedata
    })
  },

  show(data) {
    PassportServices.AuthenticationServices.setHeader();
    return Vue.axios.get('/api/staff/' + data.uid);
  },

  create(data) {

    //Init axios with oauth headers
    PassportServices.AuthenticationServices.setHeader();
    //The data is pass by reference, any modified data will reflected to front end view.
    //In practice way, we must clone it to prevent any error that caused by manipulation stage.
    const config = {
      headers: { 'content-type': 'multipart/form-data' }
    }
    console.log(data);
    return Vue.axios.post('/api/staff', data, config)
  },

  update(data) {
    PassportServices.AuthenticationServices.setHeader();
    const config = {
      headers: { 'content-type': 'multipart/form-data' }
    }
    let formData = new FormData();
    formData.append("name", data.name);
    formData.append("icno", data.icno);
    formData.append("tel1", data.tel1);
    formData.append("email", data.email);
    formData.append("role_id", data.role_id);
    formData.append("img", data.img);
    formData.append("_method", 'put');
    console.log(formData);
    return Vue.axios.post('/api/staff/' + data.uid, formData);
  },


  delete(data) {
    PassportServices.AuthenticationServices.setHeader();
    const clonedata = Object.assign({}, data);

    return Vue.axios.delete('/api/staff/' + data.uid);
  }
};

export default StaffServices;