import Vue from "vue";
import PassportServices from "../common/LaravelPassportServices";
import { commonConfig } from "../common/config";



const UserServices = {

    index(data) {
        PassportServices.AuthenticationServices.setHeader();
        const clonedata = Object.assign({}, data);
        
        return Vue.axios.get('/api/user', {
            params: clonedata
        })
    },

    show(data) {
        PassportServices.AuthenticationServices.setHeader();
        return Vue.axios.get('/api/user/' + data.uid);
    },

    create(data) {

        //Init axios with oauth headers
    PassportServices.AuthenticationServices.setHeader();
        //The data is pass by reference, any modified data will reflected to front end view.
        //In practice way, we must clone it to prevent any error that caused by manipulation stage.
        const clonedata = Object.assign({}, data);
        
        return Vue.axios.post('/api/user', clonedata)
    },

    update(data) {
        PassportServices.AuthenticationServices.setHeader();
        const clonedata = Object.assign({}, data);
        
        return Vue.axios.put('/api/user/' + clonedata.uid, clonedata);
    },


    delete(data) {
        PassportServices.AuthenticationServices.setHeader();
        const clonedata = Object.assign({}, data);
        
        return Vue.axios.delete('/api/user/' + data.uid);
    },


    changePassword(data) {
        PassportServices.AuthenticationServices.setHeader();
        const clonedata = Object.assign({}, data);
        
        return Vue.axios.post('/api/user/' + clonedata.uid + '/changepassword', clonedata);
    },

    checkPassword(data) {
        PassportServices.AuthenticationServices.setHeader();
        const clonedata = Object.assign({}, data);
        return Vue.axios.post('/api/user/' + clonedata.uid + '/checkpassword', clonedata);
    },
};

export default UserServices;