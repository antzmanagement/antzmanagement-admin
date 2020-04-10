import Vue from "vue";
import PassportServices from "../common/LaravelPassportServices";
import { commonConfig } from "../common/config";



const UserServices = {

    init() {

        Vue.axios.defaults.baseURL = commonConfig.API_URL;
    },
    setHeader() {
        Vue.axios.defaults.headers.common.Authorization = `Bearer ${PassportServices.getToken()}`;
    },
    index(data) {

        this.init();
        this.setHeader();
        const clonedata = Object.assign({}, data);
        console.log(clonedata);
        return Vue.axios.get('/api/user', {
            params: clonedata
        })
    },

    show(data) {
        this.init();
        this.setHeader();
        return Vue.axios.get('/api/user/' + data.uid);
    },

    create(data) {

        //Init axios with oauth headers
        this.init();
        this.setHeader();
        //The data is pass by reference, any modified data will reflected to front end view.
        //In practice way, we must clone it to prevent any error that caused by manipulation stage.
        const clonedata = Object.assign({}, data);
        console.log(clonedata);
        return Vue.axios.post('/api/user', clonedata)
    },

    update(data) {
        this.init();
        this.setHeader();
        const clonedata = Object.assign({}, data);
        console.log(clonedata);
        return Vue.axios.put('/api/user/' + clonedata.uid, clonedata);
    },


    delete(data) {
        this.init();
        this.setHeader();
        const clonedata = Object.assign({}, data);
        console.log(clonedata);
        return Vue.axios.delete('/api/user/' + data.uid);
    },


    changePassword(data) {
        this.init();
        this.setHeader();
        const clonedata = Object.assign({}, data);
        console.log(clonedata);
        return Vue.axios.post('/api/user/' + clonedata.uid + '/changepassword', clonedata);
    },

    checkPassword(data) {
        this.init();
        this.setHeader();
        const clonedata = Object.assign({}, data);
        return Vue.axios.post('/api/user/' + clonedata.uid + '/checkpassword', clonedata);
    },
};

export default UserServices;