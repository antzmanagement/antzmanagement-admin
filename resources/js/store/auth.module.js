import Vue from "vue";
import LaravelPassportServices from "../common/LaravelPassportServices";

const initialState = {
    user: {},
    role: {},
    userModules: [],
    userNavBarItems : [],
    navBarItems: [
        {
            icon: "mdi-home-outline",
            text: "Home",
            name: "home"
        },
        {
            icon: "mdi-view-dashboard-outline",
            text: "Dashboard",
            name: "management"
        },
        {
            icon: "mdi-account-multiple",
            text: "User",
            name: "usermanagement"
        },
        {
            icon: "mdi-home-city-outline",
            text: "Room",
            name: "rooms",
            modulename: "room"
        },
        {
            icon: "mdi-screwdriver",
            text: "Room Maintenance",
            name: "maintenances",
            modulename: "maintenance"
        },
        {
            icon: "mdi-chair-rolling",
            text: "Property",
            name: "properties",
            modulename: "property"
        },
        {
            icon: "mdi-washing-machine",
            text: "Service",
            name: "services",
            modulename: "service"
        },
        {
            icon: "mdi-file-document-edit-outline",
            text: "Contract",
            name: "contracts",
            modulename: "contract"
        }
    ],
    token: null,
};

export const state = { ...initialState };

export const actions = {
    async login(context, payload) {
        const data = await LaravelPassportServices.AuthenticationServices.login(payload);
        if (data.status == 200) {
            context.commit("setToken", data.data.access_token);
            LaravelPassportServices.saveToken(data.data.access_token);
        }
        return data;
    },

    async authentication(context) {
        const resp = await LaravelPassportServices.AuthenticationServices.authenticate();
        if (resp.status == 200) {
            context.commit("setUser", resp.data.data);
            context.commit("setRole", resp.data.data.role);
            context.commit("setModules", resp.data.data.role.modules);
            context.commit("setUserNavBarItems");
        }
        return resp;
    },
    

};

export const mutations = {
    setToken(state, token) {
        state.token = token;
    },
    setUser(state, data) {
        state.user = Object.assign({}, data);
    },
    setRole(state, data) {
        state.role = Object.assign({}, data);
    },
    setModules(state, data) {
        state.userModules = new Array(data);
    },
    setUserNavBarItems(state) {
        state.userNavBarItems = state.navBarItems.filter(function (item) {
            var result = state.userModules.find(function (module) {
                return module.name == item.modulename;
            });

            if (result) {
                return true;
            } else {
                return false;
            }
        })
    }
};

const getters = {
    authUser(state) {
        return state.user;
    },
    userRole(state) {
        return state.role;
    },
    userNavBarItems(state) {
        return state.userNavBarItems;
    },
    userModules(state) {
        return state.userModules;
    }
};

export default {
    state,
    actions,
    mutations,
    getters
};