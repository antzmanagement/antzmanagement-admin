import Vue from "vue";
import TenantServices from "../services/TenantServices";

// const initialState = {
// };

// export const state = { ...initialState };

export const actions = {
    async getTenants(context, payload) {
        const { data } = await TenantServices.index(payload);
        return data;
    },
    async filterTenants(context, payload) {
        const { data } = await TenantServices.filter(payload);
        return data;
    },
    async getTenant(context, payload) {
        const { data } = await TenantServices.show(payload);
        return data;
    },
    async createTenant(context, payload) {
        const { data } = await TenantServices.create(payload);
        return data;
    },
    async updateTenant(context, payload) {
        const { data } = await TenantServices.update(payload);
        return data;
    },
    async deleteTenant(context, payload) {
        const { data } = await TenantServices.delete(payload);
        return data;
    },
};

// export const mutations = {
// };

// const getters = {
//     article(state) {
//         return state.article;
//     },
//     comments(state) {
//         return state.comments;
//     }
// };

export default {
    // state,
    actions,
    // mutations,
    // getters
};