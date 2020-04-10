import Vue from "vue";
import LaravelPassportServices from "../common/LaravelPassportServices";

const initialState = {
    user : {},
    token : null,
};

export const state = { ...initialState };

export const actions = {
    async login(context, payload) {
        const  data  = await LaravelPassportServices.AuthenticationServices.login(payload);
        if(data.status == 200){
            context.commit("setToken", data.data.access_token);
            LaravelPassportServices.saveToken(data.data.access_token);
        }
        return data;
    },
};

export const mutations = {
    setToken(state, token){
        state.token = token;
    },
    setUser(state, user){
        state.user = user;
    }
};

const getters = {
    article(state) {
        return state.article;
    },
    comments(state) {
        return state.comments;
    }
};

export default {
    state,
    actions,
    mutations,
    getters
};